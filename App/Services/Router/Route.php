<?php

namespace App\Services\Router;

class Route {
    /** @var string  */
    private $method;
    /** @var string  */
    private $path;
    /** @var string  */
    private $controller;
    /** @var string  */
    private $action;
    /** @var array */
    private $mask;
    /** @var string */
    private $module;

    public function __construct(string $method, string $path, string $module, string $controllerAction)
    {
        $this->method = $method;
        $this->module = $module;
        $this->path = $path;

        $this->parseControllerAction($controllerAction);

        $this->get_path_mask();
    }

    private function parseControllerAction (string $ca)
    {
        if (false === stripos($ca, ':'))
            throw new \Exception('Controller Action string not valid');

        $arr = explode(':', $ca);
        $this->controller = $arr[0];
        $this->action = $arr[1];
    }

    private function get_path_mask ()
    {
        $f = function ($val) {
            if (false !== stripos($val, ':')) return '?';
            return $val;
        };

        $sections = array_values(array_filter(explode('/', $this->path)));
        $this->mask = array_map($f, $sections);
    }

    public function getMethod ()
    {
        return $this->method;
    }

    public function getPath ()
    {
        return $this->path;
    }

    public function getController ()
    {
        return $this->controller;
    }

    public function getAction ()
    {
        return $this->action;
    }

    public function getMask ()
    {
        return $this->mask;
    }

    public function getModule ()
    {
        return $this->module;
    }
}