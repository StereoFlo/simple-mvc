<?php

use Core\Container;
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
     * @var Container
     */
    private $container;

    /**
     * Modes
     */
    const MODE_API = 1, MODE_WEB = 2;

    /**
     * Static call
     *
     * @param Request   $request
     * @param Router    $router
     * @param Container $container
     *
     * @return Application
     * @throws Exception
     */
    public static function create(Request $request, Router $router, Container $container)
    {
        return new self($request, $router, $container);
    }

    /**
     * Application constructor.
     *
     * @param Request   $request
     * @param Router    $router
     * @param Container $container
     *
     * @throws Exception
     */
    private function __construct(Request $request, Router $router, Container $container)
    {
        $this->request   = $request;
        $this->router    = $router;
        $this->container = $container;
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

            if (!\is_callable($this->router->getRoute()->getController(), $this->router->getRoute()->getAction())) {
                throw new \Exception('controller is not callable!');
            }

            $params1 = $this->di();
            $class = $this->router->getRoute()->getController();
            $class = new $class;

            $response = \call_user_func_array([$class, $this->router->getRoute()->getAction()], $params1);
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

    /**
     * @return array
     * @throws ReflectionException
     * @throws Exception
     */
    private function di(): array
    {
        $params1 = [];
        $method = new ReflectionMethod($this->router->getRoute()->getController(), $this->router->getRoute()->getAction());
        $params = $method->getParameters();
        if (empty($params)) {
            return [];
        }
        foreach ($params as $param) {
            if (!$param->getType()->isBuiltin()) {
                if (!\is_callable($param->getType()->getName(), '__constructor')) {
                    throw new \Exception('parameter is not callable!');
                }
                if (null === $this->container->get($param->getType()->getName())) {
                    $this->container->set($param->getType()->getName());
                }
                $params1[$param->getPosition()] = $this->container->get($param->getType()->getName());
                $params1 = array_merge($params1, $this->router->getParams());
            }
        }
        return $params1;
    }
}