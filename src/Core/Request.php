<?php

namespace Core;

use Core\Request\Bag;

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
     * @param array $currentArray
     *
     * @return Bag
     */
    private function getBag(array $currentArray): Bag
    {
        return new Bag($currentArray);
    }
}