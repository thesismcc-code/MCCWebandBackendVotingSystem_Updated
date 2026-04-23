from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import base64
import threading
import struct

app = FastAPI(title="ZKTeco Fingerprint Service")

app.add_middleware(CORSMiddleware, allow_origins=["*"], allow_methods=["*"], allow_headers=["*"])

try:
    from pyzkfp import ZKFP2
    SDK_AVAILABLE = True
except Exception:
    SDK_AVAILABLE = False

def raw_to_bmp(raw: bytes) -> bytes:
    """Convert ZKTeco raw grayscale pixel buffer to a valid BMP image."""
    pixel_count = len(raw)

    # ZK9500 known resolutions â€” try exact matches first
    known_sizes = [
        (300, 375),   # ZK9500 â€” confirmed 112500 bytes
        (296, 296),
        (300, 400),
        (256, 360),
        (288, 384),
        (320, 480),
        (400, 500),
    ]
    width, height = None, None
    for w, h in known_sizes:
        if w * h == pixel_count:
            width, height = w, h
            break

    # Fallback: derive dimensions from buffer size
    if width is None:
        import math
        # Assume roughly 3:4 portrait ratio
        height = int(math.sqrt(pixel_count * 4 / 3))
        width  = pixel_count // height if height > 0 else pixel_count
        # Trim to exact fit
        height = pixel_count // width if width > 0 else height

    print(f"[bmp] buffer={pixel_count} â†’ {width}x{height}")

    row_size        = (width + 3) & ~3
    pixel_data_size = row_size * height
    palette         = b''.join(bytes([i, i, i, 0]) for i in range(256))
    file_size       = 54 + 1024 + pixel_data_size
    offset          = 54 + 1024

    bmp_header = struct.pack('<2sIHHI', b'BM', file_size, 0, 0, offset)
    dib_header = struct.pack('<IiiHHIIiiII',
        40, width, -height, 1, 8,
        0, pixel_data_size, 2835, 2835, 256, 256)

    rows = b''
    for y in range(height):
        row = raw[y * width: y * width + width]
        if len(row) < width:
            row = row + b'\x00' * (width - len(row))
        rows += row + b'\x00' * (row_size - width)

    return bmp_header + dib_header + palette + rows


zkfp2 = None
device_open = False
lock = threading.Lock()


def try_init_device():
    """Try to initialize device on startup."""
    global zkfp2, device_open
    if not SDK_AVAILABLE:
        return
    try:
        zkfp2 = ZKFP2()
        zkfp2.Init()
        count = zkfp2.GetDeviceCount()
        if count > 0:
            zkfp2.OpenDevice(0)
            device_open = True
            print(f"[ZKTeco] Device auto-initialized. {count} device(s) found.")
        else:
            print("[ZKTeco] No device found on startup.")
    except Exception as e:
        print(f"[ZKTeco] Auto-init failed: {e}")


@app.on_event("startup")
def startup():
    try_init_device()


def ensure_device():
    if not device_open:
        raise HTTPException(status_code=503, detail="Device not initialized. Call /init first.")


# â”€â”€ Models â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

class TemplatePayload(BaseModel):
    template: str

class MatchPayload(BaseModel):
    template1: str
    template2: str

class RegisterPayload(BaseModel):
    finger_id: int
    templates: list[str]

class LoadPayload(BaseModel):
    members: list[dict]


# â”€â”€ Routes â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

@app.post("/init")
def init_device():
    global zkfp2, device_open
    with lock:
        try:
            # If already open and working, just return ok
            if device_open and zkfp2:
                try:
                    count = zkfp2.GetDeviceCount()
                    if count > 0:
                        return {"status": "ok", "device_count": count}
                except Exception:
                    pass  # Device disconnected, re-init below

            # Re-create ZKFP2 instance to handle reconnects cleanly
            zkfp2 = ZKFP2()
            zkfp2.Init()
            count = zkfp2.GetDeviceCount()
            if count == 0:
                device_open = False
                raise HTTPException(status_code=404, detail="No ZKTeco device found.")
            zkfp2.OpenDevice(0)
            device_open = True
            return {"status": "ok", "device_count": count}
        except HTTPException:
            raise
        except Exception as e:
            device_open = False
            raise HTTPException(status_code=500, detail=str(e))


@app.post("/terminate")
def terminate_device():
    global device_open
    with lock:
        try:
            if zkfp2:
                zkfp2.Terminate()
            device_open = False
            return {"status": "terminated"}
        except Exception as e:
            raise HTTPException(status_code=500, detail=str(e))


@app.get("/status")
def status():
    global device_open
    if not SDK_AVAILABLE:
        return {"initialized": False, "sdk_available": False}
    # Actively check device count to detect physical disconnection
    try:
        count = zkfp2.GetDeviceCount() if zkfp2 else 0
        if count == 0 and device_open:
            device_open = False  # device was unplugged
        return {"initialized": device_open and count > 0, "sdk_available": True}
    except Exception:
        device_open = False
        return {"initialized": False, "sdk_available": True}


@app.post("/capture")
def capture():
    ensure_device()
    with lock:
        try:
            result = zkfp2.AcquireFingerprint()
            if not result:
                return {"captured": False}
            tmp, img = result
            raw_img = bytes(img)
            # Log actual buffer size so we can tune dimensions
            print(f"[capture] img buffer size: {len(raw_img)} bytes, tmp size: {len(bytes(tmp))}")
            bmp = raw_to_bmp(raw_img)
            return {
                "captured": True,
                "template": base64.b64encode(bytes(tmp)).decode(),
                "image":    base64.b64encode(bmp).decode(),
            }
        except Exception as e:
            raise HTTPException(status_code=500, detail=str(e))


@app.post("/identify")
def identify(payload: TemplatePayload):
    ensure_device()
    with lock:
        try:
            tmp = bytes(base64.b64decode(payload.template))
            finger_id, score = zkfp2.DBIdentify(tmp)
            return {"finger_id": finger_id, "score": score, "matched": finger_id > 0}
        except Exception as e:
            raise HTTPException(status_code=500, detail=str(e))


@app.post("/match")
def match(payload: MatchPayload):
    ensure_device()
    with lock:
        try:
            t1 = bytes(base64.b64decode(payload.template1))
            t2 = bytes(base64.b64decode(payload.template2))
            result = zkfp2.DBMatch(t1, t2)
            return {"matched": bool(result)}
        except Exception as e:
            raise HTTPException(status_code=500, detail=str(e))


@app.post("/register")
def register(payload: RegisterPayload):
    ensure_device()
    with lock:
        try:
            templates = [bytes(base64.b64decode(t)) for t in payload.templates]
            if len(templates) != 3:
                raise HTTPException(status_code=400, detail="Exactly 3 templates required.")
            try:
                reg_temp, _ = zkfp2.DBMerge(*templates)
            except Exception:
                # DBMerge can fail if scans are too similar; fall back to the last template
                reg_temp = templates[2]
            reg_bytes = bytes(reg_temp)
            # Remove existing entry first to avoid duplicate key errors
            try:
                zkfp2.DBDel(payload.finger_id)
            except Exception:
                pass
            zkfp2.DBAdd(payload.finger_id, reg_bytes)
            return {"finger_id": payload.finger_id, "template": base64.b64encode(reg_bytes).decode()}
        except HTTPException:
            raise
        except Exception as e:
            raise HTTPException(status_code=500, detail=str(e))


@app.post("/load-templates")
def load_templates(payload: LoadPayload):
    ensure_device()
    with lock:
        try:
            zkfp2.DBClear()  # Clear first to avoid duplicate key errors
            loaded = 0
            for member in payload.members:
                zkfp2.DBAdd(member["finger_id"], bytes(base64.b64decode(member["template"])))
                loaded += 1
            return {"loaded": loaded}
        except Exception as e:
            raise HTTPException(status_code=500, detail=str(e))

@app.delete("/db/clear")
def clear_db():
    ensure_device()
    with lock:
        zkfp2.DBClear()
        return {"status": "cleared"}


