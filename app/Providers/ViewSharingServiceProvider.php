<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ViewSharingServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Share the global view variables.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\View\Factory $view)
    {
        $view->share('author', 'Fenzland');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'view',
        ];
    }
}
