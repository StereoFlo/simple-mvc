<?php

namespace Core\Session;

use Core\Utils;
use Core\Config;

/**
 * Class Session
 * @package Core
 */
class Session
{
    /**
     * @var string session name
     */
    private static $SESSION_NAME = 'f7eac143c2e6c95e84a3e128e9ddcee6';

    /**
     * Session Age.
     * The number of seconds of inactivity before a session expires.
     *
     * @var integer
     */
    private static $SESSION_AGE = 1800;

    /**
     * Writes a value to the current session data.
     *
     * @param string $key   String identifier.
     * @param mixed  $value Single value or array of values to be written.
     *
     * @return mixed Value or array of values written.
     * @throws \Exception
     */
    public static function write(string $key, $value)
    {
        if (!\is_string($key)) {
            throw new \Exception('Session key must be string value');
        }
        self::init();
        $_SESSION[$key] = $value;
        self::age();

        return $value;
    }

    /**
     * Reads a specific value from the current session data.
     *
     * @param string  $key   String identifier.
     * @param boolean $child Optional child identifier for accessing array elements.
     *
     * @return mixed Returns a string value upon success.  Returns false upon failure.
     * @throws \Exception
     */
    public static function read(string $key, bool $child = false)
    {
        if (!\is_string($key)) {
            throw new \Exception('Session key must be string value');
        }
        self::init();
        $keyValue = Utils::getProperty($_SESSION, $key);
        if (!$keyValue) {
            return false;
        }
        self::age();
        if (false === $child) {
            return $keyValue;
        }
        if (Utils::getProperty($keyValue, $child)) {
            return $_SESSION[$key][$child];
        }
        return false;
    }

    /**
     * Deletes a value from the current session data.
     *
     * @param string $key String identifying the array key to delete.
     *
     * @return bool
     * @throws \Exception
     */
    public static function delete(string $key): bool
    {
        if (!\is_string($key)) {
            throw new \Exception('Session key must be string value');
        }
        self::init();
        unset($_SESSION[$key]);
        self::age();
        return true;
    }

    /**
     * Starts or resumes a session by calling {@link Session::_init()}.
     *
     * @see Session::init()
     *
     * @param bool        $regenerateSessionId
     * @param int         $limit
     * @param string      $path
     * @param null|string $domain
     * @param null        $secureOnly
     *
     * @return bool Returns true upon success and false upon failure.
     * @throws \Exception
     */
    public static function start(bool $regenerateSessionId = true, int $limit = 0, $path = '/', string $domain = null, $secureOnly = null)
    {
        return self::init($regenerateSessionId, $limit, $path, $domain, $secureOnly);
    }

    /**
     * @return bool
     */
    public static function regenerateSessionId(): bool
    {
        $session = [];
        foreach ($_SESSION as $k => $v) {
            $session[$k] = $v;
        }
        \session_destroy();
        \session_id(bin2hex(\openssl_random_pseudo_bytes(16)));
        \session_start();
        foreach ($session as $k => $v) {
            $_SESSION[$k] = $v;
        }
        return true;
    }

    /**
     * Returns current session cookie parameters or an empty array.
     *
     * @return array Associative array of session cookie parameters.
     */
    public static function params(): array
    {
        $currentSessionData = [];
        if ('' !== \session_id()) {
            $currentSessionData = \session_get_cookie_params();
        }
        if (empty($currentSessionData)) {
            return [];
        }
        return $currentSessionData;
    }

    /**
     * Closes the current session and releases session file lock.
     *
     * @return boolean Returns true upon success and false upon failure.
     */
    public static function close(): bool
    {
        if (empty(\session_id())) {
            return true;
        }
        \session_write_close();
        return true;
    }

    /**
     * Alias for {@link Session::close()}.
     *
     * @see Session::close()
     * @return boolean Returns true upon success and false upon failure.
     */
    public static function commit(): bool
    {
        return self::close();
    }

    /**
     * Removes session data and destroys the current session.
     *
     * @return bool
     */
    public static function destroy(): bool
    {
        if (empty(\session_id())) {
            return false;
        }

        $_SESSION = [];
        if (\ini_get("session.use_cookies")) {
            $params = \session_get_cookie_params();
            \setcookie(session_name(), '', \time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        return \session_destroy();

    }

    /**
     * Expires a session if it has been inactive for a specified amount of time.
     * @return void
     * @throws \Exception
     */
    private static function age()
    {
        $last = $_SESSION['LAST_ACTIVE'] ?? false;

        if (false !== $last && (\time() - $last > self::$SESSION_AGE)) {
            self::destroy();
            throw new \Exception('Something is wrong with a session');
        }
        $_SESSION['LAST_ACTIVE'] = \time();
    }

    /**
     * Initializes a new session or resumes an existing session.
     *
     * @param bool        $regenerateSessionId
     * @param int         $limit
     * @param string      $path
     * @param null|string $domain
     * @param bool|null   $secureOnly
     *
     * @return bool Returns true upon success and false upon failure.
     * @throws \Exception
     */
    private static function init(bool $regenerateSessionId = false, int $limit = 0, string $path = '/', string $domain = null, bool $secureOnly = null): bool
    {
        if (\function_exists('session_status')) {
            if (\session_status() == PHP_SESSION_DISABLED) {
                throw new \Exception('Session is disabled');
            }
        }
        if (empty(\session_id())) {
            try {
                $sessionSavePath = Config::getConfig('session', 'session_dir');
                \session_save_path($sessionSavePath);
                \session_name(self::$SESSION_NAME);
                $domain = $domain ?? $_SERVER['SERVER_NAME'];
                \session_set_cookie_params($limit, $path, $domain, $secureOnly, true);
                \session_start();
                if ($regenerateSessionId) {
                    self::regenerateSessionId();
                }
                return true;
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }
        self::age();
        if ($regenerateSessionId && \mt_rand(1, 100) <= 5) {
            self::regenerateSessionId();
            $_SESSION['regenerated_id'] = \session_id();
        }
        return true;

    }
}