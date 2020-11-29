<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinRegistration extends Model
{
    protected $guarded = [];

    public function buyer(){
        return $this->belongsTo(User::class,'user_id','buyer_id');
    }

    public function seller(){
        return $this->belongsTo(User::class,'user_id','seller_id');
    }
}
