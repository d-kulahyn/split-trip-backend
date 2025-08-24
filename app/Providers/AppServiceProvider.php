<?php

namespace App\Providers;

use App\Domain\Policies\DebtPolicy;
use App\Domain\Policies\ExpensePolicy;
use App\Domain\Policies\GroupPolicy;
use App\Domain\Repository\ActivityReadRepositoryInterface;
use App\Domain\Repository\ActivityWriteRepositoryInterface;
use App\Domain\Repository\BalanceReadRepositoryInterface;
use App\Domain\Repository\BalanceWriteRepositoryInterface;
use App\Domain\Repository\CurrencyReadRepositoryInterface;
use App\Domain\Repository\CustomerReadRepositoryInterface;
use App\Domain\Repository\CustomerWriteRepositoryInterface;
use App\Domain\Repository\DebtReadRepositoryInterface;
use App\Domain\Repository\DebtWriteRepositoryInterface;
use App\Domain\Repository\GroupReadRepositoryInterface;
use App\Domain\Repository\GroupWriteRepositoryInterface;
use App\Domain\Repository\TransactionReadRepositoryInterface;
use App\Domain\Repository\TransactionWriteRepositoryInterface;
use App\Infrastrstructure\Caching\CachedGroupReadRepository;
use App\Infrastrstructure\Persistence\EloquentActivityLogReadRepository;
use App\Infrastrstructure\Persistence\EloquentActivityLogWriteWriteRepository;
use App\Infrastrstructure\Persistence\EloquentCustomerReadRepository;
use App\Infrastrstructure\Persistence\EloquentCustomerWriteRepository;
use App\Infrastrstructure\Persistence\EloquentDebtReadRepository;
use App\Infrastrstructure\Persistence\EloquentDebtWriteRepository;
use App\Infrastrstructure\Persistence\EloquentGroupReadRepository;
use App\Infrastrstructure\Persistence\EloquentGroupWriteRepository;
use App\Infrastrstructure\Persistence\EloquentTransactionReadRepository;
use App\Infrastrstructure\Persistence\EloquentTransactionWriteRepository;
use App\Infrastrstructure\Persistence\ExchangeRateApiCurrencyReadRepository;
use App\Infrastrstructure\Persistence\RedisBalanceReadRepository;
use App\Infrastrstructure\Persistence\RedisBalanceWriteRepository;
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
//        $this->app->bind(GroupReadRepositoryInterface::class, EloquentGroupReadRepository::class);
        $this->app->bind(GroupReadRepositoryInterface::class, function ($app) {
            return new CachedGroupReadRepository(
                inner: $app->make(EloquentGroupReadRepository::class),
                cache: $app->make('cache.store'),
                ttlSeconds: 30
            );
        });

        $this->app->bind(CurrencyReadRepositoryInterface::class, ExchangeRateApiCurrencyReadRepository::class);

        $this->app->bind(DebtWriteRepositoryInterface::class, EloquentDebtWriteRepository::class);
        $this->app->bind(DebtReadRepositoryInterface::class, EloquentDebtReadRepository::class);

        $this->app->bind(ActivityWriteRepositoryInterface::class, EloquentActivityLogWriteWriteRepository::class);

        $this->app->bind(TransactionWriteRepositoryInterface::class, EloquentTransactionWriteRepository::class);

        $this->app->bind(Factory::class, function (Application $app) {
            return (new Factory())->withServiceAccount($app->basePath('firebase.json'));
        });

        $this->app->bind(BalanceWriteRepositoryInterface::class, RedisBalanceWriteRepository::class);
        $this->app->bind(BalanceReadRepositoryInterface::class, RedisBalanceReadRepository::class);

        $this->app->bind(ActivityReadRepositoryInterface::class, EloquentActivityLogReadRepository::class);
        $this->app->bind(TransactionReadRepositoryInterface::class, EloquentTransactionReadRepository::class);
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
