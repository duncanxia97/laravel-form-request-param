<?php
/**
 * @author XJ.
 * @Date   2023/8/2 0002
 */

namespace Fatbit\FormRequestParam\Commands\Generator;

use Fatbit\FormRequestParam\Commands\Generator\Traits\GeneratorCommandTrait;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('make:request-param')]
class FormRequestParamCommand extends GeneratorCommand
{
    use GeneratorCommandTrait;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:request-param';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'make:request-param';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form request param class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'RequestParam';

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\RequestParams';
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the service already exists'],
        ];
    }
}
