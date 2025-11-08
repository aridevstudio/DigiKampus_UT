<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MahasiswaRequest;
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

        if ($user && Hash::check($validated['password'], $user->password)) {
             Auth::guard('mahasiswa')->login($user);
            return redirect()->route('mahasiswa.Dashboard');
        }
        return back()->withErrors(['mahasiswa.login' => 'NIS atau password salah.']);
    }
    // controller show dashboard
    public function showDashboard() {
        return view ('pages.mahasiswa.dashboard');
    }
}
