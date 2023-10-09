<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login($req)
    {
        try {
            $user = User::email(request('email'))->first();

            if ($user) {
                // log user

                if (Auth::attempt($req->only('email', 'password'))) {

                    $token = $user->createToken('authToken')->plainTextToken;

                    return [
                        'token' => $token,
                        'user' => $user,
                    ];
                }
            }
        } catch (\Throwable $th) {
            info($th->getMessage());
        }


        return [];
    }

    public function register($req)
    {
        try {
            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => Hash::make($req->password),
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            return [
                'token' => $token,
                'user' => $user
            ];
        } catch (\Throwable $th) {
            info($th->getMessage());
        }

        return [];
    }

    public function logout()
    {
        try {
            $user = request()->user();
            $user->tokens()->delete();
        } catch (\Throwable $th) {
            info($th->getMessage());
            return false;
        }

        return true;
    }
}
