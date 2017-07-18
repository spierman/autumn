<?php
namespace Smiler\Autumn\Util;

use \zpt\anno\Annotations;

/**
 *
 * ription
 *
 * @author fuyou <fuyou@yourmall.com>
 * @copyright liequ Jul 15, 2017 10:26:54 AM
 */
class AnnotationReader
{

    private $supportPropertiesAnnotationName = array(
        'database',
        'autowired'
    );

    private $apiDir = '';

    private $controllerAnnotation = array();

    private $modelReflection = array();

    private $controllerReflection;

    public function __construct($apiDir)
    {
        $this->apiDir = $apiDir;
        $this->setClassReflection();
    }

    /**
     *
     * @param unknown $dir            
     * @param unknown $result            
     * @return unknown
     */
    private function getClassNameArr($dir, &$result)
    {
        $handle = opendir($dir);
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($cur_path)) {
                        $this->getClassNameArr($cur_path, $result);
                    } else {
                        $pos = strrpos($cur_path, '/');
                        $len = strlen($cur_path);
                        $fileName = substr($cur_path, $pos + 1, $len);
                        $fileNameArr = explode('.', $fileName);
                        $result[] = $fileNameArr[0];
                    }
                }
            }
            closedir($handle);
        }
        return $result;
    }

    public function setClassReflection()
    {
        $this->getClassNameArr($this->apiDir, $classArr);
        foreach ($classArr as $class) {
            $classReflector = new \ReflectionClass($class);
            $controllerAnnotation = $this->parseControllerAnnaotation($classReflector);
            if ($controllerAnnotation) {
                $this->controllerReflection = $classReflector;
                $controllerPath = $controllerAnnotation['path'];
                if (in_array($controllerPath, array_keys($this->controllerAnnotation))) {
                    throw new \Exception("path {$controllerPath} has existed");
                }
                unset($controllerAnnotation['path']);
                $this->controllerAnnotation[$controllerPath] = $controllerAnnotation;
            }
            // if (! $controllerRefelection) {
            // $modelRefelection = $this->pasrseModelAnnotation($classReflector);
            // $modelName = $modelRefelection['model'];
            // if (in_array($modelName, array_keys($this->modelReflection))) {
            // throw new \Exception("model {$modelName} has existed");
            // }
            // unset($modelRefelection['model']);
            // $this->modelReflection[$modelRefelection['model']] = $modelRefelection;
            // }
        }
    }

    /**
     *
     * @param unknown $classReflector            
     * @throws \Exception
     * @return multitype:
     */
    private function pasrseModelAnnotation($classReflector)
    {
        $classAnnotations = new Annotations($classReflector);
        $classAnnotationsArr = $classAnnotations->asArray();
        $model = isset($classAnnotationsArr['model']) ? $classAnnotationsArr['model'] : '';
        if (! $model) {
            return array();
        }
        $reflections = array(
            'model' => $model
        );
        foreach ($classReflector->getProperties() as $properties) {
            $propertyAnnotation = new Annotations($properties);
            $propertyName = $properties->getName();
            $propertyAnnotationArr = $propertyAnnotation->asArray();
            $annotationNameArr = array_keys($propertyAnnotationArr);
            foreach ($annotationNameArr as $annotationName) {
                if (! in_array($annotationName, $this->supportPropertiesAnnotationName)) {
                    throw new \Exception('unsupported property annotation name');
                }
                $reflections['properties'][$propertyName][$annotationName] = json_decode($propertyAnnotationArr[$annotationName], true);
            }
        }
        $this->modelReflection = $reflections;
    }

    /**
     *
     * @param unknown $classReflector            
     * @throws Exception
     * @return multitype:|Ambigous <mixed, multitype:NULL mixed multitype:unknown Ambigous <multitype:, multitype:multitype: > >
     */
    private function parseControllerAnnaotation($classReflector)
    {
        $classAnnotations = new Annotations($classReflector);
        $classAnnotationsArr = $classAnnotations->asArray();
        $controller = isset($classAnnotationsArr['controller']) ? 1 : 0;
        if (! $controller) {
            return array();
        }
        $classPath = $classAnnotationsArr['path'];
        $reflections = array(
            'path' => $classPath,
            'class' => $classReflector->getName()
        );
        foreach ($classReflector->getMethods() as $methodReflector) {
            $methonAnnotation = new Annotations($methodReflector);
            $methodName = $methodReflector->getName();
            $methodAnnotationArr = $methonAnnotation->asArray();
            if (! isset($methodAnnotationArr['route'])) {
                continue;
            }
            $routeArr = json_decode($methodAnnotationArr['route'], true);
            $methodPath = isset($routeArr['path']) ? $routeArr['path'] : '';
            $reflections['method_path'] = array(
                $methodPath => $methodName
            );
            $reflections['http_method'] = $routeArr['method'];
        }
        foreach ($classReflector->getProperties() as $properties) {
            $propertyAnnotation = new Annotations($properties);
            $propertyName = $properties->getName();
            $propertyAnnotationArr = $propertyAnnotation->asArray();
            $annotationNameArr = array_keys($propertyAnnotationArr);
            foreach ($annotationNameArr as $annotationName) {
                if (! in_array($annotationName, $this->supportPropertiesAnnotationName)) {
                    throw new Exception('unsupported property annotation name');
                }
                $reflections['properties'][$propertyName][$annotationName] = json_decode($propertyAnnotationArr[$annotationName], true);
            }
        }
        return $reflections;
    }

    /**
     *
     * @return Ambigous <multitype:, \Smiler\Autumn\Util\multitype:, \Smiler\Autumn\Util\Ambigous>
     */
    public function getControllerAnnotation()
    {
        return $this->controllerAnnotation;
    }

    /**
     *
     * @return \ReflectionClass
     */
    public function getControllerReflection()
    {
        return $this->controllerReflection;
    }
}
