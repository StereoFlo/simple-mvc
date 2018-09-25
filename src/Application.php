<?php

use Core\Logger;
use Core\Request\Request;
use Core\Response\Response;
use Core\Response\ResponseInterface;
use Core\Router\Router;


/**
 * Class Autoloader
 */
class Application
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Router
     */
    private $router;

    /**
     * Modes
     */
    const MODE_API = 1, MODE_WEB = 2;

    /**
     * Static call
     *
     * @param Request $request
     * @param Router                $router
     *
     * @return Application
     * @throws Exception
     */
    public static function create(Request $request, Router $router)
    {
        return new self($request, $router);
    }

    /**
     * Autoloader constructor.
     *
     * @param Request $request
     * @param Router  $router
     *
     * @throws Exception
     */
    private function __construct(Request $request, Router $router)
    {
        $this->request = $request;
        $this->router  = $router;
        return $this->run();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function run()
    {
        try {
            if ($this->router->getRoute()->getMethod() !== $this->request->server()->getMethod()) {
                throw new \Exception('this method is not allowed here');
            }

            if (!\file_exists($this->getControllerPath())) {
                throw new \Exception('file doesnot found');
            }

            if (!\is_callable($this->router->getRoute()->getController(), $this->router->getRoute()->getMethod())) {
                throw new \Exception('controller is not callable!');
            }

            $response = \call_user_func_array([$this->router->getRoute()->getController(), $this->router->getRoute()->getAction()], $this->router->getParams());
            if (!($response instanceof ResponseInterface)) {
                throw new \Exception('controller methods must return instance of ResponseInterface');
            }

            return $response->send();

        } catch (\Exception $e) {
            Logger::logToFile($e->getCode() . ': ' . $e->getMessage());
            Response::create('service unavailable', 500)->send();
            return false;
        } catch (\Throwable $t) {
            Logger::logToFile($t->getCode() . ': ' . $t->getMessage());
            Response::create('service unavailable', 500)->send();
            return false;
        }
    }

    /**
     * @return string
     */
    private function getControllerPath(): string
    {
        return SRC_DIR . DS . str_replace('\\', '/', $this->router->getRoute()->getController()) . PHP_EXTENSION;
    }
}