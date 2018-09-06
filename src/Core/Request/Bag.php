<?php

namespace Core\Request;

use App\Utils;

/**
 * Class Bag
 * @package Core\Request
 */
class Bag
{
    /**
     * $_POST|$_GET|$_FILES
     * @var array
     */
    protected $currentArray;

    /**
     * Bag constructor.
     *
     * @param array $currentArray
     */
    public function __construct(array $currentArray)
    {
        $this->currentArray = $currentArray;
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return Utils::getProperty($this->currentArray, $key, $default);
    }

    /**
     * @return array|null
     */
    public function all(): ?array
    {
        return $this->currentArray;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return isset($this->currentArray[$key]);
    }

    /**
     * @param     $key
     * @param int $default
     *
     * @return mixed|int
     */
    public function getInt($key, $default = 0)
    {
        return Utils::getProperty($this->currentArray, $key, $default);
    }

    /**
     * @param      $key
     * @param bool $default
     *
     * @return mixed|bool
     */
    public function getBool($key, $default = false)
    {
        return Utils::getProperty($this->currentArray, $key, $default);
    }
}