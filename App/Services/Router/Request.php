<?php


namespace App\Services\Router;


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

    private function setUrl ()
    {
        $this->url = parse_url($_SERVER['REQUEST_URI'])['path'];
    }

    private function setMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    private function setHeaders()
    {
        $this->headers = getallheaders();
    }

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

    private function setQuery()
    {
        $this->query = $_GET ?? [];
    }

    private function setBody()
    {
        if ($this->method != self::METHOD_POST || $this->route->getMethod() != self::METHOD_POST) return;
        if(!isset($_POST) || empty($_POST)) return;

        $this->body = $_POST;
    }

    /** Public methods */

    public function getMethod()
    {
        return $this->method;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getBody()
    {
        return $this->body;
    }
}