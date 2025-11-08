<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MahasiswaRequest;
use App\Http\Requests\Mahasiswa\Logout;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    // controller show login
    public function showLoginForm() {
        return view ('Auth.mahasiswa.login');
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

    // controller logout account
    public function logout(Logout $request) {
          $request->logout();
          return redirect()->route('mahasiswa.login');
    }
}
