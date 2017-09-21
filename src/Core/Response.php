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
    private static $_isHtmlResponse = true;

    /**
     * @var string
     */
    private static $_contentType;

    /**
     * @var string
     */
    private static $_charset;

    /**
     * @var bool
     */
    private static $_isNoCache = false;

    /**
     * 404 error
     */
    public static function error404()
    {
        \header("HTTP/1.0 404 Not Found");
    }

    /**
     * 503 error
     */
    public static function error503()
    {
        \header('HTTP/1.1 503 Service Temporarily Unavailable');
        \header('Status: 503 Service Temporarily Unavailable');
        \header('Retry-After: 300');
    }

    /**
     * 503 error
     */
    public static function error400()
    {
        \header('HTTP/1.1 400 BAD REQUEST');
    }

    /**
     * @param string $contentType
     * @param string $charset
     */
    public static function applyContentType($contentType = '', $charset = '') {
        if (empty($contentType)) {
            $contentType = self::$_contentType;
        }
        if (empty($charset)) {
            $charset = self::$_charset;
        }
        if (empty($contentType)) {
            return;
        }
        if ($charset) {
            $str = $contentType . '; charset=' . $charset;
        } else {
            $str = $contentType;
        }
        self::$_isHtmlResponse = self::$_contentType === Mime::HTML;
        self::applyHeader('Content-Type: ' . $str);
        self::$_contentType = null;
    }

    /**
     * @param bool $noCache
     */
    public static function applyNoCache($noCache = false) {
        if ($noCache || self::$_isNoCache) {
            self::applyHeader('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            self::applyHeader('Pragma: no-cache');
            self::$_isNoCache = null;
        }
    }

    /**
     * Применяет http заголовок
     *
     * @param string $header
     * @param int $code
     * @param bool $replace
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