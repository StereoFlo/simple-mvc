<?php

namespace Core\Http;

/**
 * Class Input
 * @package Core
 */
class Request
{
    /**
     * @var Bag
     */
    protected $request;

    /**
     * @var Bag
     */
    protected $query;

    /**
     * @var Bag
     */
    protected $files;

    /**
     * @var Bag
     */
    protected $server;

    /**
     * @var Bag
     */
    protected $cookie;

    /**
     * @var Bag
     */
    protected $headers;

    /**
     * @return Request
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->request = new Bag($_POST);
        $this->query   = new Bag($_GET);
        $this->files   = new Bag($_FILES);
        $this->server  = new ServerBag($_SERVER);
        $this->cookie  = new Bag($_COOKIE);
        $this->headers = new Bag($this->server->getHeaders());
    }

    /**
     * @return Bag
     */
    public function request(): Bag
    {
        return $this->request;
    }

    /**
     * @return Bag
     */
    public function query(): Bag
    {
        return $this->query;
    }

    /**
     * @return Bag
     */
    public function files(): Bag
    {
        return $this->files;
    }

    /**
     * @return ServerBag
     */
    public function server(): ServerBag
    {
        return $this->server;
    }

    /**
     * @return Bag
     */
    public function getCookie(): Bag
    {
        return $this->cookie;
    }
}