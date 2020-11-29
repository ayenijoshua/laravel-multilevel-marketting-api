<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use App\Models\IncentiveClaim;
use App\Models\User;
use App\Models\Level;
use Illuminate\Http\Request;
use App\Traits\HelpsResponse;
use App\Http\Requests\IncentiveRequest;
use App\Repositories\Interfaces\IncentiveRepositoryInterface;
use App\Repositories\Interfaces\IncentiveClaimRepositoryInterface;
use App\Repositories\Interfaces\LevelRepositoryInterface;
class IncentiveController extends Controller
{
    use HelpsResponse;

    private $incentive,$incentiveClaim, $levelRepository;
    public function __construct(IncentiveRepositoryInterface $incentive, IncentiveClaimRepositoryInterface $incentiveClaim, 
    LevelRepositoryInterface $levelRepository){
        $this->incentive = $incentive;
        $this->incentiveClaim = $incentiveClaim;
        $this->levelRepository = $levelRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $incentives = $this->incentive->paginate($this->getPatination());
        return $this->successResponse("incentives fetched successfully",$incentives,'incentives');
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
    public function store(IncentiveRequest $request)
    {
        try{
            if($this->incentive->valueExists('level_id',$request->level_id)){
                return $this->errorResponse(['error'=>'Incntive has been assignrd to this level. please choose a diffrent level']);
            }
            if($this->incentive->store($request)){
                return $this->successResponse("Incentive created successfully");
            }
            return $this->errorResponse(['error'=>'Unable to create incentive, please try again']);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Incentive  $incentive
     * @return \Illuminate\Http\Response
     */
    public function show(Incentive $incentive)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Incentive  $incentive
     * @return \Illuminate\Http\Response
     */
    public function edit(Incentive $incentive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Incentive  $incentive
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Incentive $incentive)
    {
        try{
            if($this->incentive->update($incentive,$request)){
                return $this->successResponse("incentive updated successfully",$incentive->fresh(),'incentive');
            }
            return $this->errorResponse(['error'=>'Unable to update incentive, please try again']);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Incentive  $incentive
     * @return \Illuminate\Http\Response
     */
    public function destroy(Incentive $incentive)
    {
        try{
            if($this->incentive->delete($incentive)){
                return $this->successResponse("incentive deleted successfully");
            }
            return $this->errorResponse(['error'=>'Unable to delete incentive, please try again']);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /* claim level incentives
    */
    public function claimLevelIncentives(Request $request, User $user, Level $level){
        try{
           $claim =  $this->incentiveClaim->getModel()->where(['user_uuid'=>$user->uuid,'level_id'=>$level_id])->first();
           if($claim){
               if($claim->status == 'approved'){
                   return $this->errorResponse("Inentive for level-{$level->id} has been claimed");
               }
               if($claim->status == 'pending'){
                return $this->errorResponse("Inentive for level-{$level->id} in process");
               }
           }
           if($v->fails()){
               $ajax = Utility::jsonResponse('not_acceptable',$v->messages()->all());
               $non_ajax = redirect()->action('UserController@dashboard')->withErrors($v->messages()->all());
              return Utility::checkAjax($ajax,$non_ajax);
           }
           if($level->id > $user->level_id){
                return $this->errorResponse("Invalid request");
           }
           if($this->incentiveClaim->store($user,$level)){
               return $this->sucessResponse("Incentive claim successfull");
           }
           return $this->errorResponse(['error'=>'Unable to process incentive claim, please try again']);
        }catch(\Exception $e){
           return $this->exceptionResponse($e);
        }
    }

    /**
     * get pending incentives claims
     */
    public function pendingIncentivesClaims(){
        try{
            $incentives_claims = $this->incentiveClaim->getModel()->where('status','pending')->paginate($this->getPagination());
            return $this->successResponse("pending incentives fetched successfully",$incentives_claims,'incentive_claims');
        }catch(Ecxeption $e){
            return $this->exceptionResonse($e);
        }
    }

    /**
     * approve incentives claim
     */
    public function approveIncentiveClaim(IncentiveClaim $incentiveClaim){
        try{
           if($this->incentiveClaim->approve($incentiveClaim)){
               $incentiveClaim->user->notify();
               return $this->successResponse("Incentive claim approved successfully");
           }
           return $this->errorResponse(['error'=>'Unable to approve incentive claim, please try again']);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * disapprove incentives claim
     */
    public function disapproveIncentiveClaim($incentive_claim_id){
        try{
            if($this->incentiveClaim->disapprove($incentiveClaim)){
                $incentiveClaim->user->notify();
                return $this->successResponse("Incentive claim disapproved successfully");
            }
            return $this->errorResponse(['error'=>'Unable to disapprove incentive claim, please try again']);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
           
    }

    /**
     * cash out incentives when user cycles out
     */
    public function incentiveCashOut(User $user){
        try{
           $this->incentiveClaim->cachOut($user);
        }catch(Exception $e){
            $this->errorLog($e);
        }
    }

    public function statuses(Request $request){
        try{
            $array = [];
            $user = $request->user();
            $incentives = $this->incentive->all();
            foreach($incentives as $inentive){
                $status = $user->incentiveClaims()->where(['level_id'=>$incentive->level_id])->value('status');
                array_push($array, array('status'=>$status,'incentive'=>$incentive));
            }
            return $this->successResponse("Incentive and claim status fetched successfully",$array);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }
}
