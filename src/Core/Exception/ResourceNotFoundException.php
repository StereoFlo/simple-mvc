<?php

namespace Core\Exception;

use Throwable;

/**
 * Class ResourceNotFoundException
 * @package Core\Exception
 */
class ResourceNotFoundException extends \Exception
{
    /**
     * ResourceNotFoundException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}