<?php

namespace App;

use App\Services\Config;
use App\Services\ORM;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;

class Controller
{
    private $twig;

    /** @var ORM */
    protected $orm;

    /** @var Config */
    protected $conf;

    function __construct(string $module)
    {
        $this->conf = new Config();
        $this->orm = ORM::class;
        $this->orm::setup($this->db_connect());
        $this->twig = $this->twig_register($module);
    }

    private function db_connect (): \mysqli
    {
        $mysqli = new \mysqli(
            $this->conf->get('db:host'),
            $this->conf->get('db:username'),
            $this->conf->get('db:password'),
            $this->conf->get('db:dbname')
        );

        if ($mysqli->connect_error)
        {
            die('Ошибка подключения (' . $mysqli->connect_errno . ') '
                . $mysqli->connect_error);
        }

        return $mysqli;
    }

    private function twig_register ($module): Environment
    {
        $loader = new FilesystemLoader(BASE_DIR."/App/Modules/$module/Views");
        $assetFunc = new TwigFunction('asset', function ($path) {
            return '/assets/'.$path;
        });

        $twig = new Environment($loader, [
//            'cache' => BASE_DIR.'/cache/',
            'auto_reload' => true
        ]);

        $twig->addFunction($assetFunc);

        return $twig;
    }

    protected function render (string $template, array $data = [])
    {
        $this->twig->display($template, $data);
    }

    protected function redirect (string $path)
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
            ."://" . $_SERVER['HTTP_HOST'] . "/$path";
        header("Location: $url");
    }
}