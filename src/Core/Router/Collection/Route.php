<?php


namespace Core\Router\Collection;


class Route
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var  string
     */
    protected $method;

    /**
     * @var  string
     */
    protected $action;

    /**
     * @var string
     */
    protected $mode;

    /**
     * Route constructor.
     *
     * @param string $path
     * @param string $method
     * @param string $action
     * @param string $mode
     */
    public function __construct(string $path, string $method, string $action, string $mode)
    {
        $this->path   = $path;
        $this->method = $method;
        $this->action = $action;
        $this->mode   = $mode;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
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
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }
}