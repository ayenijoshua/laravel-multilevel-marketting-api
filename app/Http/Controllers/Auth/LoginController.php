<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Traits\HelpsResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers,HelpsResponse;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticated($user){

    }

    /**
     * authenticate user
     */
    public function authenticate(LoginRequest $request){
        $credentials = $request->only('email', 'password');
        if (!Auth::once($credentials)) {
            return $this->errorResponse('Invalid login credentials');
        }
        if ($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
        }
        $resource = [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];
        return $this->successResponse("Logged in successfully",$resource);
    }

    /**
     * log user out
     */
    public function logOut(Request $request){
        $request->user()->token()->revoke();
        return $this->successResponse("Logged out successfully");
    }
}
