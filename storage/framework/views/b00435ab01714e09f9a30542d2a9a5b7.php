<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification — MCC Voting System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body, html { height: 100vh; width: 100%; background: white; }

        .wrapper { display: flex; height: 100%; }

        /* Left panel */
        .panel-brand {
            flex: 1;
            background: radial-gradient(ellipse at top left, #0d3520 0%, #0a2e1a 50%, #071f12 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 2rem; text-align: center; color: white;
            position: relative; overflow: hidden;
        }
        .panel-brand::before {
            content: ''; position: absolute; top: -60px; right: -60px;
            width: 240px; height: 240px; border-radius: 50%;
            background: rgba(255,255,255,.04);
        }
        .panel-brand::after {
            content: ''; position: absolute; bottom: -40px; left: -40px;
            width: 180px; height: 180px; border-radius: 50%;
            background: rgba(255,255,255,.03);
        }
        .panel-brand img { width: 90px; height: 90px; border-radius: 22px; margin-bottom: 24px; box-shadow: 0 8px 32px rgba(0,0,0,.3); position: relative; z-index: 1; }
        .panel-brand h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.03em; position: relative; z-index: 1; }
        .panel-brand p { font-size: 14px; color: rgba(255,255,255,.55); margin-top: 8px; font-weight: 500; position: relative; z-index: 1; }

        /* Right panel */
        .panel-form {
            flex: 1; display: flex; align-items: center;
            justify-content: center; padding: 2rem; background: white;
        }
        .form-container {
            width: 100%; max-width: 400px;
            display: flex; flex-direction: column;
            align-items: center; text-align: center;
        }

        /* Icon */
        .verify-icon {
            width: 64px; height: 64px; border-radius: 18px;
            background: linear-gradient(135deg, #e8f5ee, #dcfce7);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }

        .form-container h2 { font-size: 26px; font-weight: 800; color: #1a3a1a; margin-bottom: 8px; letter-spacing: -0.02em; }
        .subtitle { font-size: 14px; color: #6b7280; margin-bottom: 28px; line-height: 1.6; font-weight: 500; }
        .subtitle strong { display: block; color: #1a3a1a; font-weight: 700; margin-top: 4px; }

        /* OTP inputs */
        .input-group { display: flex; gap: 12px; margin-bottom: 24px; }
        .code-input {
            width: 64px; height: 64px;
            background: #f4f6f0;
            border: 2px solid #e8ede3;
            border-radius: 14px;
            font-size: 28px; font-weight: 800;
            text-align: center; color: #1a3a1a;
            outline: none; transition: all .2s;
            caret-color: #1a5c38;
        }
        .code-input:focus { background: #e8f5ee; border-color: #1a5c38; box-shadow: 0 0 0 3px rgba(26,92,56,.1); }
        .code-input.error { border-color: #ef4444; background: #fef2f2; }

        /* Button */
        .btn-verify {
            width: 100%; padding: 14px 24px;
            background: linear-gradient(135deg, #1a5c38, #2d7a52);
            color: white; border: none; border-radius: 14px;
            font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all .2s;
            box-shadow: 0 4px 16px rgba(26,92,56,.3);
            margin-bottom: 20px;
        }
        .btn-verify:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(26,92,56,.4); }
        .btn-verify:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        /* Links */
        .support-links { font-size: 13px; color: #9ca3af; }
        .support-links a { color: #1a5c38; text-decoration: none; font-weight: 700; cursor: pointer; }
        .support-links a:hover { text-decoration: underline; }

        .error-msg   { color: #ef4444; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
        .success-msg { color: #15803d; font-size: 13px; font-weight: 600; margin-bottom: 16px; }

        @media(max-width: 900px) {
            .wrapper { flex-direction: column; }
            .panel-brand { flex: unset; height: auto; padding: 40px 20px; }
            .panel-brand img { width: 70px; height: 70px; }
            .panel-brand h1 { font-size: 22px; }
            .panel-form { padding-top: 40px; align-items: flex-start; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="panel-brand">
        <img src="<?php echo e(asset('icons/logo.png')); ?>" alt="MCC Logo">
        <h1>MCC Voting System</h1>
        <p>Mandaue City College<br>Digital Voting Platform</p>
    </div>

    <div class="panel-form">
        <div class="form-container">
            <div class="verify-icon">
                <svg width="30" height="30" fill="none" stroke="#1a5c38" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>

            <h2>Email Verification</h2>
            <p class="subtitle">
                We sent a 4-digit code to your registered email
                <strong><?php echo e($email); ?></strong>
            </p>

            <?php if($errors->has('otp')): ?>
                <p class="error-msg"><?php echo e($errors->first('otp')); ?></p>
            <?php endif; ?>
            <?php if(session('resent')): ?>
                <p class="success-msg">A new code has been sent to your email.</p>
            <?php endif; ?>

            <form id="otpForm" action="<?php echo e(route('student.verify-otp')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="otp" id="otpValue">
                <div class="input-group">
                    <input type="text" class="code-input" maxlength="1" autofocus autocomplete="off" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                    <input type="text" class="code-input" maxlength="1" autocomplete="off" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                    <input type="text" class="code-input" maxlength="1" autocomplete="off" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                    <input type="text" class="code-input" maxlength="1" autocomplete="off" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                </div>
                <button class="btn-verify" type="submit" id="verifyBtn">Verify Email</button>
            </form>

            <div class="support-links">
                <span>Didn't receive code? </span>
                <a id="resendLink" onclick="resendOtp()">Resend</a>
            </div>
        </div>
    </div>
</div>

<script>
    const inputs = document.querySelectorAll('.code-input');
    inputs.forEach((input, index) => {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) inputs[index - 1].focus();
        });
        input.addEventListener('input', () => {
            if (input.value && index < inputs.length - 1) inputs[index + 1].focus();
        });
    });
    document.getElementById('otpForm').addEventListener('submit', function(e) {
        const otp = Array.from(inputs).map(i => i.value).join('');
        if (otp.length < 4) {
            e.preventDefault();
            inputs.forEach(i => i.classList.add('error'));
            return;
        }
        document.getElementById('otpValue').value = otp;
    });
    function resendOtp() {
        const link = document.getElementById('resendLink');
        link.textContent = 'Sending...';
        link.style.pointerEvents = 'none';
        fetch('<?php echo e(route('student.resend-otp')); ?>', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Content-Type': 'application/json', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            link.textContent = 'Resend';
            link.style.pointerEvents = 'auto';
            if (data.success) {
                inputs.forEach(i => { i.value = ''; i.classList.remove('error'); });
                inputs[0].focus();
            }
        })
        .catch(() => { link.textContent = 'Resend'; link.style.pointerEvents = 'auto'; });
    }
</script>
</body>
</html>
<?php /**PATH C:\MCCWebandBackendVotingSystem\resources\views/student/verify.blade.php ENDPATH**/ ?>