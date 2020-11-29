<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalHistory extends Model
{
    protected $guarded = [];

    public function getFailedWithdrawalsAttribute(){
        return $this->all()->filter(function($item){
            return !$item->is_successful;
        });
    }

    public function getSuccessfulWithdrawalsAttribute(){
        return $this->all()->filter(function($item){
            return $item->is_successful;
        });
    }
}
