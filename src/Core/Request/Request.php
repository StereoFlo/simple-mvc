<?php

namespace Core\Request;

/**
 * Class Input
 * @package Core
 */
class Request
{
    /**
     * @var Bag
     */
    protected $post;

    /**
     * @var Bag
     */
    protected $get;

    /**
     * @var Bag
     */
    protected $files;

    /**
     * @var Bag
     */
    protected $server;

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
        $this->post   = new Bag($_POST);
        $this->get    = new Bag($_GET);
        $this->files  = new Bag($_FILES);
        $this->server = new ServerBag($_SERVER);
    }

    /**
     * @return Bag
     */
    public function post(): Bag
    {
        return $this->post;
    }

    /**
     * @return Bag
     */
    public function get(): Bag
    {
        return $this->get;
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
}