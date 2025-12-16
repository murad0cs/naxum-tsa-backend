<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\CommissionReportRepository;
use App\Repositories\Contracts\CommissionReportRepositoryInterface;
use App\Repositories\Contracts\TopDistributorsRepositoryInterface;
use App\Repositories\TopDistributorsRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings.
     *
     * @var array<class-string, class-string>
     */
    private array $repositories = [
        CommissionReportRepositoryInterface::class => CommissionReportRepository::class,
        TopDistributorsRepositoryInterface::class => TopDistributorsRepository::class,
    ];

    /**
     * Register repository bindings.
     */
    public function register(): void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
