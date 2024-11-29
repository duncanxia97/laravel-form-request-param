<?php
/**
 * @author XJ.
 * @Date   2024/11/29 星期五
 */

namespace Fatbit\FormRequestParam\Providers;

use Fatbit\FormRequestParam\Middlewares\FormRequestParamValidateMiddleware;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class FormRequestParamValidateProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $this->app->get(Kernel::class);
        $kernel->pushMiddleware(FormRequestParamValidateMiddleware::class);
    }
}