<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\UserRepositoryInterface as RepositoryInterface;
use App\Repositories\Interfaces\SystemSettingRepositoryInterface;
use App\Repositories\Interfaces\WithdrawalRepositoryInterface;
use App\Repositories\Interfaces\LevelRepositoryInterface;
use App\Repositories\Interfaces\PinPurchaseRepositoryInterface;
use App\Repositories\Interfaces\PinPurchaseHistoryRepositoryInterface;
use App\Traits\HelpsResponse;
use App\Http\Requests\UpdateProfileData;
use App\Http\Requests\UpdateBankData;
use App\Http\Requests\UpdateProfilePhoto;
use App\Exceptions\ModelNotUpdatedException;
use App\Http\Controllers\Traits\UserDashboard;
use App\Http\Controllers\Traits\UserProfile;
use App\Http\Controllers\Traits\UserGenealogy;
use App\Http\Controllers\Traits\UserDownlines;
use App\Http\Controllers\Traits\UserWallet;

class UserController extends Controller
{
    use HelpsResponse,UserDashboard,UserProfile,UserGenealogy,UserDownlines,UserWallet;

    private $userRepository,$systemSettingRepository,$withdrawalRepository, $levelRepository,$pinPurchaseRepository,$pinPurchaseHistoryRepository;

    function __construct(RepositoryInterface $userRepository,SystemSettingRepositoryInterface $systemSettingRepository,
    WithdrawalRepositoryInterface $withdrawalRepository, LevelRepositoryInterface $levelRepository, PinPurchaseHistoryRepositoryInterface $pinPurchaseHistoryRepository){
        $this->userRepository = $userRepository;
        $this->systemSettingRepository = $systemSettingRepository;
        $this->withdrawalRepository = $withdrawalRepository;
        $this->levelRepository = $levelRepository;
        //$this->pinPurchaseRepository = $pinPurchaseRepository;
        $this->pinPurchaseHistoryRepository = $pinPurchaseHistoryRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userRepository->paginate($this->getPagination());
        return $this->successResponse('Users fetched successfully',$users,'users');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, User $user=null)
    {
        try{
            if($user){
                return $this->successResponse("User fetched successfully",$user,'user');
            }
            $user =  $request->user();
            return $this->successResponse("User fetched successfully",$user,'user');
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
            $user = $request->user();
            $this->userRepository->update($user,$request->all());
            return $this->successResponse("user updated successfully");
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try{
            $this->userRepository->delete($user);
            return $this->successResponse("User deleted successfully");
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }
    

    /**
     * update profile data
     */
    public function updateProfileData(UpdateProfileData $request){
        try{
            $user = $request->user();
            $this->userRepository->update($user,$request->all());
            return $this->successResponse("Profile data updated successfully");
        }catch(\Exception $e){
            return $this->exceptionesponse($e);
        }
    }

    /**
     * update bank data
     */
    public function updateBankData(UpdateBankData $request){
        try{
            $user = $request->user();
            $this->userRepository->update($user,$request->all());
            return $this->successResponse("Bank data updated successfully");
        }catch(\Exception $e){
            return $this->exceptionesponse($e);
        }
    }

    /**
     * update profile photo
     */
    public function updateProfilephoto(UpdateProfilePhoto $request){
        try{
            $this->userRepository->updateProfilePhoto($request->user(),$request);
            return $this->successResponse("profile photo updated successfully");
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    
}
