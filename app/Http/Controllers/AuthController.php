<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use \stdClass;


class AuthController extends Controller
{
    public function registerOrLogin(Request $request)
    {
        // Verifica si el usuario ya existe en la base de datos
        $user = User::where('email', $request->email)->first();

        if ($user) {

            if (Auth::attempt($request->only('email', 'password'))) {
                $expiration = now()->addMonths(3);
                $token = $user->createToken('auth_token', ['*'], $expiration)->plainTextToken;

                return response()->json([
                    'registered' => false,
                    'accessToken' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => $expiration->toDateTimeString(),
                ]);
            } else {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $expiration = now()->addMonths(3);
            $token = $newUser->createToken('auth_token', ['*'], $expiration)->plainTextToken;

            return response()->json([
                'registered' => true,
                'accessToken' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiration->toDateTimeString(),
            ]);
        }
    }




    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and tokens are deleted.'
        ];
    }
}
