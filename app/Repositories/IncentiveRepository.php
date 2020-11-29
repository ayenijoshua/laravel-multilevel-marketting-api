<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\IncentiveRepositoryInterface as RepositoryInterface;
use App\Models\Incentive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class IncentiveRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(Incentive $incentive){
        parent::__construct($incentive);
        $this->incentive = $incentive;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->incentive;
    }

    /**
     * store incentive
     */
    public function store($request){
        return DB::transaction(function() use ($request){
            $image_path = null;
            if($request->hasFile('image')){
                $image_path = $request->file('image')->store('incentive-images','public');
                if(!$image_path){
                    throw new \Exception("Unable to upload image");
                }
            }
            $create = $this->create([
                'title'=>$request->title,
                'description'=>$request->desc,
                'level_id'=>$request->level_id,
                'image_path'=>$image_path,
                'value'=>$request->value
            ]);
            Artisan::call('storage:link');
            if($create){
                return true;
            }
            return false;
        },2);
    }

    /**
     * update incentive
     */
    public function update($incentive,$request){
        return DB::transaction(function() use ($request){
            $old_image = $incentive->image_path;
            if($request->hasFile('image')){
                $image_path = $request->file('image')->store('incentive-images','public');
                if(!$image_path){
                    throw new \Exception("Unable to upload image");
                }
                Storage::disk('public')->delete($old_image);
            }
            $update = $this->update($incentive,[
                'title'=>$request->title,
                'description'=>$request->desc,
                'level_id'=>$request->level_id,
                'image_path'=>$image_path,
                'value'=>$request->value
            ]);
            Artisan::call('storage:link');
            if($create){
                return true;
            }
            return false;
        },2);
    }

    /**
     * delete incentive
     */
    public function delete($incentive){
        return DB::transaction(function() use ($incentive){
            $old_image = $incentive->image_path;
            if($old_image){
                Storage::disk('public')->delete($old_image);
            }
            Artisan::call('storage:link');
            if($this->delete($incentive)){
                return true;
            }
            return false;
        },2);   
    }

}