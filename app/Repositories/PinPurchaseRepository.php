<?php 
namespace App\Repositories;

use App\Repositories\Interfaces\PinPurchaseRepositoryInterface as RepositoryInterface;
use App\Repositories\Interfaces\PinPurchaseHistoryRepositoryInterface;
use App\Repositories\Interfaces\SystemSettingRepositoryInterface;
use App\Models\PinPurchase;
use Illuminate\Support\Facades\DB;


class PinPurchaseRepository extends EloquentRepository implements RepositoryInterface{
    
    function __construct(PinPurchase $pinPurchase, SystemSettingRepositoryInterface $systemSetting, 
    PinPurchaseHistoryRepositoryInterface $history){
        parent::__construct($pinPurchase);
        $this->pinPurchase = $pinPurchase;
        $this->systemSetting = $systemSetting;
        $this->history = $history;
    }

    /**
     * get user instance
     */
    public function getModel(){
        return $this->pinPurchase;
    }

    /**
     * process pin purchase
     */
    public function process($request,$user){
        DB::transaction(function() use ($request,$user){
            $file_path = null;
            $purchase = $this->updateOrCreate(
                ['user_id'=>$user->id],
                [
                    'units'=>$request->pins,
                    'status'=>'pending',
                    'pop_path'=> $file_path,
                    'amount'=> $this->systemSetting->value('pin_price'),
                    'payment_mode'=>$request->payment_mode
                ]
            );
            if(!$purchase){
                throw new \Exception("unable to process pin purchase");
            }
            if($request->hasFile('pop')){
                $file_path = $this->storeLocalFile('pop','pin-pop-images','public');
            }
            $purchase->pop_path = $file_path;
            if(!$purchase->update()){
                throw new \Exception("unable to update Purchase pop");
            }
            $this->callArtisan();
        },2);
    }

    /**
     * approve pin purchase
     */
    public function approve($pinPurchase){
        DB::transaction(function() use ($pinPurchase){
            $pinPurchase->status = 'approved';
            if(!$pinPurchase->update()){
                throw new \Exception('Unable to update pin purchase approval');
            }

            $create = $this->history->create([
                'user_id'=>$pinPurchase->user_id,
                'pop_path'=>$pinPurchase->pop,
                'units'=>$pinPurchase->units,
                'created_at'=>$pinPurchase->created_at,
                'month'=>date('n'),
                'year'=>date('Y'),
                'amount'=>$pinPurchase->amount,
                'payment_mode'=>$pinPurchase->payment_mode,
                'is_successful'=> 1
            ]);
            if(!$create){
                throw new \Exception('Unable to create pin purchase history');
            }
            $old_pin = $pinPurchase->user->pin_units;//UserPin::where('user_id',$this->pin->user_id)->value('units');
            $units = $pinPurchase->units + $old_pin;
            if(!$pinPurchase->user->update(['units'=> $units])){
                throw new \Exception('Unable to update pin purchase units');
            }
        });
    }

    /**
     * disapprove pin purchase
     */
    public function disapprove($pinPurchase){
        DB::transaction(function() use ($pinPurchase){
            $pinPurchase->status = 'disapproved';
            if($pinPurchase->update()){
                throw new \Exception('Unable to update pin purchase disapproval');
            }
            
            $ceate = $this->history->create([
                'user_id'=>$pinPurchase->user_id,
                'pop_path'=>$pinPurchase->pop_path,
                'units'=>$pinPurchase->units,
                'created_at'=>$pinPurchase->created_at,
                'month'=>date('n'),
                'year'=>date('Y'),
                'amount'=>$pinPurchase->amount,
                'payment_mode'=>$pinPurchase->payment_mode,
                'is_successful'=> 0
            ]);

            if(!$create){
                throw new \Exception('Unable to create pin purchase history');
            }

            // if($pinPurchase->pop_path){
            //     $this->deleteLocalFile($pinPurchase->pop_path);
            // }
        });
    }

}