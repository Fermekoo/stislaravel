<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepo
{
    public function login($request)
    {
        $user = User::where('username', $request->username)->first();

        if(!$user) {
            throw new \Exception('User tidak terdaftar');
        }

        if(!$user->is_active) throw new \Exception('Akun anda telah di nonaktifkan');

        if(Hash::check($request->password, $user->password)) {
           return auth()->loginUsingId($user->id, $request->rememberMe);
        }

        throw new \Exception('username atau password salah');
    }

    public function changePassword($user_id, $request)
    {
        $user = User::findOrFail($user_id);

        if(Hash::check($request->passwordLama, $user->password)) {
            $user->password = Hash::make($request->passwordBaru);
            $user->save();

            return $user;
        }

        throw new \Exception('password lama salah');
    }
}