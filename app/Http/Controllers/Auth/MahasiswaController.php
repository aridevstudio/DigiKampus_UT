<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MahasiswaRequest;
use App\Http\Requests\Mahasiswa\Logout;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    // ============================================
    // LOGIN
    // ============================================
    
    /**
     * Show login form
     */
    public function showLoginForm() {
        return view ('Auth.mahasiswa.login');
    }

    /**
     * Handle login form submission
     */
    public function showLoginFormPost(MahasiswaRequest $request) {
        $validated = $request->validated();
        $user = User::whereHas('profile', function($q) use ($validated) {
            $q->where('nim', $validated['nim']);
        })->first();

        if (!$user) {
            return back()
                ->withInput()
                ->with('alert', 'NIM tidak ditemukan. Pastikan NIM Anda sudah terdaftar.');
        }
        
        // Check if user role is mahasiswa
        if ($user->role !== 'mahasiswa') {
            return back()
                ->withInput()
                ->with('alert', 'Akun ini bukan akun mahasiswa. Silakan login di portal yang sesuai.');
        }
        
        if (!Hash::check($validated['password'], $user->password)) {
                return back()
                    ->withInput()
                    ->with('alert', 'Password salah. Silakan coba lagi.');
        }

        if ($user && Hash::check($validated['password'], $user->password)) {
             Auth::guard('mahasiswa')->login($user);
             $user->setOnline(); // Set user online status
            return redirect()->route('mahasiswa.dashboard');
        }
        return back()->withErrors(['mahasiswa.login' => 'NIS atau password salah.']);
    }

    // ============================================
    // FORGOT PASSWORD & OTP
    // ============================================

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm() {
        return view ('Auth.mahasiswa.forgot-password');
    }

    /**
     * Send OTP to email for password reset
     */
    public function sendForgotPasswordOtp(Request $request) {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;

        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        // Generate OTP 4 digit
        $otp = rand(1000, 9999);

        // Save OTP to database (hashed)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => now()
            ]
        );

        // Send Email
        try {
            Mail::to($email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi nanti.']);
        }

        // Store email in session and redirect to verify OTP page
        session(['reset_email' => $email]);

        return redirect()->route('mahasiswa.verify-otp')
            ->with('status', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Show verify OTP form
     */
    public function showVerifyOtpForm() {
        // Check if email is in session
        if (!session('reset_email')) {
            return redirect()->route('mahasiswa.forgot-password')
                ->with('error', 'Silakan masukkan email terlebih dahulu.');
        }
        return view ('Auth.mahasiswa.verify-otp');
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request) {
        $request->validate([
            'otp' => ['required', 'string', 'size:4'],
        ]);

        $email = session('reset_email');
        $otp = $request->otp;

        if (!$email) {
            return redirect()->route('mahasiswa.forgot-password')
                ->with('error', 'Session expired. Silakan mulai ulang.');
        }

        // Get OTP record
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        // Check if record exists
        if (!$record) {
            return back()->withErrors(['otp' => 'Permintaan reset password tidak ditemukan.']);
        }

        // Check expiry (5 minutes)
        if (\Carbon\Carbon::parse($record->created_at)->addMinutes(5)->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP telah kadaluarsa. Silakan request ulang.']);
        }

        // Verify OTP hash
        if (!Hash::check($otp, $record->token)) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        // Generate verification token for next step
        $verificationToken = Str::random(64);

        // Update record with verification token (hashed)
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->update([
                'token' => Hash::make($verificationToken),
                'created_at' => now() // Reset expiry for this token
            ]);

        // Store token in session
        session(['reset_token' => $verificationToken]);

        return redirect()->route('mahasiswa.reset-password')
            ->with('status', 'OTP valid. Silakan masukkan password baru.');
    }

    // ============================================
    // RESET PASSWORD
    // ============================================

    /**
     * Show reset password form
     */
    public function showResetPasswordForm() {
        // Check if verification token is in session
        if (!session('reset_token') || !session('reset_email')) {
            return redirect()->route('mahasiswa.forgot-password')
                ->with('error', 'Silakan verifikasi OTP terlebih dahulu.');
        }
        return view ('Auth.mahasiswa.reset-password');
    }

    /**
     * Reset password with verification token
     */
    public function resetPassword(Request $request) {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = session('reset_email');
        $token = session('reset_token');

        if (!$email || !$token) {
            return redirect()->route('mahasiswa.forgot-password')
                ->with('error', 'Session expired. Silakan mulai ulang.');
        }

        // Get record
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return redirect()->route('mahasiswa.forgot-password')
                ->with('error', 'Request tidak valid.');
        }

        // Verify token
        if (!Hash::check($token, $record->token)) {
            return redirect()->route('mahasiswa.forgot-password')
                ->with('error', 'Token verifikasi tidak valid.');
        }

        // Update User Password
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token (One-time use)
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Clear session
        session()->forget(['reset_email', 'reset_token']);

        return redirect()->route('mahasiswa.login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru.');
    }

    // ============================================
    // LOGOUT
    // ============================================

    /**
     * Handle logout
     */
    public function logout(Logout $request) {
          $user = Auth::guard('mahasiswa')->user();
          if ($user) {
              $user->setOffline(); // Set user offline status
          }
          $request->logout();
          return redirect()->route('mahasiswa.login');
    }
}
