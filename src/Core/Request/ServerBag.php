<?php


namespace Core\Request;

use App\Utils;

/**
 * Class ServerBag
 * @package Core\Request
 */
class ServerBag extends Bag
{
    /**
     * @return string
     */
    public function getMethod(): string
    {
        return \strtolower(Utils::getProperty($this->stack, 'REQUEST_METHOD', ''));
    }
}