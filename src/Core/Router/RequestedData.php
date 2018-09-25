<?php


namespace Core\Router;

use Core\Request\Bag;
use Core\Request\Request;

/**
 * Class RequestedData
 * @package Core\Router
 */
class RequestedData
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $uriOrig;

    /**
     * @var Bag
     */
    protected $server;

    /**
     * RequestedData constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->server = $request->server();
        $this->setMethod($this->server->get('REQUEST_METHOD'));
        $this->setUriOrig($this->server->get('REQUEST_URI'));
    }

    /**
     * @param string $method
     *
     * @return RequestedData
     */
    public function setMethod(string $method): RequestedData
    {
        $this->method = $method;

        return $this;
    }


    /**
     * @param string $uri
     *
     * @return RequestedData
     */
    public function setUri(string $uri): RequestedData
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @param string $uriOrig
     *
     * @return RequestedData
     */
    public function setUriOrig(string $uriOrig): RequestedData
    {
        $this->uriOrig = $uriOrig;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        if (false === strpos($this->uriOrig, '?')) {
            return $this->uriOrig;
        }

        $tmp = \explode('?', $this->uriOrig);
        $this->uri = $tmp[0];
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getUriOrig(): string
    {
        return $this->uriOrig;
    }
}