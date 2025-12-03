<!DOCTYPE html>
<html>

<head>
    <title>Reset Password OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
        }

        .otp-box {
            background: #f7fafc;
            border: 2px dashed #cbd5e0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #4a5568;
        }

        .text {
            color: #718096;
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">DigiKampus UT</div>
        </div>

        <p class="text">Halo Mahasiswa,</p>
        <p class="text">Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode OTP berikut untuk
            melanjutkan proses reset password:</p>

        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <p class="text">Kode ini akan kadaluarsa dalam <strong>5 menit</strong>.</p>
        <p class="text">Jika Anda tidak meminta reset password, abaikan email ini. Akun Anda tetap aman.</p>

        <div class="footer">
            &copy; {{ date('Y') }} DigiKampus UT. All rights reserved.
        </div>
    </div>
</body>

</html>