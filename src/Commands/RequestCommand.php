<?php

namespace SongBai\LaravelBuilder\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;

class RequestCommand extends GeneratorCommand
{


    protected $name = 'lb:request';

    protected $description = '创建一个请求验证器';
    protected $type = 'Request';
    protected $className = null;

    protected function getNameInput()
    {
        $className = trim($this->argument('name'));
        if ($className != "") {
            $this->className = $className;
        }
        if (is_null($this->className)) {
            do {
                $this->className = $this->ask('请输入表单验证名称 不加后缀');
            } while ($this->className == null || $this->className == '');
        }

        return ucfirst($this->className) . $this->type; // 首字母大写 再加后缀
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
        return [
            ['name', InputArgument::OPTIONAL, '类名 不用加后缀'],
            ['table', InputArgument::OPTIONAL, '表名，用于生成验证规则']
        ];
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->setRule($stub)->replaceClass($stub, $name);
    }

    public function setRule(&$stub)
    {
        $tableName = $this->argument('table');
        if ($tableName) {
            $columns = DB::connection()->getDoctrineSchemaManager()->listTableColumns($tableName);
            $rule = '';
            $attr = '';
            foreach ($columns as $column) {
                $type = $column->getType()->getName();
                $info = $column->toArray();
                $rule .= "'" . $info['name'] . "'=>'" . $type . "'," . PHP_EOL;
                $attr .= "'" . $info['name'] . "'=>'" . $info['comment'] . "'," . PHP_EOL;
            }

            $stub = str_replace(
                ['#DummyRule', '#DummyAttr'],
                [$rule, $attr],
                $stub
            );
        }
        return $this;
    }
}
