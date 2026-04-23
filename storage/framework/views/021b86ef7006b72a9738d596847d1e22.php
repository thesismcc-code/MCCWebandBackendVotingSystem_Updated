<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fingerprint Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; }

        /* ── Left panel ── */
        .left-panel {
            background: linear-gradient(160deg, #0a2218 0%, #1a5c38 45%, #2d7a52 100%);
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 44px 44px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(110,231,183,0.14) 0%, transparent 70%);
            top: -120px; left: -80px;
            pointer-events: none;
        }

        .particle {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.07);
            animation: drift linear infinite;
        }
        @keyframes drift {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-120px) scale(1); opacity: 0; }
        }

        @keyframes ring-pulse {
            0%   { transform: scale(1);   opacity: 0.55; }
            100% { transform: scale(1.65); opacity: 0; }
        }
        .rp  { animation: ring-pulse 2.4s ease-out infinite; }
        .rp2 { animation: ring-pulse 2.4s ease-out 0.8s infinite; }
        .rp3 { animation: ring-pulse 2.4s ease-out 1.6s infinite; }

        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.25} }
        .live-dot { animation: blink 1.8s ease-in-out infinite; }

        /* ── Right panel ── */
        .right-panel {
            background: #f4f6f0;
            position: relative; overflow: hidden;
        }
        .right-panel::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                radial-gradient(circle at 75% 15%, rgba(26,92,56,0.07) 0%, transparent 50%),
                radial-gradient(circle at 25% 85%, rgba(26,92,56,0.04) 0%, transparent 50%);
            pointer-events: none;
        }

        .login-card {
            background: linear-gradient(135deg, #ffffff, #fafbf9);
            border-radius: 24px;
            border: 1px solid #e8ede3;
            box-shadow: 0 4px 6px -2px rgba(26,92,56,0.05), 0 20px 60px -10px rgba(26,92,56,0.12);
        }

        .field {
            width: 100%; background: #f8faf7;
            border: 1.5px solid #d1ddd1; border-radius: 12px;
            padding: 0.82rem 1rem 0.82rem 2.85rem;
            font-size: 0.88rem; font-family: inherit; color: #1a3a1a; outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .field::placeholder { color: #9cb89c; }
        .field:focus {
            border-color: #1a5c38;
            box-shadow: 0 0 0 3.5px rgba(26,92,56,0.12);
            background: #fff;
        }
        .field.has-right { padding-right: 3rem; }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #1a5c38 0%, #2d7a52 100%);
            color: #fff; border: none; border-radius: 12px; padding: 0.9rem;
            font-size: 0.9rem; font-weight: 700; font-family: inherit; cursor: pointer;
            letter-spacing: 0.03em; transition: transform .18s, box-shadow .18s;
            position: relative; overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 55%);
        }
        .btn-primary:hover  { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(26,92,56,0.35); }
        .btn-primary:active { transform: translateY(0); box-shadow: none; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body>
<div style="display:flex;height:100vh;width:100vw;">

    
    <div class="left-panel" style="width:52%;display:flex;flex-direction:column;justify-content:space-between;padding:3rem 3.5rem;position:relative;z-index:0;">

        <div class="particle" style="width:6px;height:6px;left:14%;animation-duration:13s;animation-delay:0s;"></div>
        <div class="particle" style="width:4px;height:4px;left:34%;animation-duration:9s;animation-delay:2s;"></div>
        <div class="particle" style="width:8px;height:8px;left:62%;animation-duration:15s;animation-delay:4s;"></div>
        <div class="particle" style="width:5px;height:5px;left:80%;animation-duration:11s;animation-delay:1s;"></div>
        <div class="particle" style="width:3px;height:3px;left:50%;animation-duration:8s;animation-delay:6s;"></div>

        <div style="position:relative;z-index:2;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(8px);">
                    <img src="<?php echo e(asset('icons/logo_white_bg.png')); ?>" alt="MCC" style="width:36px;height:36px;object-fit:contain;border-radius:8px;">
                </div>
                <div>
                    <div style="color:#fff;font-size:0.82rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;opacity:0.95;">Mandaue City College</div>
                    <div style="color:rgba(255,255,255,0.5);font-size:0.7rem;font-weight:500;margin-top:1px;">Official Voting Platform</div>
                </div>
            </div>
        </div>

        <div style="position:relative;z-index:2;display:flex;flex-direction:column;align-items:flex-start;gap:2rem;">

            <div style="position:relative;width:120px;height:120px;display:flex;align-items:center;justify-content:center;">
                <div class="rp"  style="position:absolute;width:120px;height:120px;border-radius:50%;border:1.5px solid rgba(110,231,183,0.55);"></div>
                <div class="rp2" style="position:absolute;width:120px;height:120px;border-radius:50%;border:1.5px solid rgba(110,231,183,0.4);"></div>
                <div class="rp3" style="position:absolute;width:120px;height:120px;border-radius:50%;border:1.5px solid rgba(110,231,183,0.28);"></div>
                <div style="width:72px;height:72px;border-radius:50%;background:rgba(110,231,183,0.12);border:1.5px solid rgba(110,231,183,0.35);display:flex;align-items:center;justify-content:center;">
                    <svg width="32" height="32" fill="none" stroke="rgba(167,243,208,1)" stroke-width="1.6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                    </svg>
                </div>
            </div>

            <div>
                <div style="display:inline-flex;align-items:center;gap:7px;background:rgba(110,231,183,0.12);border:1px solid rgba(110,231,183,0.25);border-radius:999px;padding:5px 14px;margin-bottom:1.2rem;">
                    <span class="live-dot" style="width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;"></span>
                    <span style="color:#a7f3d0;font-size:0.7rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;">System Online</span>
                </div>
                <h1 style="color:#fff;font-size:2.6rem;font-weight:900;line-height:1.12;letter-spacing:-0.03em;margin-bottom:1rem;">
                    Secure.<br>
                    <span style="color:#6ee7b7;">Biometric.</span><br>
                    Voting.
                </h1>
                <p style="color:rgba(255,255,255,0.55);font-size:0.9rem;font-weight:400;line-height:1.7;max-width:320px;">
                    A fingerprint-authenticated election platform ensuring every vote is verified, tamper-proof, and counted accurately.
                </p>
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:10px;">
                <div style="display:flex;align-items:center;gap:7px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-radius:10px;padding:8px 14px;">
                    <svg width="14" height="14" fill="none" stroke="#4ade80" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span style="color:rgba(255,255,255,0.8);font-size:0.75rem;font-weight:600;">Tamper-Proof</span>
                </div>
                <div style="display:flex;align-items:center;gap:7px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-radius:10px;padding:8px 14px;">
                    <svg width="14" height="14" fill="none" stroke="#6ee7b7" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span style="color:rgba(255,255,255,0.8);font-size:0.75rem;font-weight:600;">Real-Time Results</span>
                </div>
                <div style="display:flex;align-items:center;gap:7px;background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-radius:10px;padding:8px 14px;">
                    <svg width="14" height="14" fill="none" stroke="#a7f3d0" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span style="color:rgba(255,255,255,0.8);font-size:0.75rem;font-weight:600;">Encrypted</span>
                </div>
            </div>
        </div>

        <div style="position:relative;z-index:2;display:flex;gap:2.5rem;padding-top:2rem;border-top:1px solid rgba(255,255,255,0.1);">
            <div>
                <div style="color:#fff;font-size:1.5rem;font-weight:800;">100%</div>
                <div style="color:rgba(255,255,255,0.4);font-size:0.68rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-top:2px;">Accuracy</div>
            </div>
            <div>
                <div style="color:#fff;font-size:1.5rem;font-weight:800;">1-Vote</div>
                <div style="color:rgba(255,255,255,0.4);font-size:0.68rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-top:2px;">Per Student</div>
            </div>
            <div>
                <div style="color:#fff;font-size:1.5rem;font-weight:800;">Live</div>
                <div style="color:rgba(255,255,255,0.4);font-size:0.68rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;margin-top:2px;">Monitoring</div>
            </div>
        </div>
    </div>

    
    <div class="right-panel" style="width:48%;display:flex;align-items:center;justify-content:center;padding:2rem;">
        <div class="login-card" style="width:100%;max-width:420px;padding:2.8rem 2.6rem;position:relative;z-index:2;" x-data="{ show: false }">

            <div style="text-align:center;margin-bottom:1.8rem;">
                <div style="display:inline-flex;align-items:center;justify-content:center;width:68px;height:68px;border-radius:18px;background:linear-gradient(135deg,#1a5c38,#2d7a52);box-shadow:0 8px 24px rgba(26,92,56,0.3);margin-bottom:1.1rem;">
                    <img src="<?php echo e(asset('icons/logo_white_bg.png')); ?>" alt="MCC" style="width:50px;height:50px;object-fit:contain;border-radius:10px;">
                </div>
                <h2 style="color:#1a3a1a;font-size:1.22rem;font-weight:800;letter-spacing:-0.01em;">Fingerprint Voting System</h2>
                <p style="color:#8fb08f;font-size:0.78rem;font-weight:500;margin-top:4px;">Mandaue City College</p>
            </div>

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.6rem;">
                <div style="flex:1;height:1px;background:#e8ede3;"></div>
                <span style="font-size:0.7rem;font-weight:700;color:#8fb08f;letter-spacing:0.08em;text-transform:uppercase;">Sign in to continue</span>
                <div style="flex:1;height:1px;background:#e8ede3;"></div>
            </div>

            <?php if(session('status')): ?>
                <div style="display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:12px 16px;margin-bottom:1.2rem;">
                    <svg width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span style="color:#15803d;font-size:0.82rem;font-weight:600;"><?php echo e(session('status')); ?></span>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div style="display:flex;align-items:center;gap:10px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:12px 16px;margin-bottom:1.2rem;">
                    <svg width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span style="color:#b91c1c;font-size:0.82rem;font-weight:600;"><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login.post')); ?>">
                <?php echo csrf_field(); ?>

                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-size:0.72rem;font-weight:700;color:#4a6a4a;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:6px;">Email Address</label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9cb89c;pointer-events:none;display:flex;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="text" name="email" placeholder="your@email.com"
                               value="<?php echo e(old('email')); ?>" class="field">
                    </div>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color:#dc2626;font-size:0.75rem;font-weight:500;margin-top:4px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div style="margin-bottom:1.6rem;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <label style="font-size:0.72rem;font-weight:700;color:#4a6a4a;text-transform:uppercase;letter-spacing:0.07em;">Password</label>
                        <a href="<?php echo e(route('password.request')); ?>"
                           style="font-size:0.72rem;font-weight:700;color:#1a5c38;text-decoration:none;position:relative;z-index:20;cursor:pointer;">Forgot password?</a>
                    </div>
                    <div style="position:relative;">
                        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9cb89c;pointer-events:none;display:flex;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input :type="show ? 'text' : 'password'" name="password"
                               placeholder="Enter your password" class="field has-right">
                        <button type="button" @click="show = !show"
                                style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9cb89c;display:flex;padding:0;transition:color .2s;"
                                onmouseover="this.style.color='#4a6a4a'" onmouseout="this.style.color='#9cb89c'">
                            <svg x-show="!show" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                            <svg x-show="show" x-cloak width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color:#dc2626;font-size:0.75rem;font-weight:500;margin-top:4px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="btn-primary">
                    <span style="position:relative;z-index:1;display:flex;align-items:center;justify-content:center;gap:8px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Log In
                    </span>
                </button>
            </form>

            <div style="margin-top:2rem;padding-top:1.4rem;border-top:1px solid #f0f4eb;text-align:center;">
                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:5px;">
                    <svg width="12" height="12" fill="none" stroke="#8fb08f" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span style="font-size:0.7rem;color:#8fb08f;font-weight:500;">Secured with end-to-end encryption</span>
                </div>
                <p style="font-size:0.68rem;color:#b8d0b8;font-weight:500;letter-spacing:0.04em;">© <?php echo e(date('Y')); ?> Mandaue City College. All rights reserved.</p>
            </div>

        </div>
    </div>

</div>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/index.blade.php ENDPATH**/ ?>