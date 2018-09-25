<?php

namespace Core;

/**
 * Class Container
 * @package Core
 */
class Container
{
    /**
     * @var array
     */
    protected $stack = [];

    /**
     * @param string $abstract
     *
     * @return Container
     */
    public function set(string $abstract): self
    {
        if (isset($this->stack[$abstract])) {
            return $this;
        }

        $this->stack[$abstract] = $abstract;
        return $this;
    }

    /**
     * @param       $abstract
     * @param array $parameters
     *
     * @return mixed|null|object
     * @throws \ReflectionException
     */
    public function get(string $abstract, array $parameters = [])
    {
        if (!isset($this->stack[$abstract])) {
            return null;
        }

        return $this->build($this->stack[$abstract], $parameters);
    }

    /**
     * @param $concrete
     * @param $parameters
     *
     * @return mixed|object
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function build(string $concrete, array $parameters)
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this, $parameters);
        }

        $reflector = new \ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return $reflector->newInstance();
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @param \ReflectionParameter[] $parameters
     *
     * @return array
     * @throws \Exception
     */
    private function getDependencies(array $parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (null === $dependency) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Can not be resolve class dependency {$parameter->name}");
                }
            } else {
                $dependencies[] = $this->get($dependency->name);
            }
        }

        return $dependencies;
    }
}