<?php
namespace App\Traits;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * A simple trait to help handling responses
 */
trait HelpsResponse {

    /**
     * Recieves an exception and logs it
     */
    public function errorLog(\Exception $ex): void {
        Log::info('Error Message -'.$ex->getMessage());
        Log::info('Error FIle -'.$ex->getFile());
        Log::info('Error Line -'.$ex->getLine());
        Log::info('Error Code -'.$ex->getcode());  
    }

    /**
     * get exception error
     * $msg - Exception message to show user
     */
    public function exceptionResponse($e,$msg='Unable to process request, please try again',$status_code=500){
        $this->errorLog($e);
        $ajax = response()->json([
            'message' => $msg,
            'success' => false
        ],$status_code);
        $non_ajax = back()->with('error',$msg)->withInput();
        return $this->checkAjax($ajax,$non_ajax);
    }

    /** check if request is an ajax request or not
     * $ajax - ajax request
     * $non_ajax - non aja request
     */
    public  function checkAjax($ajax,$non_ajax){
        if(Request::expectsJson()){
            return $ajax;
        }else{
            return $non_ajax;
        }
    }

    /**$v validator
     * get validation error
     */
    public function validationErrorResponse($v,$msg=null,$status_code=422,$redirect=null){
        $ajax = response()->json([
            'message' => $v ? $v->messages()->all():$msg,
            'success' => false,
            'redirect_url'=>$redirect
        ],$status_code);
        $non_ajax = $redirect ? redirect($redirect)->withErrors($v?$v:$msg)->withInput() : back()->withErrors($v?$v:$msg)->withInput();
        return $this->checkAjax($ajax,$non_ajax);
    }

    /**get response error
     * $msg - message to return
     * $v - validator
     */
    public function errorResponse($msg=null,$status_code=422,$redirect=null){
        return $this->validationErrorResponse(null,$msg,$status_code,$redirect);
    }

    /**
     * $msg - message to return with response
     * $resourse - resourse data to be returned with response
     * $resourse_name - name of resourse data to be returned with response
     * $redirect - redirect path (not needed if returning back)
     * 
     */
    public function successResponse($msg=null,$resourse=null,$resourse_name=null,$status_code=201,$redirect=null){
        $ajax = response()->json([
            'message' => $msg,
            $resourse_name??'data'=> $resourse,
            'success' => true
        ],$status_code);
        $non_ajax = $this->successMessage($msg,$redirect); //$redirect ? redirect($redirect)->withSuccess($msg) : back()->withSuccess($msg);
        return $this->checkAjax($ajax,$non_ajax);
    }

    /**returns a success message
     * $mgs - message to return
     * $redirect - redirect path
     */ 
    public function successMessage($msg,$redirect=null){
        return  $redirect ? redirect($redirect)->withSuccess($msg) : back()->withSuccess($msg);
    }

    /**
     * simple success response
     * may be useful for simple redirects
     * may be useful if you want to change the status code
     */
    public function simpleSuccessResponse($msg,$status_code=201,$redirect=null){
        return $this->successResponse($msg,$resourse=null,$resourse_name=null,$status_code,$redirect);
    }

    /**
     * generate random numbers
     */
    public function random($num=7){
        $val = Str::random($num);
        return $val;
    }

    /**
     * set pagination session
     */
    public function setPagination($num=100){
        //session()
        \session(['pagination'=>$num]);
    }

    /**
     * get pagination session
     */
    public function getPagination($num=100){
        //$this->setPagination();
        return session('pagination') ?? $num;
    }

    /**
     * get pagination increments
     */
    public function getIncrement($resourse){
        if(!$resourse){
            throw new \Exception('Resource passed is not an object');
        }
        $current_page = $resourse->currentPage();
        $increment = $this->getPagination() * ($current_page - 1) + 1;
        return $increment;
    }

    /**
     * prepare a curl session
     * @curl_opt_array - curl option array
     */
    public function prepareCurl(array $curl_opt_array){
        $curl = curl_init();
        curl_setopt_array($curl, $curl_opt_array);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        return array('error'=>$err,'response'=>$response);

        /**
         * $curl_opt_array example for get request
         * 
         * array(
                    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($transaction_ref),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        "accept: application/json",
                        "authorization: Bearer ".config('services.paystack.sec_key'),
                        "cache-control: no-cache"
                    ],
                )
         */
    }

    /**
     * get current route prefix
     */
    public function routePrefix($request){
        return $request->route()->getPrefix();
    }

}