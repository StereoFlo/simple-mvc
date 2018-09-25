<?php

namespace Core\Request;

/**
 * Class Input
 * @package Core
 */
class Request
{
    /**
     * @return Request
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @return Bag
     */
    public function post(): Bag
    {
        return $this->getBag($_POST);
    }

    /**
     * @return Bag
     */
    public function get(): Bag
    {
        return $this->getBag($_GET);
    }

    /**
     * @return Bag
     */
    public function files(): Bag
    {
        return $this->getBag($_FILES);
    }

    /**
     * @return Bag
     */
    public function server(): Bag
    {
        return $this->getBag($_SERVER);
    }

    /**
     * @param array $currentArray
     *
     * @return Bag
     */
    private function getBag(array $currentArray): Bag
    {
        return new Bag($currentArray);
    }
}