<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\WithdrawalRepositoryInterface as RepositoryInterface;
use App\Repositories\Interfaces\WithdrawalHistoryRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\Withdrawal;

class WithdrawalRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(Withdrawal $withdrawal, WithdrawalHistoryRepositoryInterface $history){
        parent::__construct($withdrawal);
        $this->withdrawal = $withdrawal;
        $this->history = $history;
        //$this->user = $user;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->withdrawal;
    }

    /**
     * calculate level withdrawals or total withdrawals
     */
    public function totalWithdrawals(User $user,$level_id=null){
        return $withdrawals = is_null($level_id) 
        ? $user->successfulWithdrawals->sum('amount') 
        : $user->successfulWithdrawals->where('level_id',$level_id)->sum('amount');
    }

    /**
     * total system withdrawals
     */
    public function totalSystemWithdrawals(){
        $total = $this->history->getModel()->filter(function($item){
            return $item->is_successful;
        })->sum('amount');
        return $total;
    }

    /**
     * update or create withdrawal
     */
    public function store($request,$user){
        $store = $this->updateOrCreate(
            ['user_id'=>$user->id],
            [
                'user_uuid'=>$user->uuid,
                'amount'=>$request->amount,
                'level_id'=>$request->level_id,
                'status'=>"pending",
            ]
        );
        if(!$store){
            throw new \Exception('Unable to create withdrawal');
        }
    }

    /**
     * approve withdrawal
     */
    public function approve(Model $withdrawal){
        $this->transaction(function() use ($withdrawal){
           if(!$this->update($withdrawal,['status'=>'approved'])){
               throw new \Exception('Unable to update withdrawal request on approval');
           }
            $create = $this->history->create([
                'user_id'=>$withdrawal->user_id,
                'user_uuid'=>$withdrawal->user_uuid,
                'amount'=>$withdrawal->amount,
                'created_at'=>$withdrawal->created_at,
                'updated_at'=>$withdrawal->updated_at,
                'level_id'=>$withdrawal->level_id,
                'month'=>Date('n'),
                'year'=>Date('Y'),
                'is_successful'=>1,
            ]);
            if(!$create){
                throw new \Exception('Unable to create withdrawal history on approval');
            }
        },2);
    }

    /**
     * disapprove withdrawal
     */
    public function disapprove(Model $withdrawal){
        $this->transaction(function() use ($withdrawal){
           if(!$this->update($withdrawal,['status'=>'disapproved'])){
               throw new \Exception('Unable to update withdrawal request on disapproval');
           }
            $create = $this->history->create([
                'user_id'=>$withdrawal->user_id,
                'user_uuid'=>$withdrawal->user_uuid,
                'amount'=>$withdrawal->amount,
                'created_at'=>$withdrawal->created_at,
                'updated_at'=>$withdrawal->updated_at,
                'level_id'=>$withdrawal->level_id,
                'month'=>Date('n'),
                'year'=>Date('Y'),
                'is_successful'=>0,
            ]);
            if(!$create){
                throw new \Exception('Unable to create withdrawal history on disapproval');
            }
        },2);
    }

    /**
     * force user withdrawal when about to cycle out
     */
    public function cashOut($wallet){
        //$wallet =   $this->user->calculateWallet($user);
        $update = $this->updateOrCreate(
            ['user_id'=>$user->id],
            [
                'user_uuid'=>$user->uuid,
                'amount'=>$wallet,
                'level_id'=>$user->level_id,
                'status'=>'pending'
            ]
        );
        if(!$update){
            throw new \Exception('Unable to force user withdrawal on cycling out');
        }
    }

}