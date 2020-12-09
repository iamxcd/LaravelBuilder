<?php

namespace SongBai\LaravelBuilder\Commands;

use File;
use Illuminate\Console\Command;
use ReflectionClass;

class CreateDicFilCommand extends Command
{
    protected $signature = 'lb:CreateDicFile';
    protected $description = '创建字典文件';

    public function handle()
    {

        $path = app_path('Models');
        $models = $this->getModels($path);
        $dic =  $this->getDicData($models);
        $content = "<?php\r\n\r\nreturn " . var_export($dic, true) . ';';
        file_put_contents(config_path('dictionary.php'), $content);
    }



    private function getModels($path)
    {
        $out = [];
        $results = File::allFiles($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;


            $filename = $result->getRelativePathName();
            if (is_dir($filename)) {
                $out = array_merge($out, $this->getModels($filename));
            } else {
                $class = sprintf(
                    '\%s%s%s',
                    app()->getNamespace(),
                    'Models\\',
                    strtr(substr($filename, 0, strrpos($filename, '.')), '/', '\\')
                );

                $out[] = $class;
            }
        }
        return $out;
    }

    public function getDicData($models)
    {
        $data = [];
        foreach ($models as  $class) {
            $dic = $this->getClassDic($class);
            if (count($dic) > 0) {
                $data[] = $dic;
            }
        }

        return $data;
    }

    private function getClassDic($class_name)
    {
        $class = new ReflectionClass($class_name);
        $dic = [];
        foreach ($class->getConstants() as $key => $value) {
            $doc = $class->getReflectionConstant($key)->getDocComment();

            $regx = "/(@group).*?(.*?)\n.*?(@label).*?(.*?)\n/";
            preg_match($regx, $doc, $res);
            if (count($res) != 5) {
                continue;
            }
            $group = trim($res[2]);
            $label = trim($res[4]);
            $dic[$group][] =  [
                'value' => $value,
                'label' => $label
            ];
        }
        if (count($dic) == 0) {
            return [];
        }
        return [
            'model' => $class->getShortName(),
            'dic' => $dic
        ];
    }
}
