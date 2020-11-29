<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\BankDetailRepositoryInterface;
use App\Traits\HelpsResponse;

class BankDetailController extends Controller
{
    use HelpsResponse;

    private $bankDetail;
    public function __construct(BankDetailRepositoryInterface $bankDetail){
        $this->bankDetail = $bankDetail;
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
        try{
            $this->bankDetail->store($request);
            return $this->successResponse("bank detail created successfully");
        }catch(\Exeption $e){
            return $this->exeptionResponse($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankDetail  $bankDetail
     * @return \Illuminate\Http\Response
     */
    public function show(BankDetail $bankDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankDetail  $bankDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(BankDetail $bankDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankDetail  $bankDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankDetail $bankDetail)
    {
        try{
            $this->bankDetail->updateData($bankDetail,$request);
            return $this->successMessage("updated");
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankDetail  $bankDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankDetail $bankDetail)
    {
        try{
            $this->bankDetail->deleteData($bankDetail);
            return $this->successMessage("deleted");
        }catch(\Exception $e){
            return $this->exceptionResponse($e);
        }
    }
}
