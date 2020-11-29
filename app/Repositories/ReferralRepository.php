<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\ReferralRepositoryInterface as RepositoryInterface;
use App\Models\Referral;

class ReferralRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(Referral $referral){
        parent::__construct($referral);
        $this->referral = $referral;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->referral;
    }

}