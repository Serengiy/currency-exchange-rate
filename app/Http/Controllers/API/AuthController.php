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
    /**
     * Authenticate a user and return an access token.
     *
     * This endpoint authenticates a user with the provided email and password and returns an access token if successful.
     *
     * @group Authentication
     *
     * @bodyParam email string required The email address of the user. Example: example@example.com
     * @bodyParam password string required The password of the user.
     *
     * @response
     *   "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImM2YmRiYjM1ZmU5MDIyZDJmODVlMGI0ZTA2MzNlNGRjMGIxZTcwMzIzMTc1ZGQ5N2RkZDM1ZTlhZGRjMTQ3YmFjMDM1N2M3YzJjZWI0MmFhIn0.eyJhdWQiOiIxIiwianRpIjoiYzZiZGJiMzVmZTkwMmJkMmY4NWUwYjRlMDYzM2U0ZGMwYjFlNzAzMjMxNzVkZDk3ZGRkMzVlOWFkZGMxNDdiYWMwMzU3YzdjMmNlYjQyYWEiLCJpYXQiOjE2MjA2NDEzNzEsIm5iZiI6MTYyMDY0MTM3MSwiZXhwIjoxNjUyMTc3Mzc0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.CMlVcmXlM7WZxPd92jE-3nm8VyZqKYZY2F82fGO-6oXGNPDEHvZzK2d1IP-yNBrbT7zDk_UrEgCxuIeUue1Iy4_JVQp60Uv85p1SDqIhwrNPj5JNZp6uglAv2Vjx9T3Oz1VuPqFBJWYlNrFJ9H49vE5qVhA0JCyHy8NhRb6hCKL4iv7lw_ObI8u6SXG_VXI5dLRcLw7bGiYRW4Jxj-JEP0Rk5RlMqf7Wg_LdsmMNjqFqo2aKf2FBLdxH5RvbwE_NnNh5e59-fB8F-93TSx4qXfKjYWi8JXw-f7VhCAAT1z9VNUjIlTgOcQoTXs9tljEjp9Cb-9swcxqKt0CHAw"
     *   "user":
     *     "email": "example@example.com",
     *     "userName": "John Doe"
     *
     * @response status=422
     *   "message": "The given data was invalid.",
     *   "errors":
     *       "email": ["These credentials do not match our records."]
     */
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
            'token'=> $token->accessToken,
            'user' => [
                'userName' => $user->name,
                'email' => $user->email
            ]
        ]);
    }

    /**
     * Get the authenticated user's information.
     *
     * This endpoint retrieves the information of the authenticated user.
     *
     * @group Authentication
     * @authenticated
     *
     * @response
     *   "user":
     *     "email": "example@example.com",
     *     "name": "John Doe"
     *
     * @response status=403
     *   "message": "Unauthorized."
     */

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

    /**
     * Log the authenticated user out.
     *
     * This endpoint logs out the authenticated user by revoking their access token.
     *
     * @group Authentication
     * @authenticated
     *
     * @response
     *   "success": 1
     */

    public function destroy()
    {
        $user = auth()->user();
        $user->token()->revoke();

        return response()->json([
            'success'=> 1
        ]);
    }
}
