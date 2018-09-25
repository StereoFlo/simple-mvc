<?php


namespace Core\Response;

use Core\Mime;

/**
 * Class JsonResponse
 * @package Core\Response
 */
class JsonResponse extends Response
{
    /**
     * default content type
     * @var string
     */
    protected static $contentType = Mime::JSON;

    /**
     * sends the data
     */
    public function send(): void
    {
        static::$contentType = Mime::JSON;
        $this->applyContentType();
        $this->applyNoCache(true);
        print \json_encode(static::$data);
        return;
    }
}