<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class BladeSubIndentServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Share the global view variables.
     *
     * @return void
     */
    public function boot(BladeCompiler $compiler)
    {
        $compiler->directive('subindent', function () {
            return "<?php ob_start() ?>";
        });

        $compiler->directive('endsubindent', function () {
            return "<?= preg_replace('/(?:^\\n?|(?<=\\n))\\t+(?: (?=\\t))?/', '', ob_get_clean()); ?>";
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'blade.compiler',
        ];
    }
}
