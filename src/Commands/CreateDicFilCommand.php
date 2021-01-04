<?php

namespace SongBai\LaravelBuilder\Commands;

use File;
use Illuminate\Console\Command;
use ReflectionClass;

class CreateDicFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:CreateDicFile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建字典文件';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $path = app_path('Models');
        $models = $this->getModels($path);
        $dic =  $this->getDicData($models);

        $content = "<?php\r\n\r\nreturn " . var_export($dic, true) . ';';
        file_put_contents(config_path('dictionary.php'), $content);

        $dic =  $this->getDicKeyValData($models);
        $content = "<?php\r\n\r\nreturn " . var_export($dic, true) . ';';
        file_put_contents(config_path('kv-dictionary.php'), $content);
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

    public function getDicKeyValData($models)
    {
        $data = [];
        foreach ($models as  $class) {
            $dic = $this->getClassDic($class, true);
            if (count($dic) > 0) {
                $data[$dic['model']] = $dic['dic'];
            }
        }

        return $data;
    }

    private function getClassDic($class_name, $iskv = false)
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
            if (!$iskv) {
                $dic[$group][] =  [
                    'value' => $value,
                    'label' => $label
                ];
            } else {
                $dic[$group][$value] = $label;
            }
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
