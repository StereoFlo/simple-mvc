<?php

/**
 * Class Autoloader
 */
class Autoloader
{
    /**
     * default php extension
     */
    const PHP_EXTENSION = '.php';

    /**
     * Source directory
     */
    const SRC_DIR = 'src';

    /**
     * Static call
     * @return Autoloader
     */
    public static function run()
    {
        return new self();
    }

    /**
     * Autoloader constructor.
     */
    private function __construct()
    {
        spl_autoload_register([$this, 'loader']);
        return $this;
    }

    private function loader(string $file, bool $ext = false, bool $dir = false)
    {
        $file = str_replace('\\', '/', $file);
        $srcPath = DS . '..' . DS . static::SRC_DIR . DS;
        if ($ext === false) {
            $path = $_SERVER['DOCUMENT_ROOT'] . $srcPath;
            $filePath = $path . $file . static::PHP_EXTENSION;
        } else {
            $path = $_SERVER['DOCUMENT_ROOT'] . (($dir) ? $srcPath . $dir : '');
            $filePath = $path . DS . $file . '.' . $ext;
        }

        if (file_exists($filePath)) {
            if (false === $ext) {
                require_once($filePath);
                return '';
            }
            return $filePath;
        }
        $flag = false;
        return $this->recursiveAutoload($file, $path, $ext, $flag);
    }

    /**
     * @param string $file
     * @param string $path
     * @param string $ext
     * @param bool $flag
     * @return string
     */
    private function recursiveAutoload(string $file, string $path, string $ext = self::PHP_EXTENSION, bool &$flag): string
    {
        $res = '';
        if (FALSE !== ($handle = opendir($path)) && $flag) {
            while (FAlSE !== ($dir = readdir($handle)) && $flag) {

                if (strpos($dir, '.') === FALSE) {
                    $path2 = $path . DIRECTORY_SEPARATOR . $dir;
                    $filepath = $path2 . DIRECTORY_SEPARATOR . $file . $ext;
                    if (file_exists($filepath)) {
                        $flag = FALSE;
                        if ($ext === FALSE) {
                            require_once($filepath);
                            break;
                        } else {
                            return $filepath;
                        }
                    }
                    $res = $this->recursiveAutoload($file, $path2, $ext, $flag);
                }
            }
            closedir($handle);
        }
        return $res;
    }
}