<?php

namespace Core\Response;

/**
 * Interface ResponseInterface
 * @package Core\Response
 */
interface ResponseInterface
{
    /**
     * sends the data to a browser
     */
    public function send(): void ;
}