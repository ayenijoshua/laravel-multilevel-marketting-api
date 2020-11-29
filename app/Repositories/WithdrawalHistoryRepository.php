<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\WithdrawalHistoryRepositoryInterface as RepositoryInterface;
use App\Models\WithdrawalHistory;

class WithdrawalHistoryRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(WithdrawalHistory $withdrawalHistory){
        parent::__construct($withdrawalHistory);
        $this->withdrawalHistory = $withdrawalHistory;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->withdrawalHistory;
    }
}