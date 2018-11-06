<?php

namespace Core\Http;

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