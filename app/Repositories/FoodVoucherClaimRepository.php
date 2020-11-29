<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\FoodVoucherClaimRepositoryInterface as RepositoryInterface;
use App\Models\FoodVoucherClaim;

class FoodVoucherClaimRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(FoodVoucherClaim $foodVoucherClaim){
        parent::__construct($foodVoucherClaim);
        $this->foodVoucherClaim = $foodVoucherClaim;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->foodVoucherClaim;
    }

}