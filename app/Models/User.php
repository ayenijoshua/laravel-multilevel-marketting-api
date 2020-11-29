<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name', 'email', 'password',
    // ];

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * user has one pin purchase
     */
    public function pinPurchase(){
        return $this->hasOne(PinPurchase::class);
    }

    /**
     * user has many pin purchase histories
     */
    public function pinPurchaseHistories(){
        return $this->hasMany(PinPurchaseHistory::class);
    }

    /**
     * buyer pin registration status
     */
    public function pinRegistration(){
        return $this->hasOne(PinRegistration::class);
    }
    
    /**
     * seller may register many members
     */
    public function pinRegistrationHistories(){
        return $this->hasMany(PinRegistrationHistory::class,'User_uuid','uuid');
    }

    /**
     * user has one entry payment
     */
    public function entryPayment(){
        return $this->hasOne(EntryPayment::class,'user_uuid','uuid');
    }

    /**
     * user has one referent
     */
    public function referent(){
        return $this->hasOne(Referral::class,'referred_id','uuid');
    }

    /**
     * user has many referreds
     */
    public function referreds(){
        return $this->hasMany(Referral::class,'referent_id','uuid');
    }

    /**
     * user has one level
     */
    public function level(){
        return $this->belongsTo(Level::class);
    }

    /**
     * user has many withdrawals
     */
    public function withdrawalHistories(){
        return $this->hasMany(WithdrawalHistory::class,'user_uuid','uuid');
    }

    /**
     * user has one withdrawal
     */
    public function withdrawal(){
        return $this->hasOne(Withdrawal::class);
    }

    /**
     * user can claim many incentives
     */
    public function incentiveClaims(){
        return $this->hasMany(IncentiveClaim::class,'user_uuid','uuid');
    }

    /**
     * user can claim many food-voucher claims
     */
    public function foodVoucherClaims(){
        return $this->hasMany(FoodVoucherClaim::class,'user_uuid','uuid');
    }

    //--------------------------------------------------------------Accessors------------------------------//

    public function getApprovedDownlinesAttribute(){
       return $downlines = $this->referreds->filter(function($item){
            return $this->where('uuid',$item->referred_id)->value('is_approved');
        });
    }

    public function getUnapprovedDownlinesAttribute(){
        return $downlines = $this->referreds->filter(function($item){
            return !$this->where('uuid',$item->referred_id)->value('is_approved');
         });
    }

    public function getSuccessfulWithdrawalsAttribute(){
    //    return $this->withdrawalHistories->filter(function($item){
    //         return $item->is_successful;
    //     });
        return$this->where('is_successful',1);
    }

    public function getFailedWithdrawalsAttribute(){
        return $this->withdrawalHistories->filter(function($item){
            return !$item->is_successful;
        });
    }

    public function getTotalWithdrawalsAttribute(){
        return $this->successfulWithdrawals->sum('amount');
    }

    
}
