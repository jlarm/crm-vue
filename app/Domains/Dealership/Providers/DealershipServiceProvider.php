<?php

namespace App\Domains\Dealership\Providers;


use App\Domains\Dealership\Contracts\DealershipRepositoryInterface;
use App\Domains\Dealership\Repositories\DealershipRepository;
use Illuminate\Support\ServiceProvider;

class DealershipServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DealershipRepositoryInterface::class,
            DealershipRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
