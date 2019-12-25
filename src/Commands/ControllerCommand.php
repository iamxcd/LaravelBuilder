<?php

namespace SongBai\LaravelBuilder\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;

class ControllerCommand extends GeneratorCommand
{


    protected $name = 'lb:controller';

    protected $description = '控制器';
    protected $type = 'Controller';
    protected $className = null;

    protected function getNameInput()
    {
        $className = trim($this->argument('name'));
        if ($className != "") {
            $this->className = $className;
        }
        if (is_null($this->className)) {
            do {
                $this->className = $this->ask('请输入控制器名称 不加后缀');
            } while ($this->className == null || $this->className == '');
        }
        $this->className = ucfirst($this->className);
        return $this->className . $this->type; // 首字母大写 再加后缀
    }


    protected function getStub()
    {
        $stub = 'controller.stub';
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
        return $rootNamespace . '\Http\Controllers';
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
            ['DummyNamespace', 'DummyModelClass'],
            [$this->getNamespace($name), $this->getModelClass()],
            $stub
        );

        return $this;
    }

    protected function getModelClass()
    {
        $modelNamespace = $this->rootNamespace() . 'Models';
        return $modelNamespace . '\\' . $this->className;
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
        $stub = str_replace('DummyClass', $class, $stub);
        return $stub;
    }

    /**
     * @return array
     * 重写
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::OPTIONAL, '类名 不用加后缀']
        ];
    }
}
