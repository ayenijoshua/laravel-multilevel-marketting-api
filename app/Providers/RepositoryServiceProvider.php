<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
//use App\Repositories\Interfaces\EloquentRepositoryInterface as RepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Repositories\AdminRepository;
use App\Repositories\Interfaces\EntryPaymentRepositoryInterface;
use App\Repositories\EntryPaymentRepository;
use App\Repositories\Interfaces\IncentiveRepositoryInterface;
use App\Repositories\IncentiveRepository;
use App\Repositories\Interfaces\SystemSettingRepositoryInterface;
use App\Repositories\SystemSettingRepository;
use App\Repositories\Interfaces\PinPurchaseRepositoryInterface;
use App\Repositories\PinPurchaseRepository;
use App\Repositories\Interfaces\PinPurchaseHistoryRepositoryInterface;
use App\Repositories\PinPurchaseHistoryRepository;
use App\Repositories\Interfaces\PinRegistrationRepositoryInterface;
use App\Repositories\PinRegistrationRepository;
use App\Repositories\Interfaces\PinRegistrationHistoryRepositoryInterface;
use App\Repositories\PinRegistrationHistoryRepository;
use App\Repositories\Interfaces\ReferralRepositoryInterface;
use App\Repositories\ReferralRepository;
use App\Repositories\Interfaces\BankDetailRepositoryInterface;
use App\Repositories\BankDetailRepository;
use App\Repositories\Interfaces\LevelRepositoryInterface;
use App\Repositories\LevelRepository;
use App\Repositories\Interfaces\WithdrawalRepositoryInterface;
use App\Repositories\WithdrawalRepository;
use App\Repositories\Interfaces\WithdrawalHistoryRepositoryInterface;
use App\Repositories\WithdrawalHistoryRepository;
use App\Repositories\Interfaces\FoodVoucherClaimRepositoryInterface;
use App\Repositories\FoodVoucherClaimRepository;
use App\Repositories\Interfaces\IncentiveClaimRepositoryInterface;
use App\Repositories\IncentiveClaimRepository;
//use App\Repositories\PinPurchaseRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class,UserRepository::class);
        
        $this->app->bind(AdminRepositoryInterface::class,AdminRepository::class);
        
        $this->app->bind(EntryPaymentRepositoryInterface::class,EntryPaymentRepository::class);

        $this->app->bind(IncentiveRepositoryInterface::class,IncentiveRepository::class);

        $this->app->bind(SystemSettingRepositoryInterface::class,SystemSettingRepository::class);

        $this->app->bind(PinPurchaseRepositoryInterface::class,PinPurchaseRepository::class);

        $this->app->bind(PinPurchaseHistoryRepositoryInterface::class,PinPurchaseHistoryRepository::class);

        $this->app->bind(PinRegistrationRepositoryInterface::class,PinRegistrationRepository::class);

        $this->app->bind(PinRegistrationHistoryRepositoryInterface::class,PinRegistrationHistoryRepository::class);

        $this->app->bind(ReferralRepositoryInterface::class,ReferralRepository::class);

        $this->app->bind(BankDetailRepositoryInterface::class,BankDetailRepository::class);

        $this->app->bind(LevelRepositoryInterface::class,LevelRepository::class);

        $this->app->bind(WithdrawalRepositoryInterface::class, WithdrawalRepository::class);

        $this->app->bind(WithdrawalHistoryRepositoryInterface::class,WithdrawalHistoryRepository::class);

        $this->app->bind(FoodVoucherClaimRepositoryInterface::class,FoodVoucherClaimRepository::class);

        $this->app->bind(IncentiveClaimRepositoryInterface::class,IncentiveClaimRepository::class);
        



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
