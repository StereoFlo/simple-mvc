<?php

namespace Core;

/**
 * Class Response
 * @package Core
 */
class Response
{
    /**
     * @var bool
     */
    private static $isHtmlResponse = true;

    /**
     * @var string
     */
    private static $contentType;

    /**
     * @var string
     */
    private static $charset;

    /**
     * @var bool
     */
    private static $isNoCache = false;

    /**
     * 404 error
     */
    public static function error404(): void
    {
        self::applyHeader('HTTP/1.0 404 Not Found', 404);
    }

    /**
     * 503 error
     */
    public static function error503(): void
    {
        self::applyHeader('HTTP/1.1 503 Service Temporarily Unavailable');
        self::applyHeader('Status: 503 Service Temporarily Unavailable');
        self::applyHeader('Retry-After: 300');
    }

    /**
     * 503 error
     */
    public static function error400(): void
    {
        self::applyHeader('HTTP/1.1 400 BAD REQUEST', 400);
    }

    /**
     * @param string $contentType
     * @param string $charset
     */
    public static function applyContentType($contentType = '', $charset = ''): void
    {
        if (empty($contentType)) {
            $contentType = self::$contentType;
        }
        if (empty($charset)) {
            $charset = self::$charset;
        }
        if (empty($contentType)) {
            return;
        }
        if ($charset) {
            $str = $contentType . '; charset=' . $charset;
        } else {
            $str = $contentType;
        }
        self::$isHtmlResponse = self::$contentType === Mime::HTML;
        self::applyHeader('Content-Type: ' . $str);
        self::$contentType = null;
    }

    /**
     * @param bool $noCache
     */
    public static function applyNoCache($noCache = false)
    {
        if ($noCache || self::$isNoCache) {
            self::applyHeader('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            self::applyHeader('Pragma: no-cache');
            self::$isNoCache = null;
        }
    }

    /**
     * @param $out
     *
     * @return string
     */
    public static function json($out): string
    {
        Response::applyContentType(Mime::JSON, 'utf-8');
        Response::applyNoCache(true);
        return \json_encode($out);
    }

    /**
     * Применяет http заголовок
     *
     * @param string $header
     * @param int    $code
     * @param bool   $replace
     *
     * @return bool
     */
    private static function applyHeader(string $header, int $code = null, bool $replace = true): bool
    {
        if (\headers_sent()) {
            return false;
        }
        \header($header, $replace, $code);
        return true;
    }
}