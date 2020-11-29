<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\LevelRepositoryInterface as RepositoryInterface;
use App\Models\Level;

class LevelRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(Level $level){
        parent::__construct($level);
        $this->level = $level;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->level;
    }

    

}