<?php

namespace Core\Http;

use Core\Mime;

/**
 * Class Response
 * @package Core
 */
class Response implements ResponseInterface
{
    const DEFAULT_CHARSET = 'UTF-8';

    /**
     * @var int
     */
    protected static $httpCode = 200;

    /**
     * @var string
     */
    protected static $contentType;

    /**
     * @var string
     */
    protected static $charset;

    /**
     * @var bool
     */
    protected static $isNoCache = false;

    /**
     * @var mixed
     */
    protected static $data;

    /**
     * @param        $data
     * @param int    $httpCode
     * @param string $contentType
     * @param string $charset
     *
     * @return static
     */
    public static function create($data = null, $httpCode = 200, $contentType = Mime::HTML, $charset = self::DEFAULT_CHARSET)
    {
        return new static($data, $httpCode, $contentType, $charset);
    }

    /**
     * Response constructor.
     *
     * @param        $data
     * @param int    $httpCode
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($data = null, $httpCode = 200, $contentType = Mime::HTML, $charset = self::DEFAULT_CHARSET)
    {
        static::$data        = $data;
        static::$httpCode    = $httpCode;
        static::$contentType = $contentType;
        static::$charset     = $charset;
        $this->send();
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function prepare(Request $request)
    {
        if (\headers_sent()) {
            return $this;
        }
        if (!$request->headers()->has('Content-Type')) {
            $request->headers()->set('Content-Type', static::$contentType . '; charset=' . static::$charset);
        }

        if (static::$isNoCache && !$request->headers()->has('Cache-Control')) {
            $request->headers()->set('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $request->headers()->set('Pragma', 'no-cache');
        }

        foreach ($request->headers() as $name => $value) {
            header($name.': '.$value, false, static::$httpCode);
        }
        return $this;
    }

    /**
     * sends the data
     * @return void
     */
    public function send(): void
    {
        static::$isNoCache = true;
        $this->prepare(new Request());
        print static::$data;
        return;
    }
}