<?php

namespace Core\Http;

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
    protected $stack;

    /**
     * Bag constructor.
     *
     * @param array $currentArray
     */
    public function __construct(array $currentArray)
    {
        $this->stack = $currentArray;
    }

    /**
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return Utils::getProperty($this->stack, $key, $default);
    }

    /**
     * @return array|null
     */
    public function all(): ?array
    {
        return $this->stack;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return isset($this->stack[$key]);
    }

    /**
     * @param     $key
     * @param int $default
     *
     * @return mixed|int
     */
    public function getInt($key, $default = 0)
    {
        return Utils::getProperty($this->stack, $key, $default);
    }

    /**
     * @param      $key
     * @param bool $default
     *
     * @return mixed|bool
     */
    public function getBool($key, $default = false)
    {
        return Utils::getProperty($this->stack, $key, $default);
    }
}