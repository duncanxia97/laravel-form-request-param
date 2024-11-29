<?php
/**
 * @author XJ.
 * @Date   2024/11/29 星期五
 */

namespace Fatbit\FormRequestParam\Providers;

use Fatbit\FormRequestParam\Commands\Generator\FormRequestParamCommand;
use Fatbit\FormRequestParam\Middlewares\FormRequestParamValidateMiddleware;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class FormRequestParamProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $this->app->get(Kernel::class);
        $kernel->pushMiddleware(FormRequestParamValidateMiddleware::class);
    }

    public function register()
    {
        $this->app->singleton(
            'command.make.request-param',
            function ($app) {
                return new FormRequestParamCommand($app['files']);
            }
        );
        $this->commands('command.make.request-param');
    }
}