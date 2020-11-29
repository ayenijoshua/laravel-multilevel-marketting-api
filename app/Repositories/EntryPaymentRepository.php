<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\EntryPaymentRepositoryInterface as RepositoryInterface;
use App\Repositories\Interfaces\EntryPaymentHistoryRepositoryInterface;
use App\Repositories\Interfaces\SystemSettingRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\ReferralRepositoryInterface;
use App\Models\EntryPayment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class EntryPaymentRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(EntryPayment $entryPayment, SystemSettingRepositoryInterface $systemSetting, 
    ReferralRepositoryInterface $referral, EntryPaymentHistoryRepositoryInterface $entryPaymentHistory, UserRepositoryInterface $user){
        parent::__construct($entryPayment);
        $this->entryPayment = $entryPayment;
        $this->systemSetting = $systemSetting;
        $this->referral = $referral;
        $this->entryPaymentHistory = $entryPaymentHistory;
        $this->user = $user;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->entryPayment;
    }

    /**
     * store proof of payment
     */
    public function store($request,$user,$ref=null){
        DB::transaction(function() use ($request,$user,$ref){
            $image_path = null;
            $entryPayment = $this->updateOrCreate(
                ['user_uuid'=>$user->uuid],
                [
                    'user_id'=>$user->id,
                    'pop_img'=>$image_path,
                    'status'=>'pending',
                    'amount'=> $this->systemSetting->value('entry_payment')
                ]);
            if($ref && $user->cycled_out==1){
                $referral = $referral->updateOrCreate(
                    ['referred_id'=>$user->uuid],
                    [
                        'referent_id'=>$ref->uuid,
                        'month'=>Date('n'),
                        'year'=>Date('Y')
                    ]);
            }
            $image_path = $request->file('pop')->store('pop-images','public');
            if(!$image_path){
                throw new \Exception("Unable to upload image");
            }
            if(!$this->update($entry_payment,['pop_path'=>$image_path])){
                throw new \Exception("Unable to update entry payment");
            }
            $this->callArtisan();
        },2);
        
        //return $entryPayment;
    }

    /**
     * aprove entry payment
     */
    public function approve($entry_payment,$request){
        return  $this->transaction(function() use ($entry_payment,$request) {
            $entry_payment->status = 'approved';
            $entry_payment->month = Date('n');
            $entry_payment->year = Date('Y');
            $entry_payment->payment_mode = $request->payment_mode;
            if(!$entry_payment->update()){
                throw new \Exception('Unable to update entry payment on approval');
            }

            //$user = $entry_payment->user;
            //$user->is_approved = 1;
        
            $history = $this->entryPaymentHistory->create(
                [
                    'user_id'=>$entry_payment->user_id,
                    'user_uuid'=>$entry_payment->user->uuid,
                    'pop_image'=>$entry_payment->pop_img,
                    'month'=>$entry_payment->month,
                    'year'=>$entry_payment->year,
                    'amount'=> $entry_payment->amount,
                    'payment_mode'=>$entry_payment->payment_mode,
                    //'is_successful'=>1
                ]
            );
            if(!$history){
                throw new \Exception('Unable to create entry payment history on approval');
            }
            return $history;
        },2);
    }

    /**
     * disapprove user entry payment
     */
    public function disapprove($entry_payment){
        $this->transaction(function() use ($entry_payment){
            $old_image = $entry_payment->pop_image;
            $entry_payment->status = 'disapproved';
            $entry_payment->pop_image = null;
            if(!$entry_payment->update()){
                throw new \Exception('Unable to update entry payment on disapproval');
            }

            //$this->deleteLocalFile($old_image);

            // $user = \App\User::findOrFail($entry_pay->user_id);
            // $user->is_approved = 0;
            // $user->save();

            $history = $this->entryPaymentHistory->create(
                [
                    'user_id'=>$entry_payment->user_id,
                    'user_uuid'=>$entry_payment->user->uuid,
                    'pop_image'=>$entry_payment->pop_img,
                    'month'=>$entry_payment->month,
                    'year'=>$entry_payment->year,
                    'amount'=> $entry_payment->amount,
                    'payment_mode'=>$entry_payment->payment_mode,
                ]
            );
            if(!$history){
                throw new \Exception('Unable to create entry payment history on disapproval');
            }
            
        });
    }

}