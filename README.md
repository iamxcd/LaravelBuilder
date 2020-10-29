## laravel 代码生成

>重写了artisan make 一部分命令,使之在项目中更灵活

### 安装
```
composer require iamxcd/laravel-builder --dev
```

### 使用
```
php artisan lb // 选择使用功能
php artisan lb:request // 验证器
php artisan lb:t2m //已有表格生成模型
php artisan lb:controller // 生成控制器
php artisan lb:getColumn {name?} //  获取字段信息
```

### 功能
- [x] 表单验证 
- [x] 已有表格批量生成模型、控制器、验证器
- [x] 控制器
- [x] 根据表结构 大致生成验证规则

### 自定义模板 发布配置

```
// 发布配置
php artisan vendor:publish --provider="SongBai\LaravelBuilder\Providers\LaravelBuilderServiceProvider"
<?php
// 将需要自定义的模板复制一份 stub-path 指定的目录

return [
    'stub-path' => resource_path('stub')
];
```

###  获取表结构 字段信息

    配置回调 config('laravel-builder.getColumn.columnInfoHandle');

````

config/laravel-builder.php
<?php

return [
    'stub-path' => resource_path('stubs'),
    'getColumn' => [
        // 自定义处理回调
        'columnInfoHandle' => function (\Doctrine\DBAL\Schema\Column $column) {
            $info = $column->toArray();
            echo "{label:'$info[comment]',prop:'$info[name]'}," . PHP_EOL;
        }
    ]
];



getColumnInfoCommand.php

...
$columnInfoHandle = config('laravel-builder.getColumn.columnInfoHandle');
if (is_callable($columnInfoHandle)) {
    $columnInfoHandle($column);
} else {
    $this->info('未设置表结构处理函数。设置方式见readme.md');
}
...
````