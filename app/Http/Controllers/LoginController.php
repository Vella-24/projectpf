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
        return view('auth.login');
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

        session([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);

        if ($user->role === 'validator') {
            return redirect()->route('validator.index');
        }

        elseif ($user->role === 'super_admin') {
            return redirect()->route('superadmin.index');
        }
        elseif ($user->role === 'data_entry') {
            return redirect()->route('pertanyaan.index');
        }



        
    }

    public function logout()
    {
        session()->flush();
        return redirect('/login');
    }
}
