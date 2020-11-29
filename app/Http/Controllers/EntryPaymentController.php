<?php

namespace App\Http\Controllers;

use App\Models\EntryPayment;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\EntryPaymentRepositoryInterface;
use App\Repositories\Interfaces\SystemSettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\HelpsResponse;
use App\Http\Resources\EntryPaymentResource;
use App\Http\Resources\SystemSettingResource;
use App\Http\Requests\EntryPaymentStoreRequest;
use App\Events\EntryPaymentApproved;
use App\Notifications\EntryPaymentDisapprovalNotification;


class EntryPaymentController extends Controller
{
    use HelpsResponse;
    private $entryPayment,$systemSetting,$user;
    public function __construct(EntryPaymentRepositoryInterface $entryPayment, 
    SystemSettingRepositoryInterface $systemSetting, UserRepositoryInterface $user){
        $this->entryPayment = $entryPayment;
        $this->systemSetting = $systemSetting;
        $this->user = $user;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$entry_payments = $this->entryPayment->paginate($this->getPagination());
        return EntryPaymentResource::clollection($this->entryPayment->paginate($this->getPagination()));//->response();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EntryPaymentStoreRequest $request)
    {
        try{
            $user =  $this->user->get($request->user_id);
            $ref = null;
            if(empty($user->bank_account_number)){
                return $this->errorResponse('Please fill out your bank details as we need this to pay you when needed');
            }
            if($user->cycled_out==1){
                $ref = $this->user->where('username',$request->referrer)->first();
                if($ref){
                    if($ref->cycled_out==1){
                        return $this->errorResponse('Your referrer has cycled out');
                    }
                }
                // if(\App\Referral::where('referent_id',$ref->uuid)->count() >= 3){
                //     $ajax = Utility::jsonResponse('not_acceptable','Your referrer has exceeded the maximum referral limit, please use another referral_id');
                //     $non_ajax = back()->withErrors('Your referrer has exceeded the maximum referral limit, please use another referral id')->withInput();
                //     return Utility::checkAjax($ajax,$non_ajax);
                // }
           }
           $this->entryPayment->store($request,$user,$ref);
            return $this->successResponse("Entry payment submitted successfully");
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EntryPayment  $entryPayment
     * @return \Illuminate\Http\Response
     */
    public function show(EntryPayment $entryPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EntryPayment  $entryPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(EntryPayment $entryPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EntryPayment  $entryPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
            $v = validator($request->all(),['entry_payment'=>'bail|required|numeric']);
            if($v->fails()){
                return $this->validationErrorResponse($v);
            }
            $update = $this->systemSetting->getModel()->updateOrCreate(['id'=>1],['entry_payment'=>$this->entry_payment]);
            if($update){
                return new SystemSettingResource($this->systemSetting->get(1));
            }else{
                return $this->errorResponse(['error'=>'Unable toupdate entry payment, please try again']);
            }
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EntryPayment  $entryPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(EntryPayment $entryPayment)
    {
        //
    }

     /**
     * pending entry payments
     */
    public function pendingEntryPayments(){
        try{
            $pending_payments = $this->entryPayment->getModel()->where('status','pending')->paginate($this->getPagination());
            return $this->successResponse("pending payment fetched successfully",$pending_payments,'pendign_payments');
        }catch(\Exception $e){
            return $this->exeptionResponse($e);
        }
    }

    /**
     * approve user entry payment
     */
    public function approveEntryPayment(EntryPayment $entry_payment){
        try{
           $entry_payment_history = $this->entryPayment->approve($entry_payment,$request);
           event(new EntryPaymentApproved($entry_payment,$entry_payment_history));
           return $this->successResponse("Entry payment approved succesfully");
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * disapprove user entry payment
     */
    public function disapproveEntryPayment(EntryPayment $entry_payment){
        try{
            $this->entryPayment->disapprove($entry_payment);
            $entry_payment->user->notify(new EntryPaymentDisapprovalNotification($entry_payment->user));
            return $this->successResponse("entry payment disapproved succesfully");
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * view user proof of payment
     */
    public function viewPop(EntryPayment $entry_payment){
        try{
           return $this->successResponse("POP image path loaded successfully",$entry_payment->pop_image);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * total entry payments
     */
    public function totalEntryPayments(){
        try{
            $total = $this->entryPayment-getModel()->where('status','approved')->sum('amount');
            return $this->successResponse("Total entry payment feched successfully",$total,'total_entry_payments');
        }catch(\Exception $e){
           return $this->exceptionResponse($e,'Unable to load total entry payments, please try again');
        }
    }
}
