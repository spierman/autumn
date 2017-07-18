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
