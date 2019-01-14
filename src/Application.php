<?php

use Core\Container;
use Core\Exception\HttpNotFoundException;
use Core\Exception\ResourceNotFoundException;
use Core\Exception\RuntimeException;
use Core\Exception\UnavailableMethodException;
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
     * @return mixed|void
     * @throws Exception
     */
    private function run()
    {
        try {
            $this->checkRoute();
            $this->checkRouteMethod();
            $this->checkControllerFile();
            $this->checkCallable();
            $controller = new \ReflectionClass($this->router->getRoute()->getController());
            $params     = $this->di($this->router->getRoute()->getController(), $this->router->getRoute()->getAction());
            $class      = $controller->newInstanceArgs($this->getMethodParams($controller));

            $response = \call_user_func_array([$class, $this->router->getRoute()->getAction()], $params);
            if (!($response instanceof ResponseInterface)) {
                throw new RuntimeException('controller methods must return instance of ResponseInterface', 500);
            }
            return $response->send();
        } catch (\Throwable $t) {
            Logger::logToFile($t->getCode() . ': ' . $t->getMessage());
            return Response::create($t->getMessage(), $t->getCode())->send();
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
        $method = new \ReflectionMethod($controller, $action);
        $params = $method->getParameters();
        if (empty($params)) {
            return [];
        }
        foreach ($params as $param) {
            if (!$param->getType()->isBuiltin()) {
                if (!\is_callable($param->getType()->getName(), '__constructor')) {
                    throw new RuntimeException('parameter is not callable!', 500);
                }
                if (null === $this->container->get($param->getType()->getName())) {
                    $this->container->set($param->getType()->getName());
                }
                $result[$param->getPosition()] = $this->container->get($param->getType()->getName());
                $result = \array_merge($result, $this->router->getParams());
            }
        }
        return $result;
    }

    /**
     * checkControllerFile
     * @throws ResourceNotFoundException
     */
    private function checkControllerFile(): void
    {
        if (!\file_exists($this->getControllerPath())) {
            throw new ResourceNotFoundException('file doesnot found', 404);
        }
    }

    /**
     * checkRouteMethod
     * @throws UnavailableMethodException
     */
    private function checkRouteMethod(): void
    {
        if ($this->router->getRoute()->getMethod() !== $this->request->server()->getMethod()) {
            throw new UnavailableMethodException('this method is not allowed here', 500);
        }
    }

    /**
     * checkRoute
     * @throws HttpNotFoundException
     */
    private function checkRoute(): void
    {
        if (empty($this->router->getRoute())) {
            throw new HttpNotFoundException('404 not found', 404);
        }
    }

    /**
     * checkCallable
     * @throws RuntimeException
     */
    private function checkCallable(): void
    {
        if (!\is_callable($this->router->getRoute()->getController(), $this->router->getRoute()->getAction())) {
            throw new RuntimeException('controller is not callable!', 500);
        }
    }

    /**
     * @param \ReflectionClass $ref
     *
     * @return array
     * @throws ReflectionException
     */
    private function getMethodParams(\ReflectionClass $ref): array
    {
        $resolveConstructorParams = [];
        if (!empty($ref->getConstructor())) {
            $resolveConstructorParams = $this->di($this->router->getRoute()->getController(), $ref->getConstructor()->getName());
        }
        return $resolveConstructorParams;
    }
}