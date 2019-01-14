<?php

namespace Core\Exception;

use Throwable;

/**
 * Class RuntimeException
 * @package Core\Exception
 */
class RuntimeException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}