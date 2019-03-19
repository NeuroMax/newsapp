<?php

use App\Modules\Admin\AdminModule;
use App\Modules\Site\SiteModule;
use App\Services\Router\Router;
use App\Services\ORM;
use App\Container;
use App\ModuleManager;
use App\Module;

class App
{
    private $container;
    private $config;

    function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function bootstrap ()
    {
        $this->registerServices();
        $this->registerModules();
        /** @var Router $router */
        $router = $this->container->get('Router');
        $router->start();
    }

    public function getContainer ()
    {
        return $this->container;
    }

    private function registerServices ()
    {
        $this->container->push('Router', new Router($this->container));
    }

    private function registerModules ()
    {
        $mManager = new ModuleManager();
        $mManager->set('Admin', new AdminModule($this->container));
        $mManager->set('Site', new SiteModule($this->container));
        $mManager->loadModules();
        $this->container->push('ModuleManager', $mManager);
    }

    private static function classLoader ($cName)
    {
        $className = str_replace('\\', DIRECTORY_SEPARATOR, $cName);
        require_once BASE_DIR."/$className.php";
    }

    public static function init ()
    {
        spl_autoload_register(['static','classLoader']);
        $container = new Container();
        return new App($container);
    }
}