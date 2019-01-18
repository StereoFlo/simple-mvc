<?php

namespace Core;

use App\Utils;

/**
 * Class Controller
 * @package Core
 */
class Template
{
    /**
     * @param $viewName
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public function render(string $viewName, array $params = [])
    {
        $filePath = $this->getFilePath($viewName);

        if (!\file_exists($filePath)) {
            throw new \Exception($filePath . ' template file is not exists');
        }
        \ob_start();
        \extract($params);
        require_once $filePath;
        $template = \ob_get_contents();
        \ob_end_clean();
        return $template;
    }

    /**
     * @param string $viewName
     *
     * @return string
     * @throws \Exception
     */
    private function getFilePath(string $viewName): string
    {
        $mainConfig = Config::getConfig('main');
        $filePath = \realpath(Utils::getProperty($mainConfig, 'viewPath') . DS . $viewName . \PHP_EXTENSION);
        if (!\file_exists($filePath)) {
            throw new \Exception($filePath . ' template file is not exists');
        }
        return $filePath;
    }
}