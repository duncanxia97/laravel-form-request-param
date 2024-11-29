<?php
/**
 * @author XJ.
 * @Date   2024/11/29 星期五
 */

namespace Fatbit\FormRequestParam\Middlewares;


use Fatbit\FormRequestParam\Abstracts\FormRequestParamInterface;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteAction;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

class FormRequestParamValidateMiddleware
{
    protected Container $container;

    protected Router    $router;

    public function __construct(Container $container, Router $router)
    {
        $this->container = $container;
        $this->router    = $router;
    }

    /**
     * @author XJ.
     * @Date   2024/11/29 星期五
     *
     * @param          $request
     * @param \Closure $next
     */
    public function handle(Request $request, \Closure $next)
    {
        $reRouter = new \ReflectionClass($this->router);
        $routes   = $reRouter->getProperty('routes');
//        $routes->setAccessible(true);
        $routes = $routes->getValue($this->router);
        /** @var RouteCollection $routes */
        /** @var \Illuminate\Routing\Route $route */
        $route = $routes->match($request);
        if (is_string($route->action['uses']) && !RouteAction::containsSerializedClosure($route->action)) {
            // 获取控制器方法反射
            [$instance, $method] = Str::parseCallback($route->action['uses']);
            $method = new \ReflectionMethod($instance, $method);
        } else {
            // 获取回调方法反射
            $callable = $route->action['uses'];
            if (RouteAction::containsSerializedClosure($route->action)) {
                $callable = unserialize($route->action['uses'])->getClosure();
            }
            $method = new \ReflectionFunction($callable);
        }
        $params      = $method->getParameters();
        $validParams = [
            'rules'      => [],
            'messages'   => [],
            'attributes' => [],
            'classes'    => [],
        ];
        foreach ($params as $param) {
            if ($param->getType() === null) {
                continue;
            }
            /** @var FormRequestParamInterface $className */
            $className = $param->getType()->getName();
            if (in_array(FormRequestParamInterface::class, class_implements($className))) {
                $validParams['attributes'] = [...$validParams['attributes'], ...$className::getAttributes()];
                $validParams['rules']      = [...$validParams['rules'], ...$className::getRules()];
                $validParams['messages']   = [...$validParams['messages'], ...$className::getMessages()];
                $validParams['classes'][]  = $className;
            }
        }
        if (!empty($validParams['rules'])) {
            /** @var ValidationFactory $factory */
            $factory   = app()->make(ValidationFactory::class);
            $validator = $factory
                ->make(
                    $this->validationData(),
                    $validParams['rules'],
                    $validParams['messages'],
                    $validParams['attributes'],
                );
            if ($validator->fails()) {
                $this->failedValidation($validator);
            }
            $validatedData = $validator->validated();
            foreach ($validParams['classes'] as $className) {
                // 获取需要的数据
                $data = [];
                foreach ($className::getFieldMapping() as $key => $toKey) {
                    $data[$toKey] = $validatedData[$key];
                }
                $this->container->singleton($className, fn() => new $className($data));
            }
        }

        return $next($request);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $exception = $validator->getException();

        throw new $exception($validator);
    }

    /**
     * Get data to be validated from the request.
     */
    protected function validationData(): array
    {
        return request()?->all() ?? [];
    }
}