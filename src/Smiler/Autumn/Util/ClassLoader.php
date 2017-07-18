<?php
namespace Smiler\Autumn\Util;

/**
 * class loader
 */
class ClassLoader
{

    /**
     *
     * @param unknown $path            
     */
    public static function addInclude($path)
    {
        if (is_array($path)) {
            self::$includes = array_unique(array_merge(self::$includes, $path));
        } else {
            self::$includes[] = $path;
            self::$includes = array_unique(self::$includes);
        }
    }

    /**
     * autoLoad
     *
     * @param unknown $classname            
     * @return void
     */
    public static function autoLoad($classname)
    {
        foreach (self::$includes as $path) {
            self::getFilePathArr($path, $filePathArr);
            foreach ($filePathArr as $filePath) {
                if (file_exists($filePath)) {
                    include_once $filePath;
                }
            }
        }
    }

    private static function getFilePathArr($dir, &$result)
    {
        $handle = opendir($dir);
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($cur_path)) {
                        self::getFilePathArr($cur_path, $result);
                    } else {
                        $result[] = $cur_path;
                    }
                }
            }
            closedir($handle);
        }
        return $result;
    }

    public static $includes = array();
}