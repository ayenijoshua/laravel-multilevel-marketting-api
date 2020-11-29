<?php

namespace App\Http\Controllers;

use App\Models\SystemSettings;
use App\Models\Level;
use Illuminate\Http\Request;
use App\Http\Requests\LevelCompletionBonusRequest;
use App\Repositories\Interfaces\SystemSettingRepositoryInterface;
use App\Traits\HelpsResponse;

class SystemSettingsController extends Controller
{
    use HelpsResponse;

    private $systemSetting;
    public function __construct(SystemSettingRepositoryInterface $systemSetting){
        $this->systemSetting = $systemSetting;
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
     * @param  \App\Models\SystemSettings  $systemSettings
     * @return \Illuminate\Http\Response
     */
    public function show(SystemSettings $systemSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SystemSettings  $systemSettings
     * @return \Illuminate\Http\Response
     */
    public function edit(SystemSettings $systemSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SystemSettings  $systemSettings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
            $this->systemSetting->updateData($request);
            return $this->successResponse('System settings updated successfully');
        }catch(\Exception $e){
            return $this->exceptionRespone($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SystemSettings  $systemSettings
     * @return \Illuminate\Http\Response
     */
    public function destroy(SystemSettings $systemSettings)
    {
        //
    }

    /**
     * update level completion bonus
     */
    public function updateLevelCompletionBonus(LevelCompletionBonusRequest $request, Level $level){
        try{
            $this->systemSetting->updateLevelCompletionBonus($level,$request);
           return $this->successResponse("Level completion bonus updated successfully");
        }catch(\Exception $e){
            return $this->exceptionResponse($e,'Unable to update level completion bonus, please try again');
        }
    }
}
