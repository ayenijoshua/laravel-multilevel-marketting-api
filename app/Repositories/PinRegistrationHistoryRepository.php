<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\PinRegistrationHistoryRepositoryInterface as RepositoryInterface;
use App\Models\PinRegistrationHistory;

class PinRegistrationHistoryRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(PinRegistrationHistory $history){
        parent::__construct($history);
        $this->history = $history;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->history;
    }

}