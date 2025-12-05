<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password OTP - DigiKampus UT</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            line-height: 1.6;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .header-logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .header-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message {
            color: #4a5568;
            font-size: 15px;
            margin-bottom: 30px;
        }

        .otp-container {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .otp-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .otp-code {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 12px;
            font-family: 'Courier New', monospace;
            text-shadow: 2px 2px 4px rgba(102, 126, 234, 0.2);
        }

        .otp-timer {
            margin-top: 15px;
            font-size: 13px;
            color: #e53e3e;
            font-weight: 600;
        }

        .info-box {
            background: #fef5e7;
            border-left: 4px solid #f39c12;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .info-box p {
            color: #7c5e3b;
            font-size: 14px;
            margin: 0;
        }

        .security-notice {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .security-notice-title {
            color: #0369a1;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .security-notice p {
            color: #075985;
            font-size: 13px;
            margin: 0;
        }

        .footer {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer-text {
            color: #a0aec0;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .footer-copyright {
            color: #cbd5e0;
            font-size: 12px;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            margin: 30px 0;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding: 20px 10px;
            }

            .content {
                padding: 30px 20px;
            }

            .otp-code {
                font-size: 36px;
                letter-spacing: 8px;
            }

            .header-logo {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            <div class="header-logo">🎓 DigiKampus UT</div>
            <div class="header-subtitle">Universitas Terbuka - Digital Campus</div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Halo, Mahasiswa DigiKampus! 👋</div>

            <p class="message">
                Kami menerima permintaan untuk <strong>mereset password</strong> akun Anda.
                Gunakan kode OTP di bawah ini untuk melanjutkan proses reset password:
            </p>

            <!-- OTP Box -->
            <div class="otp-container">
                <div class="otp-label">Kode Verifikasi Anda</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-timer">⏱️ Berlaku selama 5 menit</div>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p>
                    <strong>⚠️ Penting:</strong> Kode ini akan kadaluarsa dalam <strong>5 menit</strong>.
                    Jangan bagikan kode ini kepada siapapun, termasuk staff DigiKampus UT.
                </p>
            </div>

            <div class="divider"></div>

            <!-- Security Notice -->
            <div class="security-notice">
                <div class="security-notice-title">🔒 Catatan Keamanan</div>
                <p>
                    Jika Anda <strong>tidak meminta</strong> reset password, abaikan email ini.
                    Akun Anda tetap aman dan tidak ada perubahan yang dilakukan.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                Email ini dikirim otomatis oleh sistem DigiKampus UT.<br>
                Mohon tidak membalas email ini.
            </p>
            <p class="footer-copyright">
                &copy; {{ date('Y') }} DigiKampus UT - Universitas Terbuka. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>