<?php
namespace App\Repositories\Interfaces;


interface EloquentRepositoryInterface {

    /**
     * create a user
     */
    public function create($data);

    /**
     * gel all users
     */
    public function all();

    /**
     * get a user
     */
    public function get($id);

    /**
     * update a user
     */
    public function update($id, $data);

    /**
     * delete a user
     */
    public function delete($id);
        
}