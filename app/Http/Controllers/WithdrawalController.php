<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\WithdrawalRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WithdrawalRepositoryInterface;
use App\Repositories\Interfaces\SystemSettingRepositoryInterface;
use App\Repositories\Interfaces\WithdrawalHistoryRepositoryInterface;
use App\Notifications\WithdrawalApprovalNotification;
use App\Notifications\WithdrawalDisapprovalNotification;
use App\Traits\HelpsResponse;

class WithdrawalController extends Controller
{
    use HelpsResponse;

    private $withdrawal,$systemSetting,$user, $withdrawalHistoryRepository;
    public function __construct(WithdrawalRepositoryInterface $withdrawal, SystemSettingRepositoryInterface $systemSetting,
     UserRepositoryInterface $user, WithdrawalHistoryRepositoryInterface $withdrawalHistoryRepository){
        $this->systemSetting = $systemSetting;
        $this->withdrawal = $withdrawal;
        $this->user = $user;
        $this->withdrawalHistoryRepository = $withdrawalHistoryRepository;
    }
    
    public function withdrawalRequest(WithdrawalRequest $request, User $user)
    {
        try{
            //$wallet_controller = new \App\Http\Controllers\WalletController($request);
            $minimum_withdrawal = $this->systemSetting->value('minimum_withdrawal'); //$wallet_controller->getWalletValue('minimum_withdrawal');
            $wallet = $this->user->calculateWallet($user);  //$wallet_controller->calculateWallet($user); //$wallet_controller->calculateTotalWithdrawableBonus($user);//calculateWallet($user);

            if($wallet < $this->amount || $wallet <= $minimum_withdrawal){
                return $this->errorResponse('You do not have enough cash to witdraw');
            }
            $this->withdrawal->store($request,$user);
            return $this->successResponse('Withdrawal request submitted successfully, you would be notified on approval');
        }catch(\Exception $e){
            return $this->exceptionResponse($e,'Unable to process withdrawal request, please try again');
        }
    }

    /**
     * admin get pending withdrawals
     */
    public function pendingWithdrawals(){
        try{
            $pending_withdrawals = $this->withdrawal->getModel()->where('status','pending')->paginate($this->getPagination());
            return $pending_withdrawals;
        }catch(\Exception $e){
            return $this->exceptionResponse($e,'Unable to fetch pending wirhdrawals, please try again');
        }
    }

    /**
     * admin approve use withdrawal
     */
    public function approveWithdrawal(Withdrawal $withdrawal){
        try{
            $this->withdrawal->approve($withdrawal);
            $withdrawal->user->notify(new WithdrawalApprovalNotification($withdrawal));
            return $this->successResponse('Withdrawal approved successfully');
        }catch(\Exception $e){
            return $this->exceptionResponse($e,'Unable to approve withdrawal, please try again');
        }
    }

    /**
     * admin disapprove withdrawal
     */
    public function disapproveWithdrawal(Withdrawal $withdrawal){
        try{
            $this->withdrawal->disapprove($withdrawal);
            $withdrawal->user->notify(new WithdrawalDisapprovalNotification($withdrawal));
            return $this->successResponse('Withdrawal disapproved successfully');
        }catch(\Exception $e){
            return $this->exceptionResponse($e,'Unable to disapprove withdrawal, please try again');
        }
    }

    /**
     * cash out user wallet on cycling out
     */
    public function userCashOut(User $user){
        try{
            $this->withdrawal->cashOut($user);
        }catch(Exception $e){
            $this->errorLog($e);
        }
    }

    public function status(Request $request){
        $user = $request->user();
       return $user->withdrawal()->first();
    }

     
}
