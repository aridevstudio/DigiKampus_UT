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
    // controller show login
    public function showLoginForm() {
        return view ('Auth.mahasiswa.login');
    }

    // controller show forgot password
    public function showForgotPasswordForm() {
        return view ('Auth.mahasiswa.forgot-password');
    }

    // controller show verify otp
    public function showVerifyOtpForm() {
        // Check if email is in session
        if (!session('reset_email')) {
            return redirect()->route('mahasiswa.forgot-password')
                ->with('error', 'Silakan masukkan email terlebih dahulu.');
        }
        return view ('Auth.mahasiswa.verify-otp');
    }

    // controller show reset password
    public function showResetPasswordForm() {
        // Check if verification token is in session
        if (!session('reset_token') || !session('reset_email')) {
            return redirect()->route('mahasiswa.forgot-password')
                ->with('error', 'Silakan verifikasi OTP terlebih dahulu.');
        }
        return view ('Auth.mahasiswa.reset-password');
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

    // controller post login
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
        if (!Hash::check($validated['password'], $user->password)) {
                return back()
                    ->withInput()
                    ->with('alert', 'Password salah. Silakan coba lagi.');
        }

        if ($user && Hash::check($validated['password'], $user->password)) {
             Auth::guard('mahasiswa')->login($user);
            return redirect()->route('mahasiswa.dashboard');
        }
        return back()->withErrors(['mahasiswa.login' => 'NIS atau password salah.']);
    }

    // controller show dashboard
    public function showDashboard() {
        return view ('pages.mahasiswa.dashboard');
    }

    // controller show calendar
    public function showCalendar() {
        return view ('pages.mahasiswa.calendar');
    }

    // controller show profile
    public function showProfile() {
        return view ('pages.mahasiswa.profile');
    }

    // controller show notification
    public function showNotification() {
        return view ('pages.mahasiswa.notification');
    }

    // controller show edit profile form
    public function showEditProfile() {
        return view ('pages.mahasiswa.edit-profile');
    }

    // controller show get courses
    public function showGetCourses() {
        return view ('pages.mahasiswa.get-courses');
    }

    // controller show course detail
    public function showCourseDetail($id) {
        return view ('pages.mahasiswa.course-detail', ['id' => $id]);
    }

    // controller show checkout
    public function showCheckout() {
        return view ('pages.mahasiswa.checkout');
    }

    // controller show payment
    public function showPayment() {
        return view ('pages.mahasiswa.payment');
    }

    // controller show payment success
    public function showPaymentSuccess() {
        return view ('pages.mahasiswa.payment-success');
    }

    // controller update profile
    public function updateProfile(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/'],
        ], [
            'password.regex' => 'Kata sandi harus mengandung huruf besar dan huruf kecil.',
        ]);

        $user = Auth::guard('mahasiswa')->user();
        
        // Update user name
        $user->name = $request->name;
        
        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        // Update or create profile
        $profileData = [
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'visibilitas' => $request->has('visibilitas') ? 1 : 0,
        ];

        // Handle foto upload
        if ($request->hasFile('foto_profile')) {
            $file = $request->file('foto_profile');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profiles', $filename, 'public');
            $profileData['foto_profile'] = $path;
        }

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $profileData['user_id'] = $user->id;
            \App\Models\Profile::create($profileData);
        }

        return redirect()->route('mahasiswa.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    // controller logout account
    public function logout(Logout $request) {
          $request->logout();
          return redirect()->route('mahasiswa.login');
    }
}
