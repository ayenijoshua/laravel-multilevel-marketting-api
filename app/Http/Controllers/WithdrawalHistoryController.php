<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalHistory;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HelpsResponse;
use App\Repositories\Interfaces\WithdrawalHistoryRepositoryInterface;

class WithdrawalHistoryController extends Controller
{
    use HelpsResponse;

    private $withdrawalHistoryRepository;

    function  __construct(WithdrawalHistoryRepositoryInterface $withdrawalHistoryRepository){
        $this->withdrawalHistoryRepository = $withdrawalHistoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $withdrawalHistory = $this->withdrawalHistoryRepository->paginate($this->getPagination());
        return $this->successResponse("Withdrawal history fetched successfully",$withdrawalHistory);
    }

    public function failedWithdrawals(){
        $withdrawalHistory = $this->withdrawalHistoryRepository->failedWithdrawals()->paginate($this->getPagination());
        return $this->successResponse("Withdrawal history fetched successfully",$withdrawalHistory);
    }

    public function successfulWithdrawals(){
        $withdrawalHistory = $this->withdrawalHistoryRepository->successfulWithdrawals()->paginate($this->getPagination());
        //$total 
        return $this->successResponse("Withdrawal history fetched successfully",$withdrawalHistory);
    }

    public function userWithdrawals(Request $request, User $user=null){
        $user = $request->user() ?? $user;
        $withdrawals = $user->withdrawalHistories->paginate($this->getPagination());
        $total_withdrawals = $user->totalWithdrawals;
        $data = array('withdrawals'=>$withdrawals,'total_withdrawals'=>$total_withdrawals);
        return $this->successResponse("User withdrawals fetched successfully",$data);
    }

    public function failedUserWithdrawals(User $user){
        $withdrawals = $user->failedWithdrawals()->paginate($this->getPagination());
        return $this->successResponse("Failed User withdrawals fetched successfully",$withdrawals);
    }

    public function successfulUserWithdrawals(Request $request, User $user=null){
        $user = $user ?? $request->user();
        $withdrawals = $user->successfulWithdrawals->paginate($this->getPagination());
        $total_withdrawals = $user->totalWithdrawals;
        $data = array('withdrawals'=>$withdrawals,'total_withdrawals'=>$total_withdrawals);
        return $this->successResponse("Failed User withdrawals fetched successfully",$data);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  \App\Models\WithdrawalHistory  $withdrawalHistory
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show(WithdrawalHistory $withdrawalHistory)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  \App\Models\WithdrawalHistory  $withdrawalHistory
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit(WithdrawalHistory $withdrawalHistory)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \App\Models\WithdrawalHistory  $withdrawalHistory
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, WithdrawalHistory $withdrawalHistory)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  \App\Models\WithdrawalHistory  $withdrawalHistory
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy(WithdrawalHistory $withdrawalHistory)
    // {
    //     //
    // }
}
