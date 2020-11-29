<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\BankDetailRepositoryInterface as RepositoryInterface;
use App\Models\BankDetail;

class BankDetailRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(BankDetail $bankDetail){
        parent::__construct($bankDetail);
        $this->bankDetail = $bankDetail;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->bankDetail;
    }

    /**
     * create bank detail
     */
    public function store($request){
        $create = $this->create($request->all());
        if(!$create){
            throw new \Exception('Unable to create bank detail');
        }
        return $create;
    }

    /**
     * updaate ban details
     */
    public function updateData($bankDetail,$request){
        if(!$this->update($bankDetail,$request->all())){
            throw new \Exception('Unable to update bank detail');
        }
    }

    /**
     * delete bank detail
     */
    public function deleteData($bankDetail){
        if(!$this->delete($bankDetail)){
            throw new \Exception('Unable to delete bank detail');
        }
    }

}