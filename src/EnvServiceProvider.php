<?php

namespace James\Env;

use Illuminate\Support\ServiceProvider;

class EnvServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Env $extension)
    {
        if (! Env::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'env');
        }

        $this->app->booted(function () {
            Env::routes(__DIR__.'/../routes/web.php');
        });
    }
}