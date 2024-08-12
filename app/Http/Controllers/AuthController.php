<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $registerdData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create($registerdData);

        $accessToken = $user->createToken('registration_token')->accessToken;

        return response()->json([
            'user' => $user, 'access_token' => $accessToken], 201);

    }
    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);


        if($validator->fails()){
            return response()->json([
                'ok' => false,
                'message' => 'Request did not pass the validation.',
                'errors' => $validator->errors()
            ], 400);
        }

        if(Auth::attempt($validator->validated())){
            $user = Auth::user();
            $token = $user->createToken('LaravelPassportToken')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        }else {
            return response()->json([
                'error' => "Unauthorized"
            ], 401);
        }
    }

    public function logout()
    {
        Auth::user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
