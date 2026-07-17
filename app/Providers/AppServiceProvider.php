<?php

namespace App\Providers;

use App\Repositories\Interfaces\CropTypeRepositoryInterface;
use App\Repositories\CropTypeRepository;
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
