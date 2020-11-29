<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\EloquentRepositoryInterface as RepositoryInterface;
use App\Repositories\Interfaces\SystemSettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\LevelRepositoryInterface;
use App\Repositories\Interfaces\WithdrawalRepositoryInterface;
use App\Repositories\Interfaces\PinPurchaseHistoryRepositoryInterface;
use App\Repositories\Interfaces\IncentiveRepositoryInterface;
use App\Models\User;
use App\Exceptions\ModelNotUpdatedException;
use App\Repositories\Traits\Wallet;
use App\Repositories\Traits\Genealogy;

class UserRepository extends EloquentRepository implements UserRepositoryInterface{
    
    use Wallet,Genealogy; 

    private $user,$systemSetting,$level,$withdrawal,$pinPurchaseHistory,$incentive;

    function __construct(User $user, SystemSettingRepositoryInterface $systemSetting, LevelRepositoryInterface $level,
    WithdrawalRepositoryInterface $withdrawal, PinPurchaseHistoryRepositoryInterface $pinPurchaseHistory, IncentiveRepositoryInterface $incentive){
        parent::__construct($user);
        $this->user = $user;
        $this->systemSetting = $systemSetting;
        $this->level = $level;
        $this->withdrawal = $withdrawal;
        $this->pinPurchaseHistory = $pinPurchaseHistory;
        $this->incentive = $incentive;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->user;
    }

    public function updateProfilePhoto($user,$request){
        if(!$this->update($user,$request->image)){
            throw new ModelNotUpdatedException("Unable to update profile photo");
        }
        $this->storeLocalFile('image','profile-photos');
    }


    

    

   

}