<?php
namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface {

    /**
     * create a model
     */
    public function create(array $data);

    /**
     * gel all models
     */
    public function all();

    /**
     * get a model
     */
    public function get($id);

    /**
     * update a model
     */
    public function update(Model $model, array $data);

    /**
     * delete a model
     */
    public function delete(Model $model);
        
}