<?php

namespace App\Services\Router;

/**
 * Class Route
 * @package App\Services\Router
 */
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
    /** @var array */
    private $options;

    /**
     *  Конструктор маршрута
     * Route constructor.
     * @param string $method Request method Метод http запроса (POST, GET)
     * @param string $path Маршрут (например '/admin/news/:id') параметр писать с двоеточием
     * @param string $module Название модуля
     * @param string $controllerAction Название контроллера и экшена вида 'UserController:index'
     * @param array $options Опции (из опций доступно [ 'auth' => true ] Разрешение маршрута только авторизированным пользователям)
     * @throws \Exception
     */
    public function __construct(string $method, string $path, string $module, string $controllerAction, array $options = [])
    {
        $this->method = $method;
        $this->module = $module;
        $this->path = $path;
        $this->options = $options;

        $this->parseControllerAction($controllerAction);

        $this->get_path_mask();
    }

    /**
     * Разделяет контроллер и экшен
     * @param string $ca
     * @throws \Exception
     */
    private function parseControllerAction (string $ca)
    {
        if (false === stripos($ca, ':'))
            throw new \Exception('Controller Action string not valid');

        $arr = explode(':', $ca);
        $this->controller = $arr[0];
        $this->action = $arr[1];
    }

    /**
     * Создает маску маршрута
     */
    private function get_path_mask ()
    {
        $f = function ($val) {
            if (false !== stripos($val, ':')) return '?';
            return $val;
        };

        $sections = array_values(array_filter(explode('/', $this->path)));
        $this->mask = array_map($f, $sections);
    }

    /**
     * @return string
     */
    public function getMethod ()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath ()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getController ()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction ()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getMask ()
    {
        return $this->mask;
    }

    /**
     * @return string
     */
    public function getModule ()
    {
        return $this->module;
    }

    /**
     * @return array
     */
    public function getOptions ()
    {
        return $this->options;
    }
}