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
    }

    /**
     * applyContentType
     */
    public function applyContentType(): void
    {
        $this->applyHeader('Content-Type: ' . static::$contentType . '; charset=' . static::$charset);
        static::$contentType = null;
    }

    /**
     * @param bool $noCache
     */
    public function applyNoCache($noCache = false)
    {
        if ($noCache || static::$isNoCache) {
            $this->applyHeader('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $this->applyHeader('Pragma: no-cache');
            static::$isNoCache = null;
        }
    }

    /**
     * sends the data
     * @return void
     */
    public function send(): void
    {
        $this->applyContentType();
        $this->applyNoCache(true);
        print static::$data;
        return;
    }

    /**
     * Применяет http заголовок
     *
     * @param string $header
     * @param bool   $replace
     *
     * @return bool
     */
    protected function applyHeader(string $header, bool $replace = true): bool
    {
        if (\headers_sent()) {
            return false;
        }
        \header($header, $replace, static::$httpCode);
        return true;
    }
}