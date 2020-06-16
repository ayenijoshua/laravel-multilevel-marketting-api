<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\EloquentRepositoryInterface as RepositoryInterface;
use App\Models\Admin;

class AdminRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(Admin $admin){
        parent::__construct($user);
        $this->admin = $admin;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->admin;
    }

}