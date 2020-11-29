<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Closure;

class StoreLocalFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_name,$file_path,$directory,$model=null,$multiple=null)
    {
        $this->File_name = $file_name;
        $this->file_path;
        $this->directory = $directory;
        $this->model = $model;
        $this->multiple = $multiple;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_path = $request->file($file_name)->store($file_path,$directory);
        if(!$file_path){
            throw new \Exception("Unable to store file");
        }
        if(!$model->update([$file_name=>$file_path])){
            throw new \Exception("Unable to update $file_path");
        }
    }
}
