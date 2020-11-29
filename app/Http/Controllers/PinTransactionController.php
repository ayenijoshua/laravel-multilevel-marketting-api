<?php

namespace App\Http\Controllers;

use App\Models\PinTransaction;
use App\Models\PinPurchase;
use App\Models\PinRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\PinPurchaseRequest;
use App\Http\Requests\PinRegistrationRequest;
use App\Repositories\Interfaces\PinPurchaseRepositoryInterface;
use App\Repositories\Interfaces\PinPurchaseHistoryRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\PinRegistrationRepositoryInterface;
use App\Repositories\Interfaces\PinRegistrationHistoryRepositoryInterface;
use App\Notifications\PinRegistrationApprovalNotification;
use App\Traits\HelpsResponse;
use App\Notifications\PinRegistrationDisapprovalNotification;
use App\Notifications\PinPurchaseDisapprovalNotification;
use App\Notifications\PinPurchaseApprovalNotification;

class PinTransactionController extends Controller
{
    use HelpsResponse;

    private $pinPurchase,$user,$registration,$purchaseHistory,$registrationHistory;

    public function __construct(PinPurchaseRepositoryInterface $pinPurchase, UserRepositoryInterface $user,
    PinRegistrationRepositoryInterface $registration, PinPurchaseHistoryRepositoryInterface $purchaseHistory, 
    PinRegistrationHistoryRepositoryInterface $registrationHistory){
        $this->pinPurchase = $pinPurchase;
        $this->user = $user;
        $this->registration = $registration;
        $this->purchaseHistory = $purchaseHistory;
        $this->registrationHistory = $registrationHistory;
    }

    /**
     * user sent pin purchase request
     */
    public function PinPurchase(PinPurchaseRequest $request, User $user){
        try{
            $this->pinPurchase->process($request,$user);
            return $this->successResponse("Pin Purchase processed successfully, we will notify you on approval");
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * admin approve pin purchase
     */
    public function approvePurchase(PinPurchase $purchase){
        try{
           $this->pinPurchase->approve($purchase);
           $purchase->user->notify(new PinPurchaseApprovalNotification($purchase));
            return $this->successResponse('Pin purchase approved successfully');
        }catch(\Exception $e){
            return $this->exceptionResponse($e,"Unable to approve pin purchase, please try again");
        }
    }

    /**
     * admin disapprove pin purchase
     */
    public function disapprovePinPurchase(PinPurchase $purchase){
        try{
            $this->pinPurchase->disapprove($purchase);
            $purchase->user->notify(new PinPurchaseDisapprovalNotification($purchase));
            return $this->successResponse('Pin purchase disapproved successfully');
        }catch(\Exception $e){
            return $thi->exceptionResponse($e,'Unable to disapprove pin purchase, please try again');
        }
    }

    /**
     * admin get pending pin request page
     */
    public function pendingPinPurchases(){
        try{
            $pending_purchases = $this->pinPurchase->getModel()->where('status','pending')->paginate($this->getPagination());
            return $this->successResponse("pending pin purchases fetched successfully",$pending_purchases,'pending_pin_purchases');
        }catch(\Exception $e){
            return $this->exceptionResponse($e,'Unable to load pending pin purchases, please try again');
        }
    }

    public function pinPurchaseHistory(){
        try{
            $purchase_history = $this->pinPurchase->getModel()->paginate($this->getPagination());
            return $this->successResponse("pending pin purchases fetched successfully",$purchase_history);
        }catch(\Exception $e){
            return $this->exceptionResponse($e,'Unable to load pin purchase history, please try again');
        }
    }

    /**
     * process pin registration
     */
    public function pinRegistration(PinRegistrationRequest $request){
        try{
            $buyer = $this->user->get($request->buyer_id);
            $seller = $this->user->get($request->seller_id);
            $ref = $this->user->getModel()->where('username',$request->referrer)->first();
            $this->registration->process($buyer,$seller,$ref);
            return $this->successResponse('Pin registration is in process, we would notify on approval');
        }catch(\Exception $e){
            return $this->exceptionResponse($e,'Unable to process pin registration, please try again');
        }
    }

    /**
     * Seller approve a pin registration
     */
    public function approveRegistration(PinRegistrationRequest $request, PinRegistration $registration){
        try{
            $ref = $this->user->getModel()->where('username',$request->referrer)->first();
            $this->registration->approve($registration,$ref);
            $registration->user->notify(new PinRegistrationApprovalNotification($registration));
            return $this->successResponse('Pin registration approved successfully');
        }catch(\Exception $e){
            return $this->exceptionResponse($e, 'Unable to approve pin registration, please try again');
        }
    }

    /**
     * seller disapprove a pin registration
     */
    public function disapproveRegistration(){
        try{
            $ref = $this->user->getModel()->where('username',$request->referrer)->first();
            $this->registration->disapprove($registration,$ref);
            $registration->user->notify(new PinRegistrationDisapprovalNotification($registration));
            return $this->successResponse('Pin registration disapprove successfully');
        }catch(\Exception $e){
            return $this->exceptionResponse($e, 'Unable to disapprove pin registration, please try again');
        }
    }

    /**
     * user loadd pending pin registration approval/disapproval
     */
    public function pendingRegistrationApprovals(Request $request, User $user=null){
        try{
            $user =  $user ?? $request->user();
            $pending_reg = $this->registration->getModel()->where('seller_id',$user->id)->paginate($this->getPagination()); //PinTransaction::where('seller_id',$user->id)->where('is_approved',2)->paginate(5);
            $pins = $user->pin_units;
            return $this->successResponse("data fetched successfully",$pending_reg);
        }catch(\Exception $e){
           return $this->exceptionResponse($e);
        }
    }

    /**
     * admin load all pendi
     */
    public function allPendingPinRegistrations(){
        try{
            $pending_reg = $this->registration->paginate($this->getPagination()); //PinTransaction::where('seller_id',$user->id)->where('is_approved',2)->paginate(5);
            return $this->successResponse("data fetched successfully",$pending_reg,'pending_pin_registrations');
        }catch(\Exception $e){
           return $this->errorResponse($e);
        }
    }

    /**
     * admin load pin registration history
     */
    public function registrationHistory(){
        try{
            $history = $this->registrationHistory->paginate($this->getPagination()); //PinTransactionHistory::paginate($this->paginate);
            return view('admin.pin_transactions',['transactions'=>$history,'userPins'=>$userPins]);
        }catch(\Exception $e){
            return $this->errorResponse($e,'Unable to load pin registration history');
        }
    }



}
