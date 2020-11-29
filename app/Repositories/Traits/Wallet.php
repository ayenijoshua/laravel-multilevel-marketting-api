<?php
namespace App\Repositories\Traits;

/**
 * user wallet trait
 */
Trait Wallet{

    /**
     * get system welcome bonus
     */
    public function welcomeBonus(){
        return $this->systemSetting->value('welcome_bonus');
    }

     /**
     * get user referral bonus
     */
    public function referralBonus($user){
        $referrals = $user->referreds->filter(function($item){
           return $item->referred->is_approve;
        });
        return $referrals->count() * $this->systemSetting->value('referal_bonus');
    }

    /**
     * calaculate user wallet ballance
     */
    public function calculateWallet($user,$type=null){
        $user_id = $user->id;
        $level_id = $user->level_id;
        $sum = 0;
        if(is_null($type)){
            $sum = $this->referralBonus($user) + $this->welcomeBonus();
            for($i=0; $i<$level_id+1; $i++){
                if($level_id==$i){
                    return $sum -= $this->matrixBonus($user,$i) - ($this->withdrawal->totalWithdrawals($user) + $this->pinPurchaseHistory->successfulWalletPinPurchases($user));
                    break;
                }
                $sum +=  $this->matrixBonus($user,$i) + $this->levelCompletionBonus($user,$i);
            }
        }
        else{
            if($type=='matrix_bonus'){
                for($i=0; $i<$level_id+1; $i++){
                     $sum +=  $this->matrixBonus($user,$i);
                }
                return $sum;
            }
            if($type=='completion_bonus'){
                for($i=0; $i<$level_id+1; $i++){
                    if($level_id==$i){
                        return $sum += 0;
                        break;
                    }
                        $sum += $this->levelCompletionBonus($user,$i);
                }
            }
            if($type=='food_stuff_bonus'){
                for($i=0; $i<$level_id+1; $i++){
                    $sum +=  $this->levelFoodStuffBonus($i);
                }
                return $sum;
            }
        }
    }

    /**
     * get bonus for completing a level
     */
    public function levelCompletionBonus($user,$level_id=null){
        if($user->level_id==$level_id){
            $completion_bonus = 0;
        }else{
            $level_id = !is_null($level_id)? $level_id : $user->level_id;
            $completion_bonus =  $this->level->get($level_id)->completion_bonus;
            //array_push();
        }
        return $completion_bonus;
    }

    /**
     * calculate bonus based on number of downlines
     */
    public function matrixBonus($user,$level=null){ //total level downline bonus
        $level_id = !is_null($level) ? $level : $user->level_id ;
        $downline_bonus =  $this->level->get($level_id)->downline_bonus; // : $user->level->downline_bonus;
        $traits = class_uses($this);
        //info($traits);
        if(!\in_array('App\Repositories\Traits\Genealogy',$traits)){
            throw new \Exception("Genealogy Trait not used");
        }
        $matrix_bonus =  $downline_bonus * $this->totalLevelDownlines($user,$level_id);
        return $matrix_bonus;   
    }

     /**
     * calculate level food-stuff bonus
     */
    public function levelFoodStuffBonus($level_id){
        $check = $this->level->get($level_id);
        if($check){
            return $check->food_stuff_bonus;
        }
        return 0;
    }
}