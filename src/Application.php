<?php
use Core\ApiController;
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
     * Static call
     *
     * @param int $mode
     * @return Application
     */
    public static function run(int $mode)
    {
        return new self($mode);
    }

    /**
     * Autoloader constructor.
     *
     * @param int $mode
     */
    private function __construct(int $mode)
    {
        spl_autoload_register([$this, 'loader']);
        $this->go($mode);
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
    /**
     * Modes
     */
    const MODE_API = 1,
        MODE_WEB = 2;

    /**
     * @param int   $mode
     *
     * @return mixed
     */
    private function go(int $mode)
    {
        try {
            switch ($mode) {
                case self::MODE_API:
                    return ApiController::respond(self::getRouter());
                    break;
                case self::MODE_WEB:
                default:
                    return self::getRouter();
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
    }

    /**
     * @return mixed
     */
    private static function getRouter()
    {
        return Router::init();
    }
}