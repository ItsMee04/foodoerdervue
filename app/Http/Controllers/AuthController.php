<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'     =>  'required',
            'password'  =>  'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' =>  ['The provided credentials are incorrect'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('token')->plainTextToken;
        $user->token = $token;

        unset($user->email_verified_at);
        unset($user->created_at);
        unset($user->updated_at);
        unset($user->deleted_at);

        return response()->json(['success' => true, 'message' => 'Login Success', 'data' => $user]);
    }

    public function me()
    {
        return response()->json(['success' => true, 'data' => Auth::user()]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
