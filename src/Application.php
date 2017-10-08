<?php

use App\Boot\Api;
use App\Boot\Web;
use Core\Logger;
use Core\Response;
use Core\Router;

/**
 * Class Autoloader
 */
class Application
{
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
    private function recursiveAutoload(string $file, string $path, string $ext = \PHP_EXTENSION, bool &$flag): string
    {
        $res = '';
        if (false !== ($handle = opendir($path)) && $flag) {
            while (false !== ($dir = readdir($handle)) && $flag) {
                if (false === strpos($dir, '.')) {
                    $path2 = $path . DS . $dir;
                    $filePath = $path2 . DS . $file . $ext;
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
            $router = self::getRouter();
            switch ($router->getMode()) {
                case self::MODE_WEB:
                    return Web::run($router->getOut());
                    break;
                case self::MODE_API:
                    return Api::run($router->getOut());
                    break;
                default:
                    Response::error503();
                    break;
            }
        } catch (Exception $e) {
            Logger::logToFile($e->getCode() . ': ' . $e->getMessage());
            Response::error503();
            return false;
        } catch (Throwable $t) {
            Logger::logToFile($t->getCode() . ': ' . $t->getMessage());
            Response::error503();
            return false;
        }
        return false;
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
        if (false === $ext) {
            $filePath = SRC_DIR . DS . $file . \PHP_EXTENSION;
            return [SRC_DIR . DS, $filePath];
        }
        $path = SRC_DIR . DS . (($dir) ? SRC_DIR . $dir : '');
        $filePath = $path . DS . $file . '.' . $ext;
        return [$path, $filePath];
    }
}