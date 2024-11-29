<?php
/**
 * @author XJ.
 * @Date   2024/11/29 星期五
 */

namespace Fatbit\FormRequestParam\Commands\Generator\Traits;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

/**
 * @author XJ.
 * Date: 2022/10/12 0012
 * @extends  GeneratorCommand
 */
trait GeneratorCommandTrait
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/../stubs/' . Str::camel($this->type) . '.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     *
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $name  = str_replace('/', '\\', $name);
        $names = explode('\\', $name);
        $name  = [];
        foreach ($names as $str) {
            $name[] = Str::studly(Str::snake($str));
        }
        $name = implode('\\', $name);
        $name = Str::studly(Str::snake($name));
        $name = parent::qualifyClass($name);
        if (!Str::endsWith($name, $this->type)) {
            $name .= $this->type;
        }

        return $name;
    }

}