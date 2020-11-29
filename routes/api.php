<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("levels",'LevelController@index');

/**
 * authentication routes
 */
Route::group(['prefix'=>'auth'],function(){
    //register user
    Route::post('register','Auth\RegisterController@register');
    //authenticate user
    Route::post('login','Auth\LoginController@authenticate');
    //logout user
    Route::post('logout','Auth\LoginController@logOut');
});

Route::group(['prefix'=>'user','middleware'=>['auth:sanctum']],function(){
    //get authenticated user
    Route::get('show','UserController@show');
    //update
    Route::put('update','UserController@update');
    //dashboard data
    Route::get('dashboard','UserController@dashboard');
    //incentives
    Route::apiResource('incentives','IncentiveController');
    //incentive claims
    Route::post('incentive-claims/users/{user}/levels/{level}','IncentiveController@claim');
    //get profile data
    Route::get('profile','UserController@profile');
    //downlines
    Route::get('downlines','UserController@downlines');
    //levels
    Route::get('levels','LevelController@index');
    //genealogy
    Route::get('genealogy','UserController@genealogy');
    //food-voucher ckaim status
    Route::get('food-voucher-claims/levels/{level}/status','FoodVoucherClaimController@status');
    //all user food-voucher-claim statuses
    Route::get('food-voucher-claims/statuses','FoodVoucherClaimController@statuses');
    //all user incentive-claim statuses
    Route::get('incentive-claims/statuses','IncentiveController@statuses');
    //wallet summary
    Route::get('wallet-summary','UserController@walletSummary');
    //wallet analysis
    Route::get('wallet-analysis','UserController@walletAnalysis');
    //withdrawal history
    Route::get('withdrawal-history','WithdrawalHistoryController@successfulUserWithdrawals');
    //withrawal status
    Route::get('withdrawals/status','WithdrawalController@status');
    //pending pin registration approval
    Route::get('pin-registrations/pending-approvals','PinTransactionController@pendingRegistrationApprovals');
    //pin purchase history
    Route::get('pin-purchase-history','PinTransactionController@pinPurchaseHistory');

    //Route::apiResource('users','UserController')->except('delete','all','show','create');

});
