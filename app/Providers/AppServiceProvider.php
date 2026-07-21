<?php

namespace App\Providers;

use App\Repositories\Interfaces\CropTypeRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\CropTypeRepository;
use App\Repositories\CategoryRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $bindings = [
            CropTypeRepositoryInterface::class => CropTypeRepository::class,
            CategoryRepositoryInterface::class => CategoryRepository::class,
        ];

        foreach($bindings as $interface => $value){
            $this->app->bind($interface, $value);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
