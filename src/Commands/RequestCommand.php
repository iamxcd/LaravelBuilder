<?php

namespace SongBai\LaravelBuilder\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class RequestCommand extends GeneratorCommand
{


    protected $name = 'lb:request';

    protected $description = '创建一个请求验证器';
    protected $type = 'Request';
    protected $className = null;


    protected function getNameInput()
    {
        if (is_null($this->className)) {
            do {
                $this->className = $this->ask('请输入表单验证类名');
            } while ($this->className == null || $this->className == '');
        }

        return $this->className;
    }

    protected function getStub()
    {
        $stub = 'request.stub';
        $filePath = config('laravel-builder.stub-path') . '/' . $stub;

        if (!file_exists($filePath)) {
            $filePath = __DIR__ . '/../../stubs/' . $stub;
        }

        return $filePath;
    }

    /**
     * @param string $rootNamespace
     * @return string
     *
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Requests';
    }

    /**
     * @param string $stub
     * @param string $name
     * @return $this|GeneratorCommand
     *
     *
     */
    protected function replaceNamespace(&$stub, $name)
    {
        /**
         * 将模板中的替换
         */
        $stub = str_replace(
            ['DummyNamespace'],
            [$this->getNamespace($name)],
            $stub
        );

        return $this;
    }

    /**
     * @param string $stub
     * @param string $name
     * @return mixed|string
     * 替换类名
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace('DummyClass', $class, $stub);
    }

    /**
     * @return array
     * 重写
     */
    protected function getArguments()
    {
        return [];
    }
}
