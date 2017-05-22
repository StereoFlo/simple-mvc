<?php

namespace Core;

class Session
{
    /* The diecrotry name for the session */
    private static $SESSION_DIR = '../tmp/sessions/';

    /* The name used for the session */
    private static $SESSION_NAME = 'f7eac143c2e6c95e84a3e128e9ddcee6';

    /**
     * Session Age.
     *
     * The number of seconds of inactivity before a session expires.
     *
     * @var integer
     */
    private static $SESSION_AGE = 1800;

    /**
     * Writes a value to the current session data.
     *
     * @param string $key String identifier.
     * @param mixed $value Single value or array of values to be written.
     * @return mixed Value or array of values written.
     * @throws \Exception
     */
    public static function write(string $key, $value)
    {
        if (!is_string($key)) {
            throw new \Exception('Session key must be string value');
        }
        self::_init();
        $_SESSION[$key] = $value;
        self::_age();
        return $value;
    }

    /**
     * Reads a specific value from the current session data.
     *
     * @param string $key String identifier.
     * @param boolean $child Optional child identifier for accessing array elements.
     * @return mixed Returns a string value upon success.  Returns false upon failure.
     * @throws \Exception
     */
    public static function read(string $key, bool $child = false)
    {
        if (!is_string($key))
            throw new \Exception('Session key must be string value');
        self::_init();
        if (isset($_SESSION[$key])) {
            self::_age();

            if (false == $child) {
                return $_SESSION[$key];
            } else {
                if (isset($_SESSION[$key][$child])) {
                    return $_SESSION[$key][$child];
                }
            }
        }
        return false;
    }

    /**
     * Deletes a value from the current session data.
     *
     * @param string $key String identifying the array key to delete.
     * @return void
     * @throws \Exception
     */
    public static function delete(string $key)
    {
        if (!is_string($key)) {
            throw new \Exception('Session key must be string value');
        }
        self::_init();
        unset($_SESSION[$key]);
        self::_age();
    }

    /**
     * Echos current session data.
     *
     * @return void
     */
    public static function dump()
    {
        self::_init();
        echo nl2br(print_r($_SESSION));
    }

    /**
     * Starts or resumes a session by calling {@link Session::_init()}.
     *
     * @see Session::_init()
     * @param bool $regenerate_session_id
     * @param int $limit
     * @param string $path
     * @param null|string $domain
     * @param null $secure_cookies_only
     * @return bool Returns true upon success and false upon failure.
     */
    public static function start(bool $regenerate_session_id = true, int $limit = 0, $path = '/', string $domain = null, $secure_cookies_only = null)
    {
        // this function is extraneous
        return self::_init($regenerate_session_id, $limit, $path, $domain, $secure_cookies_only);
    }

    /**
     *
     */
    public static function regenerate_session_id() {

        $session = array();
        foreach ($_SESSION as $k => $v) {
            $session[$k] = $v;
        }
        session_destroy();
        session_id(bin2hex(openssl_random_pseudo_bytes(16)));
        session_start();
        foreach ($session as $k => $v) {
            $_SESSION[$k] = $v;
        }
    }

    /**
     * Returns current session cookie parameters or an empty array.
     *
     * @return array Associative array of session cookie parameters.
     */
    public static function params()
    {
        $r = array();
        if ('' !== session_id()) {
            $r = session_get_cookie_params();
        }
        return $r;
    }

    /**
     * Closes the current session and releases session file lock.
     *
     * @return boolean Returns true upon success and false upon failure.
     */
    public static function close()
    {
        if ('' !== session_id()) {
            return session_write_close();
        }
        return true;
    }

    /**
     * Alias for {@link Session::close()}.
     *
     * @see Session::close()
     * @return boolean Returns true upon success and false upon failure.
     */
    public static function commit()
    {
        return self::close();
    }

    /**
     * Removes session data and destroys the current session.
     *
     * @return void
     */
    public static function destroy()
    {
        if ('' !== session_id()) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
        }
    }

    /**
     * Expires a session if it has been inactive for a specified amount of time.
     * @return void
     * @throws \Exception
     */
    private static function _age()
    {
        $last = $_SESSION['LAST_ACTIVE'] ?? false;

        if (false !== $last && (time() - $last > self::$SESSION_AGE)) {
            self::destroy();
            throw new \Exception('Something is wrong with a session');
        }
        $_SESSION['LAST_ACTIVE'] = time();
    }

    /**
     * Initializes a new session or resumes an existing session.
     *
     * @param bool $regenerate_session_id
     * @param int $limit
     * @param string $path
     * @param null|string $domain
     * @param bool|null $secure_cookies_only
     * @param string $baseUrl
     * @return bool Returns true upon success and false upon failure.
     * @throws \Exception
     */
    private static function _init(bool $regenerate_session_id = false, int $limit = 0, string $path = '/', string $domain = null, bool $secure_cookies_only = null, string $baseUrl = '')
    {
        if (function_exists('session_status')) {
            if (session_status() == PHP_SESSION_DISABLED)
                throw new \Exception('Session is disabled');
        }
        if ('' === session_id()) {
            $site_root = $baseUrl;
            $session_save_path = $site_root . self::$SESSION_DIR;
            session_save_path($session_save_path);
            session_name(self::$SESSION_NAME);
            $domain = $domain ?? $_SERVER['SERVER_NAME'];
            session_set_cookie_params($limit, $path, $domain, $secure_cookies_only, true);
            session_start();
            if ($regenerate_session_id) {
                self::regenerate_session_id();
            }
            return true;
        }
        self::_age();
        if ($regenerate_session_id && rand(1, 100) <= 5) {
            self::regenerate_session_id();
            $_SESSION['regenerated_id'] = session_id();
        }
        return true;

    }
}