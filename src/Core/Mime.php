<?php

namespace Core;

/**
 * Class Mime
 * @package Core
 */
final class Mime {

    /**
     * @var \Finfo
     */
    private static $fileInfo;

    /**
     * Most used mime types
     */
    const BINARY  = 'application/octet-stream',
        EXE     = 'application/octet-stream',
        HTML    = 'text/html',
        XHTML   = 'application/xhtml+xml',
        XML     = 'text/xml',
        XML2    = 'application/xml',
        TEXT    = 'text/plain',
        RICH    = 'text/enriched',
        JSON    = 'application/json',
        ATOM    = 'application/atom+xml',
        CSS     = 'text/css',
        JS      = 'application/x-javascript',
        JS1     = 'text/javascript',
        JS2     = 'application/javascript',
        PDF     = 'application/pdf',
        RAR     = 'application/z-rar-compressed',
        JAR     = 'application/java-archive',
        _7Z     = 'application/z-7z-compressed',
        ZIP     = 'application/zip',
        DOC     = 'application/msword',
        RTF     = 'application/rtf',
        XLS     = 'application/vnd.ms-excel',
        PPT     = 'application/vnd.ms-powerpoint',
        CSV     = 'text/csv',
        GIF     = 'image/gif',
        JPG     = 'image/jpeg',
        JPGI    = 'image/pjpeg', //for ie
        PNG     = 'image/png',
        PNGI    = 'image/x-png', //for ie
        SVG     = 'image/svg+xml',
        TIFF    = 'image/tiff',
        ICO     = 'image/vnd.microsoft.icon',
        WBMP    = 'image/vnd.wap.wbmp',
        BMP     = 'image/x-ms-bmp'
    ;

    const
        FORM_URL  = 'application/x-domain-form-urlencoded',
        FORM_DATA = 'multipart/form-data',
        MULTI_MIX = 'multipart/mixed',
        MULTI_PAR = 'multipart/parallel',
        MULTI_REL = 'multipart/related',
        MULTI_ALT = 'multipart/alternative';

    /**
     * @var array
     */
    protected static $mimeImages = [
        self::JPG  => self::JPG,
        self::JPGI => self::JPGI,
        self::PNG  => self::PNG,
        self::PNGI => self::PNGI,
        self::TIFF => self::TIFF,
        self::WBMP => self::WBMP,
        self::BMP  => self::BMP,
        self::SVG  => self::SVG,
        self::GIF  => self::GIF,
    ];

    /**
     * @param $type
     *
     * @return bool
     */
    public static function isImageMime($type)
    {
        return isset(self::$mimeImages[$type]);
    }

    /**
     * Определяет mime type файла
     *
     * @param string $file
     *
     * @return string|null
     */
    public static function resolve($file)
    {
        $fi = self::_getFileInfo();
        $mime = $fi->file($file, FILEINFO_MIME_TYPE);
        return $mime;
    }

    /**
     * @return \Finfo
     */
    protected static function _getFileInfo()
    {
        if (!self::$fileInfo instanceof \Finfo) {
            self::$fileInfo = new \Finfo(\FILEINFO_MIME);
        }
        return self::$fileInfo;
    }

}
