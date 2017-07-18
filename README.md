# autumn
php annotation mvc
## Installation

Install the latest version with

```bash
$ composer require smiler/autumn
```

## Basic Usage

```php
<?php

/**
 * @controller
 * @path ("/hw")
 */
class HelloWorld
{

    /**
     * @autowired ({"name":"hello_world"})
     */
    private $helloWorldModel;

    /**
     * @route({"method":["GET","POST"],"path":"/"})
     */
    public function doSomething1()
    {
        $this->helloWorldModel->execute();
    }
}
```
## Documentation

### Requirements

- PHP > 5.4

### Author

spiderman - <spiderman1517650@163.com>

### License

Autumn is licensed under the MIT License - see the `LICENSE` file for details

### Acknowledgements

PHP Reflection
JsonPath
Class Annotation
