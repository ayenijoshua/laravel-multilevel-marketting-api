<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\SystemSettingRepositoryInterface as RepositoryInterface;
use App\Models\SystemSetting;

class SystemSettingRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(SystemSetting $systemSetting){
        parent::__construct($systemSetting);
        $this->systemSetting = $systemSetting;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->systemSetting;
    }

    /**
     * get value
     */
    public function value($value){
        //info($this->all());
        return  $this->get(1)->$value ?? 1; 
    }

    /**
     * update data
     */
    public function updateData($request){
        $this->update($this->get(1),$request->all());
    }

    /**
     * update level completion bonus
     */
    public function updateLevelCompletionBonus($level,$request){
        $this->update($level,$request->all());
    }

}