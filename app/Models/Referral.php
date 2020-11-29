<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $guarded = [];

    /**
     * referred belongs to user
     */
    public function referred(){
        return $this->belongsTo(User::class,'referred_id','uuid');
    }

    /**
     * referent belongs to user
     */
    public function referent(){
        return $this->belongsTo(User::class,'referent_id','uuid');
    }
}
