<?php
namespace Smiler\Autumn;

use Smiler\Autumn\Util\ClassLoader;
use Smiler\Autumn\Router;

class Bootstrap
{

    public static function run($confFile, $apiDir)
    {
        $err = null;
        try {
            ClassLoader::addInclude($apiDir);
            spl_autoload_register(array(
                __NAMESPACE__ . '\Util\ClassLoader',
                'autoLoad'
            ));
            $router = new Router($apiDir);
            $router->setConfFile($confFile);
            $router->execute();
        } catch (\Exception $e) {
            $err = $e;
        }
        if ($err) {
            echo $err;
        }
    }
}
