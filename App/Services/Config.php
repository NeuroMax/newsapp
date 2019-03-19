<?php
namespace App\Services;


use App\InvestedInterface;

/**
 * Class Config
 * @package App\Services
 */
class Config implements InvestedInterface
{
    /** @var object */
    private $conf;

    /**
     * Config constructor.
     * @throws \Exception
     */
    function __construct()
    {
        $path = __DIR__ . '/../Config/conf.json';
        if (!file_exists($path)) throw new \Exception('Not Config file');

        $file = file_get_contents($path);
        $this->conf = json_decode($file);
    }


    /**
     * @param string $str
     * @return bool|mixed|object
     */
    public function get (string $str) {
        $elements = explode(':', $str);
        if (!is_array($elements)) $elements = [$elements];
        $conf = $this->conf;

        foreach ($elements as $el) {
            $conf = $this->getParams($conf, $el);
        }

        return $conf;
    }

    /**
     * @param $conf
     * @param string $param
     * @return bool
     */
    private function getParams ($conf, string $param) {
        if (property_exists($conf, $param)) return $conf->$param;
        return false;
    }
}