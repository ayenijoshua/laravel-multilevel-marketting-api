<?php

namespace App\Listeners;

use App\Events\EntryPaymentApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\HelpsResponse;
use App\Notifications\EntryPaymentApprovalNotification;
use App\Notifications\BackgroundProcessErrorNotification;
use App\Models\Admin;
use Illuminate\Support\Facades\Notification;

class MakeReferrerAParent
{
    use HelpsResponse;

    private $user;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Handle the event.
     *
     * @param  EntryPaymentApproved  $event
     * @return void
     */
    public function handle(EntryPaymentApproved $event)
    {   
        $referent = $event->entryPayment->user->referent;
        $child = $event->entryPayment->uuid;
        $user = $event->entryPayment->user;
        $level_id=0;
        try{
            $this->user->transaction(function(){
                if(!$event->entryPaymentHistory->update(['is_successful'=>1])){
                    $msg = 'Unable to update entry payment history on making referrer a parent';
                    throw new \Exception($msg);
                }
                $user->is_approved = 1;
                if($user->cycled_out){
                    $user->cycled_out = 0;
                }
                if(!$user->update()){
                    throw new \Exception('Unable to update user on making referrer a parent');
                }
                $this->user->makeRefrerrerAParent($referent,$child,$level_id);
                $user->notify(new EntryPaymentApprovalNotification($event->entryPayment->user));
            },2);
            
        }catch(\Exception $e){
            $this->errorLog($e);
            $msg = 'This exception occured when trying to make a referral a parent(via event listener) after entry payment approval';
            Notification::send(Admin::all(), new BackgroundProcessErrorNotification($msg,$e));
            if(!$event->entryPayment->update(['status'=>'pending'])){
                $msg = 'Unable to revert entry payment status to pending on making referrer a parent';
                Notification::send(Admin::all(), new BackgroundProcessErrorNotification($msg));
                throw new \Exception($msg);
            }
        }
        
    }
}
