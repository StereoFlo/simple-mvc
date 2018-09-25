<?php

namespace Core;

/**
 * Class Response
 * @package Core
 */
class Response
{
    const DEFAULT_CHARSET = 'UTF-8';

    /**
     * @var bool
     */
    protected $isHtmlResponse = true;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var string
     */
    protected $charset;

    /**
     * @var bool
     */
    protected $isNoCache = false;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param        $data
     * @param string $contentType
     * @param string $charset
     *
     * @return Response
     */
    public static function create($data, $contentType = Mime::HTML, $charset = self::DEFAULT_CHARSET): self
    {
        return new self($data, $contentType, $charset);
    }

    /**
     * Response constructor.
     *
     * @param        $data
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($data, $contentType = Mime::HTML, $charset = self::DEFAULT_CHARSET)
    {
        $this->data        = $data;
        $this->contentType = $contentType;
        $this->charset     = $charset;
    }

    /**
     * 404 error
     */
    public function error404(): void
    {
        $this->applyHeader('HTTP/1.0 404 Not Found', 404);
    }

    /**
     * 503 error
     */
    public function error503(): void
    {
        $this->applyHeader('HTTP/1.1 503 Service Temporarily Unavailable');
        $this->applyHeader('Status: 503 Service Temporarily Unavailable');
        $this->applyHeader('Retry-After: 300');
    }

    /**
     * 503 error
     */
    public function error400(): void
    {
        $this->applyHeader('HTTP/1.1 400 BAD REQUEST', 400);
    }

    /**
     * applyContentType
     */
    public function applyContentType(): void
    {
        $this->isHtmlResponse = $this->contentType === Mime::HTML;
        $this->applyHeader('Content-Type: ' . $this->contentType . '; charset=' . $this->charset);
        $this->contentType = null;
    }

    /**
     * @param bool $noCache
     */
    public function applyNoCache($noCache = false)
    {
        if ($noCache || $this->isNoCache) {
            $this->applyHeader('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $this->applyHeader('Pragma: no-cache');
            $this->isNoCache = null;
        }
    }

    /**
     * @return void
     */
    public function html(): void
    {
        $this->applyContentType();
        $this->applyNoCache(true);
        print $this->data;
        return;
    }

    /**
     * @return void
     */
    public function json(): void
    {
        $this->applyContentType();
        $this->applyNoCache(true);
        print \json_encode($this->data);
        return;
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
    protected function applyHeader(string $header, int $code = null, bool $replace = true): bool
    {
        if (\headers_sent()) {
            return false;
        }
        \header($header, $replace, $code);
        return true;
    }
}