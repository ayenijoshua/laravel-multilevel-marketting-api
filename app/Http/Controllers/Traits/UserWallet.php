<?php
namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use App\Models\User;
/**
 * user trait
 */
Trait UserWallet{

    /**
     * user wallet summary
     */
    public function walletSummary(Request $request, User $user=null){
        try{
            $user = $user ?? $request->user();
            $welcome_bonus = $this->systemSettingRepository->value('welcome_bonus');
            $referral_bonus = $this->systemSettingRepository->value('referral_bonus') * $user->approvedDownlines->count();//($user);
            $total_matrix_bonus = $this->userRepository->calculateWallet($user,'matrix_bonus');
            $total_completion_bonus = $this->userRepository->calculateWallet($user,'completion_bonus');
            $total_withdrawals = $this->withdrawalRepository->totalWithdrawals($user);
            //$withdrawals = $this->withdrawal->getWithdrawalHistory($user)->paginate($this->pagination);
            //$children = $network->findChildren($user->uuid,$user->level_id,false);
            $wallet_balance = $this->userRepository->calculateWallet($user); //+ (($user->level->completion_bonus/3) * (!is_null($children) ? count($children) : 0));
            //$total_food_bonus = $this->wallet->calculateFoodStuffWallet($user);
            //$current_food_bonus = $this->wallet->clalculateLevelFoodStuffBonus($user);
            //info($this->pinPurchaseHistoryRepository->successfulWalletPinPurchases($user));
            $total_pin_purchase = $this->pinPurchaseHistoryRepository->successfulWalletPinPurchases($user);
            $total_bonus = $welcome_bonus + $referral_bonus + $total_matrix_bonus + $total_completion_bonus;
            $data = [
                'total_matrix_bonus'=>$total_matrix_bonus,
                'total_pin_purchase'=>$total_pin_purchase,
                'total_referral_bonus'=>$referral_bonus,
                'total_completion_bonus'=>$total_completion_bonus,
                'total_bonus'=>$total_bonus,
                'total_withdrawals'=>$total_withdrawals,
                //'total_food_bonus'=> $total_food_bonus, 
                //'current_food_bonus'=>$current_food_bonus,
                'welcome_bonus'=>$welcome_bonus,
                //'withdrawal_history'=>$withdrawals
                'wallet_balance'=>$wallet_balance
            ];
            return $this->successResponse("Wallet summary fetched successfully",$data);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * user detailed wallet analysis
     */
    public function walletAnalysis(Request $request, User $user=null){
        try {
            $user = $user ?? $request->user();
            $analysis_array = [];
            $levels = $this->levelRepository->all();
            $i = 0;
            while($i <= 6){
                //compact()
                $level = $this->levelRepository->get($i);
                $level_name = $level->name;
                $downline_bonus = $level->downline_bonus;
                $num_of_downlines = $this->userRepository->totalLevelDownlines($user,$level->id);
                $level_downline_bonus = $this->userRepository->matrixBonus($user,$level->id);
                $level_completion_bonus = $this->userRepository->levelCompletionBonus($user,$level->id);
                $level_total_bonus = $level_downline_bonus + $level_completion_bonus;
                $withdrawals = $this->withdrawalRepository->totalWithdrawals($user,$level->id);
                $level_net_bonus = $level_total_bonus - $withdrawals;
                $total_net_bonus = $this->userRepository->calculateWallet($user);
                array_push($analysis_array, array('level_name'=>$level_name,
                    'downline_bonus'=>$downline_bonus,
                    'num_of_downlines'=>$num_of_downlines,
                    'level_downline_bonus'=>$level_downline_bonus,
                    'level_completion_bonus'=>$level_completion_bonus,
                    'level_total_bonus'=>$level_total_bonus,
                    'withdrawals'=>$withdrawals,
                    'level_net_bonus'=>$level_net_bonus,
                    'total_net_bonus'=>$total_net_bonus
                ));
                $i++;
            }
            return $this->successResponse("Wallet analysis fetched successfully",$analysis_array);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}