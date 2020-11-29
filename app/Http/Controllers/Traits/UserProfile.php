<?php
namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use App\Models\User;
/**
 * user profole trait
 */
trait UserProfile{

    /**
     * fetch profile data
     */
    public function profile(Request $request, User $user=null){
        try{
            $user = $user ?? $request->user();
            $downlines = $user->referreds->filter(function($item){
               return $this->userRepository->getModel()->where('uuid',$item->referred_id)->value('is_approved');
            });
            $upline = $user->referent;
            $data = [
                'downlines'=>$downlines,
                'upline'=>$upline
            ];
            return $this->successResponse("profile data fetched successfully",$data);
        }catch(\Exception $e){
            return $this->exceptionResponse($e); 
        }
    }
}