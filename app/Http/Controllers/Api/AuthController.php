<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Requests\RegisterRequest;
use App\User as User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * User can register
     * api/auth/register
     * @param RegisterRequest $request (name, email, password)
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
           'name'     =>  $request->name,
           'email'    =>  $request->email,
           'password' =>  bcrypt($request->password)
        ]);
        return response()->json($user, 201);
    }

    /**
     * Registered user can log in and get api token
     * api/auth/login
     * @param LoginRequest $request (email, password)
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::foundBy('email',$request->email);
        /** @var User $user */
        if(Hash::check($request->password, $user->password)) {
            $user->generateApiToken();
            return response()->json(['status'=>'success', 'user' => $user],200);
        }

        return response()->json(['status'=>'error', 'message' => 'Invalid Credentials'], 401);
    }

    /**
     * User can logout - delete api token
     * api/auth/logout
     * @param LogoutRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(LogoutRequest $request)
    {
        User::foundBy('api_token',$request->api_token)->deleteApiToken();
        return response()->json(['status'=>'Success', 'message' => 'You successfully logged out'], 200);
    }

}