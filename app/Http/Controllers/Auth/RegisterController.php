<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RegisterRequest;
use App\Repositories\UserRepository;
use App\Traits\HelpsResponse;
use Illuminate\Auth\Events\Registered;
use App\Repositories\Interfaces\UserRepositoryInterface;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers,HelpsResponse;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $userRepository;

    function __construct(UserRepositoryInterface $userRepository){
        $this->middleware('guest');
        $this->userRepository = $userRepository;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * register user
     */
    public function register(RegisterRequest $request){
        try{
            $uuid = \Illuminate\Support\Str::random(7);
            $user = $this->userRepository->create(array_merge($request->except('password','terms','password_confirmation'),
            ['password'=>Hash::make($request->password),'month'=>date('n'),'year'=>date('Y'),'uuid'=>$uuid,'uuids'=>$uuid]));
            if($user){
                event(new Registered($user));
                return $this->successResponse('Your account was created successfully. Please check your mail to verify your email address');
            }
        }catch(\Exception $e){
            return $this->exceptionResponse($e);   
        }
    }
}
