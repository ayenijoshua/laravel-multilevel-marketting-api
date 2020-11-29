<?php

namespace App\Http\Controllers;

use App\Models\FoodVoucherClaim;
use App\Models\Level;
use Illuminate\Http\Request;
use App\Traits\HelpsResponse;
use App\Repositories\Interfaces\FoodVoucherClaimRepositoryInterface;
use App\Repositories\Interfaces\LevelRepositoryInterface;

class FoodVoucherClaimController extends Controller
{
    use HelpsResponse;

    private $foodVoucherClaimRepository,$levelRepository;

    function __construct(FoodVoucherClaimRepositoryInterface $foodVoucherClaimRepository, LevelRepositoryInterface $levelRepository){
        $this->foodVoucherClaimRepository = $foodVoucherClaimRepository;
        $this->levelRepository = $levelRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FoodVoucherClaim  $foodVoucherClaim
     * @return \Illuminate\Http\Response
     */
    public function show(FoodVoucherClaim $foodVoucherClaim)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FoodVoucherClaim  $foodVoucherClaim
     * @return \Illuminate\Http\Response
     */
    public function edit(FoodVoucherClaim $foodVoucherClaim)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FoodVoucherClaim  $foodVoucherClaim
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FoodVoucherClaim $foodVoucherClaim)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FoodVoucherClaim  $foodVoucherClaim
     * @return \Illuminate\Http\Response
     */
    public function destroy(FoodVoucherClaim $foodVoucherClaim)
    {
        //
    }

    /**
     * get claim status
     */
    public function status(Request $request, Level $level){
        try{
            $user = $request->user();
            $status = $user->foodVoucherClaims()->where(['level_id'=>$level->id])->value('status');
            return $this->successResponse("food voucher claim status fetched successfully",$status);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
        
    }

    public function statuses(Request $request){
        try{
            $array = [];
            $user = $request->user();
            $levels = $this->levelRepository->all()->filter(function($val){
               return $val->id > 0;
            });
            foreach($levels as $level){
                $status = $user->foodVoucherClaims()->where(['level_id'=>$level->id])->value('status');
                array_push($array, array('level'=>$level,'status'=>$status));
            }
            return $this->successResponse("food voucher claim status fetched successfully",$array);
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }
}
