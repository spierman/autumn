<?php
namespace Smiler\Autumn;

use Smiler\Autumn\Util\AnnotationReader;

class Router
{

    private $annotationReader;

    private $confFile;

    public function __construct($apiDir)
    {
        $this->annotationReader = new AnnotationReader($apiDir);
    }

    public function setConfFile($confFile)
    {
        $this->confFile = $confFile;
    }

    public function execute()
    {
        $requestUrl = $_SERVER['REQUEST_URI'];
        $requestArr = explode('/', $requestUrl);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $controllerAnnotation = $this->annotationReader->getControllerAnnotation();
        $controllerReflection = $this->annotationReader->getControllerReflection();
        $classPath = '/' . $requestArr[1];
        $arr = $controllerAnnotation[$classPath];
        $methodPath = str_replace($classPath, '', $requestUrl);
        if (! $methodPath) {
            $methodPath = '/';
        }
        $methodPathArr = $arr['method_path'];
        $class = $arr['class'];
        $method = $methodPathArr[$methodPath];
        $httpMethodArr = $arr['http_method'];
        if (! in_array($requestMethod, $httpMethodArr)) {
            throw new \Exception('http method not allowed');
        }
        $properties = $arr['properties'];
        $int = $controllerReflection->newInstance();
        $this->autowiredProperties($properties, $int, $controllerReflection);
        $int->$method();
    }

    public function autowiredProperties($properties, $int, $controllerReflection)
    {
        $confData = json_decode(file_get_contents($this->confFile), true);
        foreach ($properties as $propertyName => $propertyArr) {
            foreach ($propertyArr as $propertyAnnotationName => $property) {
                $name = isset($property['name']) ? $property['name'] : '';
                if ($name) {
                    $className = \JmesPath\search("{$propertyAnnotationName}.{$name}", $confData);
                    $classProperty = $controllerReflection->getProperty($propertyName);
                    $classProperty->setAccessible(true);
                    $classProperty->setValue($int, new $className());
                }
            }
        }
    }
}
