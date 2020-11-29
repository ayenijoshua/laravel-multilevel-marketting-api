<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinPurchase extends Model
{
    protected $guarded = [];

    /**
     * pin purchase belongs to a user
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    
}
