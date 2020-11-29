<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\IncentiveClaimRepositoryInterface as RepositoryInterface;
use App\Repositories\Interfaces\IncentiveRepositoryInterface;
use App\Models\IncentiveClaim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class IncentiveClaimRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(IncentiveClaim $claim, IncentiveRepositoryInterface $incentive){
        parent::__construct($claim);
        $this->claim = $claim;
        $this->incentive = $incentive;
    }

    /**
     * get model instance
     */
    public function getModel(){
        return $this->claim;
    }

    public function store($user,$level){
        return DB::transaction(function() use ($user,$level){
           $create =  $this->updateOrCreate(
                ['user_id'=>$user->id,'user_uuid'=>$user->uuid,'level_id'=>$level->id],
                ['status'=>'pending']
            );
            if($create){
                return true;
            }
            return false;
        },2);
    }

    /**
     * approve incentive claim
     */
    public function approve($incentiveClaim){
        return DB::transaction(function() use ($incentiveClaim){
            $incentive_claim->status = 'disapproved';
            if($incentive_claim->update()){
                return true;
            }
            return false;
            $title = 'Incentive Approval';
            $message = "Congratulations!!, Your Incentives claim for stage {$incentive_claim->level_id} has been approved successfully";
            //Utility::sendNotification(\App\User::find($incentive_claim->user_id),$title,$message);
        },2);
    }

    /**
     * disapprove incentive claim
     */
    public function disapprove($incentiveClaim){
        return DB::transaction(function() use ($incentiveClaim){
            $incentive_claim->status = 'approved';
            if($incentive_claim->update()){
                return true;
            }
            return false;
            $title = 'Incentive Approval';
            $message = "Congratulations!!, Your Incentives claim for stage {$incentive_claim->level_id} has been approved successfully";
            //Utility::sendNotification(\App\User::find($incentive_claim->user_id),$title,$message);
        },2);
    }

    public function cashOut($user){
        DB::transaction(function() use ($user) {
            $incentives = $this->incentive->all();
            foreach( $incentives as  $incentive){
                $claim = $this->incentiveClaim->getModel()->where(['user_uuid'=>$user->uuid,'level_id'=>$incentive->level_id])->first();
                if($claim){
                    if($claim->status != 'approved'){
                       $chshout =  $this->updateOrCreate(
                            ['user_uuid'=>$user->uuid,'level_id'=>$incentive->level_id],
                            ['status'=>'pending','user_id'=>$user->id]
                        );
                        if(!$cashout){
                            throw new \Exception("User was unable to cashout");
                        }
                        $title = 'Incentive In Process';
                        $message = "Congratulations!! Your Incentives claim for Level {$incentive->level_id} is in process";
                        //fire incentive-cashout-event
                        //Utility::sendNotification($user,$title,$message);
                    }
                }else{
                    $chshout = $this->updateOrCreate(
                        ['user_uuid'=>$user->uuid,'level_id'=>$incentive->level_id],
                        ['user_id'=>$user->id,'status'=>'pending']
                    );
                    if(!$cashout){
                        throw new \Exception("User was unable to cashout");
                    }
                    $title = 'Incentive In Process';
                    $message = "Congratulations!! Your Incentives claim for {$incentive->level_id} is in process";
                    //fire incentive-cashout-event
                    //Utility::sendNotification($user,$title,$message);
                }
            }
        });
    }

}