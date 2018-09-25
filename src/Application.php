<?php

use Core\Logger;
use Core\Response;
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
     */
    public static function create(Router $router)
    {
        return new self($router);
    }

    /**
     * Autoloader constructor.
     *
     * @param Router $router
     */
    private function __construct(Router $router)
    {
        $this->router = $router;
        return $this->run();
    }

    /**
     * @return mixed
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
            if (!($response instanceof Response)) {
                throw new \Exception('controller methods must return instance of Response class');
            }

            switch ($this->router->getRoute()->getMode()) {
                case self::MODE_API:
                    return $response->json();
                case self::MODE_WEB:
                    return $response->html();
            }

        } catch (\Exception $e) {
            Logger::logToFile($e->getCode() . ': ' . $e->getMessage());
            Response::create(null)->error503();
            return false;
        } catch (\Throwable $t) {
            Logger::logToFile($t->getCode() . ': ' . $t->getMessage());
            Response::create(null)->error503();
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