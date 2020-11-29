<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\PinRegistrationRepositoryInterface as RepositoryInterface;
use App\Repositories\Interfaces\PinRegistrationHistoryRepositoryInterface;
use App\Repositories\Interfaces\ReferralRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\PinRegistration;

class PinRegistrationRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(PinRegistration $pinRegistration, ReferralRepositoryInterface $referral, 
    PinRegistrationHistoryRepositoryInterface $history, UserRepositoryInterface $user){
        parent::__construct($pinRegistration);
        $this->pinRegistration = $pinRegistration;
        $this->referral = $referral;
        $this->history = $history;
        $this->user = $user;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->pinRegistration;
    }

    public function process($buyer,$seller,$ref=null){
        return DB::transaction(function() use ($buyer,$seller,$ref){
            if($buyer->cycled_out){
                if($ref){
                   $referral = $this->referral->create(['referred_id'=>$buyer->uuid],
                    [
                        'referent_id'=>$seller->uuid,
                        'month'=>Date('n'),
                        'year'=>Date('Y')
                    ]);
                    if(!$referral){
                        throw new \Exception('Unable to update referral');
                    }
                }
            }
            $process = $this->updateOrCreate(
                ['buyer_id'=>$buyer->id],
                [
                'seller_id'=>$seller->id,
                'status'=>'pending'
            ]);
            if(!$process){
                throw new \Exception('Unable to process registration');
            }
            $buyer->is_approved = 0;
            if(!$buyer->update()){
                throw new \Exception('Unable to update buyer');
            }
        },2);
    }

    /**
     * approve pin registration
     */
    public function approve($registration,$ref){
        DB::transaction(function() use ($registration,$ref){
            $seller = $registration->seller;//UserPin::where('user_id',$approve->seller_id)->first();
            $buyer = $registration->buyer;
            $registration->status = 'approved';
            if(!$registration->update()){
                throw new \Exception('Unable to update pin approval');
            }

            $seller->pin_units = ($seller->pin_units - 1);
            if(!$seller->update()){
                throw new \Exception('Unable to update pin seller');
            }

            $buyer->cycled_out ? $buyer->cycled_out = 0 : '';
            $buyer->is_approved = 1;
            if(!$buyer->update()){
                throw new \Exception('Unable to update pin buyer');
            }

            $create = $this->history->create([
                'seller_id'=>$registration->seller_id,
                'buyer_id'=>$registration->buyer_id,
                'created_at'=>$registration->created_at,
                'updated_at'=>$registration->updated_at
                ]);
            if(!$create){
                throw new \Exception('Unable to create pin registration history');
            }
            //$ref = $this->referral->getModel()->where('referred_id',$buyer->uuid)->value('referent_id');
            //fire make event
            $this->user->makeRefrerrerAPArent($ref->uuid,$buyer->uuid,0);
            
            //$title = 'Pin Registration Approval';
            //$message = 'Congrtulations!!, Your pin registration has been approved syccessfully';
                //Utility::sendNotification(User::find($approve->buyer_id),$title,$message);
            //}
        },2);
    }

    public function disapprove($registration){
        DB::transaction(function() use ($registration){
            $approve = PinTransaction::findOrFail($this->id);
            $old_pin = UserPin::where('user_id',$approve->seller_id)->value('units');
            
            $registration->status = 'disapproved';
            if($registration->update()){
                throw new \Exception('Unable to process pin registration disapproval');
            }

            $registration->buyer->is_approved = 0;
            if($registration->buyer->update()){
                throw new \Exception("unable to update buyer");
            }

            $title = 'Pin Registration Disapproval';
            $message = 'Sorry!!, Your pin registration has been disapproved, contact the merchant for more details';
            Utility::sendNotification(User::find($approve->buyer_id),$title,$message);
            $this->ajax = Utility::jsonResponse('ok','Your Pin Registration disapproved successfully','/user/pin_transactions');
            $this->non_ajax = back()->with('success','Your Pin Registration disapproved successfully');
            
        });
    }

}