<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\EntryPaymentHistoryRepositoryInterface as RepositoryInterface;
use App\Models\EntryPaymentHistory;

class EntryPaymentHistoryRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(EntryPaymentHistory $entryPaymentHistory){
        parent::__construct($entryPaymentHistory);
        $this->entryPaymentHistory = $entryPaymentHistory;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->entryPaymentHistory;
    }

}