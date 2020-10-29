## laravel 代码生成

>重写了artisan make 一部分命令,使之在项目中更灵活

### 安装
```
composer require songbai/laravel-builder --dev
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

### 最佳体验

需要配合另一个扩展包，导入增删改查功能。

将模板发布后，调整到适合自己的项目。

#### 根据功能模块建立模型的迁移
```bash
php artisan make:model user -m
```

注意：迁移文件每个字段记得加上备注。

#### 生成控制器和验证规则。

```bash
php artisan lb // 根据提示 填写模块名 不用加controller

php artisan lb:request user users // 生成验证规则文件，参数1 模块名 参数2 表名
// 以上将根据字段类型 为store,update 方法生成默认的验证规则。
// 控制器如有其他方法需要验证，新增一个 方法名+Rule的方法即可。
```

#### 创建路由
给刚添加的模块创建一个apiResource路由。


通过以上几个步骤，即可完成一个简单的增删改查接口。如有调整，重写该方法即可。