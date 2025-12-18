<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class DosenController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('Auth.dosen.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'dosen')
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->with('alert', 'Email tidak terdaftar sebagai dosen.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withInput()
                ->with('alert', 'Password salah. Silakan coba lagi.');
        }

        if ($user->status !== 'aktif') {
            return back()
                ->withInput()
                ->with('alert', 'Akun Anda sedang tidak aktif. Hubungi admin.');
        }

        // Login using Laravel Auth guard
        Auth::guard('dosen')->login($user, $request->boolean('remember'));

        return redirect()->route('dosen.dashboard')
            ->with('status', 'Login berhasil. Selamat datang!');
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())
                ->where('role', 'dosen')
                ->first();

            if (!$user) {
                return redirect()->route('dosen.login')
                    ->with('alert', 'Email tidak terdaftar sebagai dosen. Silakan hubungi admin.');
            }

            if ($user->status !== 'aktif') {
                return redirect()->route('dosen.login')
                    ->with('alert', 'Akun Anda sedang tidak aktif. Hubungi admin.');
            }

            // Update google_id if not set
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }

            Auth::guard('dosen')->login($user, true);

            return redirect()->route('dosen.dashboard')
                ->with('status', 'Login dengan Google berhasil. Selamat datang!');

        } catch (\Exception $e) {
            return redirect()->route('dosen.login')
                ->with('alert', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }

    /**
     * Show dashboard
     */
    public function showDashboard()
    {
        $dosen = Auth::guard('dosen')->user();

        return view('Auth.dosen.dashboard', [
            'dosen' => $dosen
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('dosen')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dosen.login')
            ->with('status', 'Logout berhasil.');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('Auth.dosen.forgot-password');
    }

    /**
     * Send OTP for password reset
     */
    public function sendForgotPasswordOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)
            ->where('role', 'dosen')
            ->first();

        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email tidak ditemukan sebagai dosen.']);
        }

        // Generate 4-digit OTP
        $otp = rand(1000, 9999);

        // Store OTP in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        // Send OTP via email
        try {
            Mail::to($email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi nanti.']);
        }

        // Store email in session for verification
        session(['dosen_reset_email' => $email]);

        return redirect()->route('dosen.verify-otp')
            ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtpForm()
    {
        if (!session('dosen_reset_email')) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Silakan masukkan email terlebih dahulu.');
        }

        return view('Auth.dosen.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:4'],
        ]);

        $email = session('dosen_reset_email');
        $otp = $request->otp;

        if (!$email) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Sesi telah berakhir. Silakan ulangi proses.');
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid. Silakan coba lagi.']);
        }

        // Check if OTP is expired (5 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(5)->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan minta kode baru.']);
        }

        // Verify OTP
        if (!Hash::check($otp, $record->token)) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Silakan periksa kembali.']);
        }

        // Generate verification token for reset password
        $verificationToken = Str::random(64);
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => Hash::make($verificationToken),
                'created_at' => now()
            ]);

        session(['dosen_reset_token' => $verificationToken]);

        return redirect()->route('dosen.reset-password')
            ->with('status', 'OTP valid. Silakan masukkan password baru.');
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm()
    {
        if (!session('dosen_reset_email') || !session('dosen_reset_token')) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Sesi telah berakhir. Silakan ulangi proses.');
        }

        return view('Auth.dosen.reset-password');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = session('dosen_reset_email');
        $token = session('dosen_reset_token');

        if (!$email || !$token) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Sesi telah berakhir. Silakan ulangi proses.');
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record || !Hash::check($token, $record->token)) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'Token tidak valid. Silakan ulangi proses.');
        }

        // Update user password
        $user = User::where('email', $email)
            ->where('role', 'dosen')
            ->first();

        if (!$user) {
            return redirect()->route('dosen.forgot-password')
                ->with('alert', 'User tidak ditemukan.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset token
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        // Clear session
        session()->forget(['dosen_reset_email', 'dosen_reset_token']);

        return redirect()->route('dosen.login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}
