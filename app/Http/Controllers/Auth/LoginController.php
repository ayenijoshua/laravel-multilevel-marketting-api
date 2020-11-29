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
        //$this->middleware('guest')->except('logout');
    }

    public function authenticated($user){

    }

    /**
     * authenticate user
     */
    public function authenticate(LoginRequest $request){
        try{
            //dd($request->all());
            $credentials = $request->only('username', 'password');
            if (!Auth::attempt($credentials)) {
                return $this->errorResponse('Invalid login credentials');
            }
            $user = $request->user();
            $token = $user->createToken('authToken');//->plainTextToken;
            if ($request->remember_me){
                //$token->expires_at = Carbon::now()->addWeeks(1);
                //$token->save();
            }
            $resource = [
                'access_token' => $token->plainTextToken,
                //'email_verified_at'=> $user->email_verified_at,
                //'token_type' => 'Bearer',
                'user'=>$user
                // 'expires_at' => Carbon::parse(
                //     $tokenResult->token->expires_at
                // )->toDateTimeString()
            ];
            return $this->successResponse("Logged in successfully",$resource);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }

        /**
         * $user = $request->user();        
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;        
        
        if ($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1); 
        }
                   
        $token->save();        
        
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
         */
    }

    /**
     * log user out
     */
    public function logOut(Request $request){
        $request->user()->token()->revoke();
        return $this->successResponse("Logged out successfully");
    }
}
