<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Responses\TokenResponse;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'        => ['required'],
            'device_name' => ['required'],
            'email'       => ['required', 'email', 'unique:users'],
            'password'    => ['required', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return new TokenResponse($user);
    }
}
