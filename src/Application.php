<?php

use Core\Container;
use Core\Logger;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\ResponseInterface;
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
            if (empty($this->router->getRoute())) {
                Response::create('404 not found', 404)->send();
                return false;
            }

            if ($this->router->getRoute()->getMethod() !== $this->request->server()->getMethod()) {
                throw new \Exception('this method is not allowed here');
            }

            if (!\file_exists($this->getControllerPath())) {
                throw new \Exception('file doesnot found');
            }

            if (!\is_callable($this->router->getRoute()->getController(), $this->router->getRoute()->getAction())) {
                throw new \Exception('controller is not callable!');
            }

            $ref = new ReflectionClass($this->router->getRoute()->getController());
            $resolveConstructorParams = [];
            if (!empty($ref->getConstructor())) {
                $resolveConstructorParams = $this->di($this->router->getRoute()->getController(), $ref->getConstructor()->getName());
            }

            $params = $this->di($this->router->getRoute()->getController(), $this->router->getRoute()->getAction());
            $class  = $ref->newInstanceArgs($resolveConstructorParams);

            $response = \call_user_func_array([$class, $this->router->getRoute()->getAction()], $params);
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
     * @param $controller
     * @param $action
     *
     * @return array
     * @throws ReflectionException
     * @throws Exception
     */
    private function di(string $controller, string $action): array
    {
        $result = [];
        $method = new ReflectionMethod($controller, $action);
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
                $result[$param->getPosition()] = $this->container->get($param->getType()->getName());
                $result = array_merge($result, $this->router->getParams());
            }
        }
        return $result;
    }
}