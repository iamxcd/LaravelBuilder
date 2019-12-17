<?php

namespace SongBai\LaravelBuilder\Commands;

use Illuminate\Console\Command;

class LbCommand extends Command
{
    protected $name = 'lb';

    protected $description = 'laravel 代码生成器';

    private $menu = [];

    public function __construct()
    {
        parent::__construct();

        $this->menu = [
            '表单验证' => 'lb:request'
        ];
    }

    public function handle()
    {
        $defaultIndex = 0;
        $name = $this->choice('选择需要创建的类型 ?', array_keys($this->menu), $defaultIndex);
        $this->call($this->menu[$name]);
    }
}
