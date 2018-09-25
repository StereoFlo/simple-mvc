<?php

use Core\Logger;
use Core\Response\Response;
use Core\Response\ResponseInterface;
use Core\Router\Router;


/**
 * Class Autoloader
 */
class Application
{
    private $router;

    /**
     * Modes
     */
    const MODE_API = 1, MODE_WEB = 2;

    /**
     * Static call
     *
     * @param Router $router
     *
     * @return Application
     * @throws Exception
     */
    public static function create(Router $router)
    {
        return new self($router);
    }

    /**
     * Autoloader constructor.
     *
     * @param Router $router
     *
     * @throws Exception
     */
    private function __construct(Router $router)
    {
        $this->router = $router;
        return $this->run();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function run()
    {
        try {
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
            Response::create('service unavailable', 500);
            return false;
        } catch (\Throwable $t) {
            Logger::logToFile($t->getCode() . ': ' . $t->getMessage());
            Response::create('service unavailable', 500);
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