<?php


namespace Core\Http;

use App\Utils;

/**
 * Class ServerBag
 * @package Core\Request
 */
class ServerBag extends Bag
{
    /**
     * @return string
     */
    public function getMethod(): string
    {
        return \strtolower(Utils::getProperty($this->stack, 'REQUEST_METHOD', ''));
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        $headers = [];
        foreach ($this->stack as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = \str_replace(' ', '-', \ucwords(\str_replace('_', ' ', \strtolower(\substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }
}