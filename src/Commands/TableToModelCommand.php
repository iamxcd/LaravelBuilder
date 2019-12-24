<?php

namespace SongBai\LaravelBuilder\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\DB;

class TableToModelCommand extends GeneratorCommand
{


    protected $name = 'lb:t2m';

    protected $description = '已有表格生成模型';
    protected $type = 'Model';
    protected $className = null;
    protected $tableName = null;

    protected $menu = [
        '创建表单验证' => 'lb:request',
        '控制器' => 'lb:controller'
    ];

    protected function getNameInput()
    {
        return $this->className;
    }

    public function toCamelCase($str)
    {
        preg_match('/(\w+).*?/', $str, $mat); // 只匹配字母和下划线
        $str = $mat[0];

        $array = explode('_', $str);
        $result = '';
        foreach ($array as $value) {
            $result .= ucfirst($value);
        }
        return $result;
    }

    public function handle()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $names = $this->choice('请选择需要转换的表序号 ? （多个请用逗号隔开）', $tables, null, null, true);

        $cmds = $this->choice('选择需要创建的类型 ?(多个请用逗号隔开)', array_keys($this->menu), null, null, true);

        foreach ($names as $name) {
            $this->className = $this->toCamelCase($name);
            $this->tableName = $name;
            $this->nextStep();

            foreach ($cmds as $cmd) {
                $this->call($this->menu[$cmd], ['name' => $this->className]);
            }
        }
    }

    public function nextStep()
    {
        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $this->info($this->type . ' created successfully.');
    }

    protected function getStub()
    {
        $stub = 'table2model.stub';
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
        return $rootNamespace . '\Models';
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
            ['DummyNamespace', 'DummyTableName'],
            [$this->getNamespace($name), $this->tableName],
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
