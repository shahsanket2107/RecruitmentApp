<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['login','ResetPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $phone = $request->input('phone');
        $password = $request->input('password');

        $user = User::where('phone', '=', $phone)->first();
        if (!$user) {
           return response()->json(['success'=>false, 'message' => 'Login Fail, please check phone number']);
        }
        if (!Hash::check($password, $user->Password)) {
           return response()->json(['success'=>false, 'message' => 'Login Fail, pls check password']);
        }
        //$user_detail = User::where('id',$user->id)->first();
        $token = JWTAuth::fromUser($user);
        //$user['token'] = $token;
        $user->api_token=$token;
        $user->save();
           return response()->json(['success'=>true,'message'=>'success', 'data' => $user]);
    }
    public function ResetPassword(Request $request)
    {
        $phone = $request->input('phone');
        $password = $request->input('password');

        $user = User::where('phone', '=', $phone)->first();
        error_log($user);
        if (!$user) {
           return response()->json(['success'=>false, 'message' => 'Given Phone number does not exist']);
        }

        $user->Password=Hash::make($password);
        $user->save();
           return response()->json(['success'=>true,'message'=>'Password reset successfully', 'data' => $user]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
