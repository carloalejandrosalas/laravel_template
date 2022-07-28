<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = User::where('email', $credentials['email'])->first();
            return [
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
                'exp' => strtotime('+1 day'),
            ];
        }

        return response([
            'email' => 'The provided credentials do not match our records.',
            'code' => 'bad/credentials'
        ], 401);
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:App\Models\User,email',
            'password' => 'required',
            'username' => 'required|string|unique:App\Models\User,username',
            'role_id' => 'required|exists:App\Models\Role,id'
        ]);

        $credentials['password'] = Hash::make($credentials['password']);

        $user = User::create($credentials);

        return $user;
    }
}
