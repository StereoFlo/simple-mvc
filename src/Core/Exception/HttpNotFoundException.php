<?php

namespace Core\Exception;

/**
 * Class HttpNotFoundException
 * @package Core\Exception
 */
class HttpNotFoundException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}