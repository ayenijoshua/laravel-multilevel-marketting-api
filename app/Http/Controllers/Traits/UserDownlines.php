<?php
namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use App\Models\User;
/**
 * user trait
 */
Trait UserDownlines{

    /**
     * get downlines
     */
    public function downlines(Request $request, User $user=null){
        try{
            $user = $user ?? $request->user();
            $data = $user->referreds()->paginate($this->getPagination());
            return $this->successResponse("downline fetched successfully",$data);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    public function approvedDownlines($user){
        try{
            $downlines = $user->referreds->filter(function($item){
                return $this->userRepository->getModel()->where('uuid',$item->referred_id)->value('is_approved');
            });
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }
}