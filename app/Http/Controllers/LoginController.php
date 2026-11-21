<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UserController;

class LoginController extends Controller
{
    public function login()
    {
        if (session()->has('user')) {
            $role = session('user.role');

            if ($role === 'validator') {
                return redirect()->route('validator.index');
            } elseif ($role === 'super_admin') {
                return redirect()->route('superadmin.index');
            } elseif ($role === 'data_entry') {
                return redirect()->route('pertanyaan.index');
            }
        }

        return view('auth.login');
        // supaya kalau habis login, gak bisa kembali ke halaman login, jadi nya kalo buak tab baru langsung ke buka sesuai dengan role nya yang sedang login
    }

    public function prosesLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = DB::table('users')
            ->where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Email atau password salah!');
        }

        if (!in_array($user->role, [
            'data_entry',
            'validator',
            'super_admin'
        ])) {
            return back()->with('error', 'Role tidak valid!');
        }

        if ($user->status !== 'aktif') {
        return back()->with(
            'error',
            'Akun Anda sedang nonaktif. Silakan hubungi Super Admin!'
        );
    }

        // Periksa dan simpan session agar satu akun hanya bisa login di satu browser    
        $berhasilLogin = DB::transaction(function () use ($request, $user) {

            $akun = DB::table('users')
                ->where('id', $user->id)
                ->lockForUpdate()
                ->first();

            if (
                $akun->session_id !== null &&
                $akun->session_id !== ''
            ) {
                return false;
            }

            $request->session()->regenerate();

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'session_id' => $request->session()->getId()
                ]);

            $request->session()->put('user', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]);

            return true;
        });

        if (!$berhasilLogin) {
            return back()
                ->with(
                    'error',
                    'Akun ini sudah login di browser lain. Silakan logout terlebih dahulu.'
                )
                ->withInput($request->only('email'));
        }

        if ($user->role === 'validator') {
            return redirect()->route('validator.index');
        }

        elseif ($user->role === 'super_admin') {
            return redirect()->route('superadmin.index');
        }
        elseif ($user->role === 'data_entry') {
            return redirect()->route('pertanyaan.index');
        };
    }

    public function logout(Request $request)
    {
        // Ambil ID pengguna dan session browser saat ini
        $userId = $request->session()->get('user.id');
        $sessionId = $request->session()->getId();

        // Kosongkan session_id hanya jika cocok dengan browser ini
        if ($userId) {
            DB::table('users')
                ->where('id', $userId)
                ->where('session_id', $sessionId)
                ->update([
                    'session_id' => null
                ]);
        }

        // Hapus session login
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
