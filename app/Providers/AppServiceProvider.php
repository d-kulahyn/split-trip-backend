<?php

namespace App\Providers;

use App\Domain\Policies\DebtPolicy;
use App\Domain\Policies\ExpensePolicy;
use App\Domain\Policies\GroupPolicy;
use App\Domain\Repository\ActivityWriteRepositoryInterface;
use App\Domain\Repository\CurrencyReadRepositoryInterface;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\CustomerWriteRepositoryInterface;
use App\Domain\Repository\DebtReadRepositoryInterface;
use App\Domain\Repository\DebtWriteRepositoryInterface;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;
use App\Infrastrstructure\Persistence\EloquentActivityLogWriteWriteRepository;
use App\Infrastrstructure\Persistence\EloquentCustomerReadRepository;
use App\Infrastrstructure\Persistence\EloquentCustomerWriteRepository;
use App\Infrastrstructure\Persistence\EloquentDebtReadRepository;
use App\Infrastrstructure\Persistence\EloquentDebtWriteRepository;
use App\Infrastrstructure\Persistence\EloquentGroupReadRepository;
use App\Infrastrstructure\Persistence\EloquentGroupWriteRepository;
use App\Infrastrstructure\Persistence\EloquentTransactionWriteRepository;
use App\Infrastrstructure\Persistence\ExchangeRateApiCurrencyReadRepository;
use App\Infrastrstructure\Service\Interface\SecurityCodeStorageInterface;
use App\Infrastrstructure\Service\SecurityCodeRedisStorage;
use App\Models\Expense;
use App\Models\ExpenseDebt;
use App\Models\Group;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //security code
        $this->app->bind(
            SecurityCodeStorageInterface::class,
            SecurityCodeRedisStorage::class
        );

        //customer
        $this->app->bind(CustomerWriteRepositoryInterface::class, EloquentCustomerWriteRepository::class);
        $this->app->bind(CustomerReadRepositoryInterface::class, EloquentCustomerReadRepository::class);

        //groups
        $this->app->bind(GroupWriteRepositoryInterface::class, EloquentGroupWriteRepository::class);
        $this->app->bind(GroupReadRepositoryInterface::class, EloquentGroupReadRepository::class);

        $this->app->bind(CurrencyReadRepositoryInterface::class, ExchangeRateApiCurrencyReadRepository::class);

        $this->app->bind(DebtWriteRepositoryInterface::class, EloquentDebtWriteRepository::class);
        $this->app->bind(DebtReadRepositoryInterface::class, EloquentDebtReadRepository::class);

        $this->app->bind(ActivityWriteRepositoryInterface::class, EloquentActivityLogWriteWriteRepository::class);

        $this->app->bind(TransactionWriteRepositoryInterface::class, EloquentTransactionWriteRepository::class);

        $this->app->bind(Factory::class, function (Application $app) {
            return (new Factory())->withServiceAccount($app->basePath('firebase.json'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        bcscale(2);

        Gate::policy(ExpenseDebt::class, DebtPolicy::class);
        Gate::policy(Expense::class, ExpensePolicy::class);
        Gate::policy(Group::class, GroupPolicy::class);
    }
}
