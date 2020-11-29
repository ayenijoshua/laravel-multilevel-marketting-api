<?php
namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use App\Models\User;
/**
 * user Genealogy trait
 */
Trait UserGenealogy{


    /**
     * get genealogy
     */
    public function genealogy(Request $request, User $user=null){
        try{
            $user = $user ?? $request->user();
            $user_id = $user->id;
            $data = [
                'level_genealogy'=>$this->userRepository->levelTreeStructure($user_id),
                'downline_genealogy'=>$this->downlineTreeStructure($user_id)
            ];
            return $this->successResponse('genealogy fetched successfully',$data);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }
}