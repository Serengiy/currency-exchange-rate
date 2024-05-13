<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Register a new user.
     *
     * This endpoint registers a new user with the provided name, email, and password.
     *
     * @group Authentication
     *
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email address of the user. Example: example@example.com
     * @bodyParam password string required The password of the user. Example: 12345678
     * @bodyParam password_confirmation string required The confirmation of the password. Example: 12345678
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
     *       "name": ["The name field is required."],
     *       "email": ["The email field is required.", "The email must be a valid email address.", "The email has already been taken."],
     *       "password": ["The password field is required.", "The password confirmation does not match."]
     */
    public function store(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('apiToken');

        return response()->json([
            'token' => $token->accessToken,
            'user' => [
                'email' =>$user->email,
                'userName' =>$user->name,
            ]
        ]);
    }

}
