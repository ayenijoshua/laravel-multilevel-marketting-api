<?php
namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
/**
 * user trait
 */
Trait UserDashboard{

    /**
     * fetch dashboard data
     */
    public function dashboard(Request $request){
        try{
            $user = $request->user();
            $wallet = $this->userRepository->calculateWallet($user);
            $data = [
                'wallet'=>$wallet,
                'total_withdrawals'=> $user->totalWithdrawals
            ];
            return $this->successResponse("dashboard data fetched successfully",$data);
        }catch(\Exception $e){
            return $this->exceptionResponse($e, 'Unable to process request, please try again');
        }
        
    }

}