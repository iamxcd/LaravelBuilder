## laravel 代码生成

>重写了artisan make 一部分命令,使之在项目中更灵活

### 安装
```
composer require songbai/laravel-builder --save
```

### 使用
```
php artisan lb // 选择使用功能
php artisan lb:request // 验证器
php artisan lb:t2m //已有表格生成模型
```

### 功能
- [x] 表单验证 
- [x] 已有表格批量生成模型、控制器、验证器
- [ ] 控制器 
- [ ] 模型
- [ ] 资源控制器

### 自定义模板

```
<?php
// 将需要自定义的模板复制一份 stub-path 指定的目录

return [
    'stub-path' => resource_path('stub')
];
```