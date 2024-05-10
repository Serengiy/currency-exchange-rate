<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
//    public function token(LoginRequest $request)
//    {
//        $request->authenticate();
//        $user = isAuth()->user();
//        $token = $user->createToken('apiToken')->accessToken;
//
//        return response()->json([
//           'token' => $token
//        ]);
//    }

    public function store(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::query()->where('email', $data['email'])->first();
        $checked = Hash::check($data['password'], $user->password);
        if(!$checked){
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        $token = $user->createToken('apiToken');
        return response()->json([
            'token'=> $token->accessToken
        ]);
    }

    public function me()
    {
        if(!auth()->user()){
            abort(403, 'Unauthorized.');
        }
        $user= [
            'email'=> auth()->user()->email,
            'name' => auth()->user()->name
        ];

        return response()->json([
           'user' => $user
        ]);
    }

    public function destroy()
    {
        $user = auth()->user();
        $user->token()->revoke();

        return response()->json([
            'success'=> 1
        ]);
    }
}
