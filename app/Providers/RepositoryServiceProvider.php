<?php

namespace App\Providers;

use App\Interfaces\MainDocumentRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\MainDocumentRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

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
        $this->app->bind(MainDocumentRepositoryInterface::class,MainDocumentRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
