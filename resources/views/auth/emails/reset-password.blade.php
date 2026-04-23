<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: 'Inter', Arial, sans-serif; background: #f4f6f0; margin: 0; padding: 40px 20px; }
  .card { background: #fff; border-radius: 16px; max-width: 520px; margin: 0 auto; padding: 40px 36px; border: 1px solid #e8ede3; }
  .logo-row { text-align: center; margin-bottom: 24px; }
  .logo-row img { width: 60px; height: 60px; border-radius: 14px; }
  h2 { color: #1a3a1a; font-size: 1.2rem; font-weight: 800; text-align: center; margin: 0 0 8px; }
  p { color: #4b5563; font-size: 0.9rem; line-height: 1.7; margin: 0 0 16px; }
  .btn { display: block; width: fit-content; margin: 24px auto; background: linear-gradient(135deg, #1a5c38, #2d7a52); color: #fff; text-decoration: none; padding: 14px 36px; border-radius: 12px; font-weight: 700; font-size: 0.92rem; letter-spacing: 0.03em; }
  .note { font-size: 0.78rem; color: #9ca3af; text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f4eb; }
  .url-box { background: #f4f6f0; border-radius: 8px; padding: 10px 14px; font-size: 0.75rem; color: #6b7280; word-break: break-all; margin-top: 8px; }
</style>
</head>
<body>
  <div class="card">
    <div class="logo-row">
      <div style="width:60px;height:60px;border-radius:14px;background:linear-gradient(135deg,#1a5c38,#2d7a52);display:flex;align-items:center;justify-content:center;margin:0 auto;">
        <svg width="30" height="30" fill="none" stroke="white" stroke-width="1.8" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
      </div>
    </div>
    <h2>Password Reset Request</h2>
    <p style="text-align:center;color:#6b7280;font-size:0.82rem;margin-bottom:24px;">MCC Fingerprint Voting System</p>

    <p>Hello, <strong>{{ $firstName }}</strong>,</p>
    <p>We received a request to reset the password for your account. Click the button below to set a new password. This link will expire in <strong>60 minutes</strong>.</p>

    <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>

    <p style="font-size:0.82rem;color:#6b7280;">If the button doesn't work, copy and paste this link into your browser:</p>
    <div class="url-box">{{ $resetUrl }}</div>

    <p class="note">If you did not request a password reset, you can safely ignore this email. Your password will not be changed.<br><br>© {{ date('Y') }} Mandaue City College. All rights reserved.</p>
  </div>
</body>
</html>
