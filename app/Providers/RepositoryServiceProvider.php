<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\EloquentRepositoryInterface as RepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\AdminRepository;
use App\Controllers\UserController;
use App\Controllers\AdminController;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(UserController::class)
          ->needs(RepositoryInterface::class)
          ->give(function () {
              return UserRepository::class;
          });

          $this->app->when(AdminController::class)
          ->needs(RepositoryInterface::class)
          ->give(function () {
              return AdminRepository::class;
          });


        // $this->app->bind(
        //     RepositoryInterface::class,
        //     UserRepository::class
        // );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
