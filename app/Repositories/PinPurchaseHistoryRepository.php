<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\PinPurchaseHistoryRepositoryInterface as RepositoryInterface;
use App\Repositories\Interfaces\SystemSettingRepositoryInterface;
use App\Models\PinPurchaseHistory;
use Illuminate\Support\Facades\DB;


class PinPurchaseHistoryRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(PinPurchaseHistory $history){
        parent::__construct($history);
        $this->history = $history;
        //$this->systemSetting = $systemSetting;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->history;
    }

    /**
     * successful wa in purchases
     */
    public function successfulWalletPinPurchases($user){
        return $pin_purchases = $user->pinPurchaseHistories->filter(function($item){
            return $item->is_successful && $item->payment_mode=='wallet';
        })->sum('amount');//\App\PinPurchaseHistory::where('user_id',$user->id)->where('pop','wallet')->get();
           
    }
}