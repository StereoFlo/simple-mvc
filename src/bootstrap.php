<?php
define('DS', DIRECTORY_SEPARATOR);
define('PROJECT_DIR', realpath('..'));
define('SRC_DIR', realpath('..' . DS . 'src'));
define('APP_DIR', realpath('..' . DS . 'src/App'));
define('CONFIG_PATH', realpath('..' . DS . 'config'));
define('PHP_EXTENSION', '.php');

\spl_autoload_register('loader');

function loader(string $file, bool $ext = false, bool $dir = false): string
{
    $file = \str_replace('\\', '/', $file);
    list($path, $filePath) = getPaths($file, $ext, $dir);

    if (!\file_exists($filePath)) {
        $flag = false;
        return recursiveAutoload($file, $path, $flag);
    }
    if (false === $ext) {
        require_once($filePath);
        return '';
    }
    return $filePath;
}

function getPaths(string $file, bool $ext, bool $dir): array
{
    if (false === $ext) {
        $filePath = SRC_DIR . DS . $file . \PHP_EXTENSION;
        return [SRC_DIR . DS, $filePath];
    }
    $path = SRC_DIR . DS . (($dir) ? SRC_DIR . $dir : '');
    $filePath = $path . DS . $file . '.' . $ext;
    return [$path, $filePath];
}

function recursiveAutoload(string $file, string $path, bool &$flag): string
{
    $res = '';
    if (false !== ($handle = \opendir($path)) && $flag) {
        while (false !== ($dir = \readdir($handle)) && $flag) {
            if (false === \strpos($dir, '.')) {
                $path2 = $path . DS . $dir;
                $filePath = $path2 . DS . $file . \PHP_EXTENSION;
                if (!\file_exists($filePath)) {
                    $res = recursiveAutoload($file, $path2, $flag);
                }
                $flag = false;
                if (false === \PHP_EXTENSION) {
                    require_once($filePath);
                    break;
                }
                return $filePath;
            }
        }
        \closedir($handle);
    }
    return $res;
}

require_once __DIR__ . '/../src/Application.php';