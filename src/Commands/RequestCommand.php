<?php

namespace SongBai\LaravelBuilder\Commands;

use Illuminate\Console\GeneratorCommand;

class RequestCommand extends GeneratorCommand
{


    protected $name = 'lbc:request';

    protected $description = '创建一个请求验证器';
    protected $type = 'Request';


    protected function getStub()
    {
        $stub = 'request.stub';

        return __DIR__ . '/../../stubs/' . $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Requests';
    }
}
