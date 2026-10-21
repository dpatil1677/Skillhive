<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password — SkillHive</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 40px 15px;
            color: #1e293b;
        }
        .email-container {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background: #6D28D9;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .email-header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .email-body {
            padding: 36px 30px;
            font-size: 15px;
            line-height: 1.6;
        }
        .btn-reset {
            display: inline-block;
            background-color: #6D28D9;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin: 25px 0;
            text-align: center;
        }
        .note-box {
            background-color: #f8fafc;
            border-left: 4px solid #6D28D9;
            padding: 12px 16px;
            margin: 20px 0;
            font-size: 13px;
            color: #475569;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .break-url {
            word-break: break-all;
            color: #6D28D9;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        {{-- Header --}}
        <div class="email-header">
            <h1>SkillHive</h1>
            <p>Student Freelance Marketplace &amp; Escrow</p>
        </div>

        {{-- Body --}}
        <div class="email-body">
            <h2 style="font-size: 18px; margin-top: 0; color: #1e293b;">Password Reset Request</h2>
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            <p>You are receiving this email because we received a password reset request for your SkillHive account.</p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn-reset" target="_blank">Reset Password</a>
            </div>

            <div class="note-box">
                <strong>Important:</strong> This password reset link will expire in <strong>{{ $expire }} minutes</strong>.
            </div>

            <p style="font-size: 13px; color: #64748b;">
                If you did not request a password reset, no further action is required and your account remains safe and secure.
            </p>

            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 25px 0;">

            <p style="font-size: 12px; color: #64748b; margin-bottom: 5px;">
                Having trouble with the button? Copy and paste the URL below into your web browser:
            </p>
            <a href="{{ $resetUrl }}" class="break-url">{{ $resetUrl }}</a>
        </div>

        {{-- Footer --}}
        <div class="email-footer">
            <p style="margin: 0 0 6px 0;">&copy; {{ date('Y') }} SkillHive. All rights reserved.</p>
            <p style="margin: 0;">Connect with verified student talent across campuses.</p>
        </div>
    </div>
</body>
</html>
