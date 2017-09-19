<?php

use Core\Logger;
use Core\Response;
use Core\Router;

/**
 * Class Autoloader
 */
class Application
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
     * Modes
     */
    const MODE_API = 1,
          MODE_WEB = 2;

    /**
     * Static call
     *
     * @return Application
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
        return $this->go();
    }

    /**
     * @param string $file
     * @param bool   $ext
     * @param bool   $dir
     *
     * @return string
     */
    private function loader(string $file, bool $ext = false, bool $dir = false)
    {
        $file = str_replace('\\', '/', $file);
        list($path, $filePath) = $this->getPaths($file, $ext, $dir);

        if (!file_exists($filePath)) {
            $flag = false;
            return $this->recursiveAutoload($file, $path, $ext, $flag);
        }
        if (false === $ext) {
            require_once($filePath);
            return '';
        }
        return $filePath;
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
        if (false !== ($handle = opendir($path)) && $flag) {
            while (false !== ($dir = readdir($handle)) && $flag) {
                if (false === strpos($dir, '.')) {
                    $path2 = $path . DIRECTORY_SEPARATOR . $dir;
                    $filePath = $path2 . DIRECTORY_SEPARATOR . $file . $ext;
                    if (!file_exists($filePath)) {
                        $res = $this->recursiveAutoload($file, $path2, $ext, $flag);
                    }
                    $flag = false;
                    if (false === $ext) {
                        require_once($filePath);
                        break;
                    }
                    return $filePath;
                }
            }
            closedir($handle);
        }
        return $res;
    }

    /**
     * @return mixed
     */
    private function go()
    {
        try {
            return self::getRouter();
        } catch (Exception $e) {
            Logger::logToFile($e->getCode() . ': ' . $e->getMessage());
            Response::error503();
            return false;
        } catch (Throwable $t) {
            Logger::logToFile($t->getCode() . ': ' . $t->getMessage());
            Response::error503();
            return false;
        }
    }

    /**
     * @return Router
     */
    private static function getRouter()
    {
        return Router::create($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }

    /**
     * @param string $file
     * @param bool   $ext
     * @param bool   $dir
     *
     * @return array
     */
    private function getPaths(string $file, bool $ext, bool $dir): array
    {
        $srcPath = DS . '..' . DS . static::SRC_DIR . DS;
        if (false === $ext) {
            $path = $_SERVER['DOCUMENT_ROOT'] . $srcPath;
            $filePath = $path . $file . static::PHP_EXTENSION;
            return [$path, $filePath];
        }
        $path = $_SERVER['DOCUMENT_ROOT'] . (($dir) ? $srcPath . $dir : '');
        $filePath = $path . DS . $file . '.' . $ext;
        return [$path, $filePath];
    }
}