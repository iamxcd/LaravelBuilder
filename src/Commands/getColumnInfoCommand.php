<?php

namespace SongBai\LaravelBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class getColumnInfoCommand extends Command
{

    protected $signature = 'lb:getColumn {name?}';
    protected $description = '获取表字段信息';

    public function handle()
    {

        $tableName = trim($this->argument('name'));
        if (!$tableName) {
            $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $tableName = $this->choice('请选择表序号 ?', $tables);
            preg_match('/(\w+).*?/', $tableName, $mat); // 只匹配字母和下划线
            $tableName = $mat[0];
            $columns = DB::connection()->getDoctrineSchemaManager()->listTableColumns($tableName);

            foreach ($columns as $column) {
                // $type = $column->getType()->getName(); 字段类型
                // $info = $column->toArray();

                $columnInfoHandle = config('laravel-builder.getColumn.columnInfoHandle');
                if (is_callable($columnInfoHandle)) {
                    $columnInfoHandle($column);
                } else {
                    $this->info('未设置表结构处理函数。设置方式见readme.md');
                }

                // echo "{label:'$info[comment]',prop:'$info[name]'}," . PHP_EOL;
            }
        }
    }
}
