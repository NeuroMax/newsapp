<?php
namespace App\Services\Router;

use App\Authenticator;
use App\Container;
use App\InvestedInterface;
use App\Module;
use App\ModuleManager;

/**
 * Роутер
 * Class Router
 * @package App\Services\Router
 */
class Router implements InvestedInterface
{
    /** @var Container */
    private $container;
    /** @var array  */
    private $routes = [];
    /** @var Request */
    private $request;
    /** @var string  */
    private $url = '';

    /**
     * Router constructor.
     * @param Container $container
     */
    function __construct(Container $container)
    {
        $this->container = $container;
        $this->setUrl();
    }

    /**
     * Запуск роутера
     * @throws \Exception
     */
    public function start (): void
    {
        // Получение объекта маршрута
        if (!$route = $this->determineMatchingRoute())
        {
            // Если не найден редиректит на 404 страницу
            $this->redirect('404');
        }

        // Проверка на наличие токена в куках
        $this->checkToken($route);

        /** @var ModuleManager $moduleManager */
        $moduleManager = $this->container->get('ModuleManager');

        // Получение объекта модуля
        /** @var Module $module */
        $module = $moduleManager->get($route->getModule());

        if (!$module)
            throw new \Exception('Not found module', 500);

        // Запуск контроллера в модуле
        $module->dispatch($route);
    }

    /**
     * Функция ложит в массив объект маршрута
     * @param Route $route
     */
    public function registerRoute (Route $route): void
    {
        if (!$this->has($route))
            $this->routes[] = $route;
    }

    /**
     * Функция проверяет есть ли уже в массиве маршрут
     * @param Route $route
     * @return bool
     */
    private function has (Route &$route): bool
    {
        /** @var Route $r */
        foreach ($this->routes as $r)
        {
            if ($route->getPath() === $r->getPath() && $route->getMethod() === $r->getMethod()) return true;
        }
        return false;
    }

    /**
     * Функция находить маршрут по текущему URL и возвращает его если найдет
     * @return mixed|null
     */
    private function determineMatchingRoute ()
    {
        foreach ($this->routes as $route)
        {
            if($_SERVER['REQUEST_METHOD'] != $route->getMethod()) continue;

            $urlSections = array_values(array_filter(explode('/', $this->url)));
            if (count($route->getMask()) != count($urlSections)) continue;
            if (empty($route->getMask()) && empty($urlSections)) return $route;

            $res = true;
            for ($i = 0; $i < count($route->getMask()); $i++)
            {
                if ($route->getMask()[$i] != '?' && $route->getMask()[$i] != $urlSections[$i]) $res = false;
            }

            if ($res) return $route;
        }
        return null;
    }

    /**
     * Функция проверяет наличие токена в куках
     * @param Route $route
     * @throws \Exception
     */
    private function checkToken (Route $route)
    {
        // Если в опциях маршрута включена проверка то проверяем
        if (isset($route->getOptions()['auth']) && true === $route->getOptions()['auth'])
        {
            $token = $_COOKIE['token'] ?? false;

            // Если нет токена или он не действительный то редирект на страницу авторизации
            if (!$token || !Authenticator::validate($token)) $this->redirect('admin/signIn');
        }

        return;
    }

    /**
     *
     */
    private function setUrl ()
    {
        $this->url = parse_url($_SERVER['REQUEST_URI'])['path'];
    }

    /**
     * Редирект
     * @param string $path
     */
    private function redirect (string $path)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
            ."://" . $_SERVER['HTTP_HOST'] . "/$path";
        header("Location: $url");
        die();
    }
}