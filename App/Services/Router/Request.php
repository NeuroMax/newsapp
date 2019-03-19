<?php


namespace App\Services\Router;


/**
 * Class Request
 * @package App\Services\Router
 */
class Request
{

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /** @var string  */
    private $method = '';
    /** @var array  */
    private $headers = [];
    /** @var array  */
    private $params = [];
    /** @var array  */
    private $query = [];
    /** @var array  */
    private $body = [];
    /** @var string  */
    private $url = '';
    /** @var Route */
    private $route;

    /**
     * Request constructor.
     * @param Route $route
     */
    function __construct(Route $route)
    {
        $this->route = $route;

        $this->setUrl();
        $this->setMethod();
        $this->setHeaders();
        $this->setParams();
        $this->setQuery();
        $this->setBody();
    }

    /**
     *  Url
     */
    private function setUrl ()
    {
        $this->url = parse_url($_SERVER['REQUEST_URI'])['path'];
    }

    /**
     * Request method
     */
    private function setMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Request headers
     */
    private function setHeaders()
    {
        $this->headers = getallheaders();
    }

    /**
     * Route params
     * Вытаскивает параматры маршрута
     */
    private function setParams()
    {
        $path_arr = array_values(array_filter(explode('/', $this->route->getPath())));
        $url_arr = array_values(array_filter(explode('/', $this->url)));

        if (count($path_arr) !== count($url_arr)) return;

        foreach ($this->route->getMask() as $key => $val) {
            if ($val !== '?') continue;

            $param = ltrim($path_arr[$key], ':');
            $this->params[$param] = $url_arr[$key];
        }
    }

    /**
     * Request query
     */
    private function setQuery()
    {
        $this->query = $_GET ?? [];
    }

    /**
     * Request body
     */
    private function setBody()
    {
        if ($this->method != self::METHOD_POST || $this->route->getMethod() != self::METHOD_POST) return;
        if(!isset($_POST) || empty($_POST)) return;

        $this->body = $_POST;
    }

    // Public methods

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }
}