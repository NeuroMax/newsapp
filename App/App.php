<?php

use App\Modules\Admin\AdminModule;
use App\Modules\Site\SiteModule;
use App\Services\Router\Router;
use App\Container;
use App\ModuleManager;
use \App\Services\Config;

/**
 * Class App
 */
class App
{
    /**
     * @var Container
     */
    private $container;

    /**
     * App constructor.
     * @param Container $container
     */
    function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Запуск приложения
     * @throws Exception
     */
    public function bootstrap ()
    {
        $this->registerServices();
        $this->registerModules();
        /** @var Router $router */
        $router = $this->container->get('Router');
        $router->start();
    }

    /**
     * @return Container
     */
    public function getContainer ()
    {
        return $this->container;
    }

    /**
     * Регистрация сервисов
     * @throws Exception
     */
    private function registerServices ()
    {
        $this->container->push('Router', new Router($this->container));
    }

    /**
     * Регистрация и загрузка модулей
     */
    private function registerModules ()
    {
        $mManager = new ModuleManager();
        $mManager->set('Admin', new AdminModule($this->container));
        $mManager->set('Site', new SiteModule($this->container));
        $mManager->loadModules();
        $this->container->push('ModuleManager', $mManager);
    }

    /**
     * Загрузчик классов
     * @param $cName
     */
    private static function classLoader ($cName)
    {
        $className = str_replace('\\', DIRECTORY_SEPARATOR, $cName);
        require_once BASE_DIR."/$className.php";
    }


    /**
     *  Инициализация
     * @return App
     */
    public static function init ()
    {
        spl_autoload_register(['static','classLoader']);
        $container = new Container();
        return new App($container);
    }
}