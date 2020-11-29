<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\AdminRepositoryInterface as RepositoryInterface;
use App\Models\Admin;

class AdminRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(Admin $admin){
        parent::__construct($admin);
        $this->admin = $admin;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->admin;
    }

}