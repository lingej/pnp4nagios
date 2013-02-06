<?php defined('SYSPATH') OR die('No direct access allowed.');


class Auth_Multisite_Model {
    private $htpasswdPath;
    private $serialsPath;
    private $secretPath;
    private $authFile;

    public function __construct($htpasswdPath, $serialsPath, $secretPath, $loginUrl) {
        $this->htpasswdPath = $htpasswdPath;
        $this->serialsPath  = $serialsPath;
        $this->secretPath   = $secretPath;
        $this->loginUrl     = $loginUrl;

        // When the auth.serial file exists, use this instead of the htpasswd
        // for validating the cookie. The structure of the file is equal, so
        // the same code can be used.
        if(file_exists($this->serialsPath)) {
            $this->authFile = 'serial';

        } elseif(file_exists($this->htpasswdPath)) {
            $this->authFile = 'htpasswd';

        } else {
            throw new Kohana_exception("error.auth_multisite_missing_htpasswd");
        }

        if(!file_exists($this->secretPath)) {
            $this->redirectToLogin();
        }
    }

    private function loadAuthFile($path) {
        $creds = array();
        foreach(file($path) AS $line) {
            if(strpos($line, ':') !== false) {
                list($username, $secret) = explode(':', $line, 2);
                $creds[$username] = rtrim($secret);
            }
        }
        return $creds;
    }

    private function loadSecret() {
        return trim(file_get_contents($this->secretPath));
    }

    private function generateHash($username, $now, $user_secret) {
        $secret = $this->loadSecret();
        return md5($username . $now . $user_secret . $secret);
    }

    private function checkAuthCookie($cookieName) {
        if(!isset($_COOKIE[$cookieName]) || $_COOKIE[$cookieName] == '') {
            throw new Exception();
        }

        list($username, $issueTime, $cookieHash) = explode(':', $_COOKIE[$cookieName], 3);

        if($this->authFile == 'htpasswd')
            $users = $this->loadAuthFile($this->htpasswdPath);
        else
            $users = $this->loadAuthFile($this->serialsPath);

        if(!isset($users[$username])) {
            throw new Exception();
        }
        $user_secret = $users[$username];

        // Validate the hash
        if($cookieHash != $this->generateHash($username, $issueTime, $user_secret)) {
            throw new Exception();
        }

        // FIXME: Maybe renew the cookie here too

        return $username;
    }

    private function checkAuth() {
        // Loop all cookies trying to fetch a valid authentication
        // cookie for this installation
        foreach(array_keys($_COOKIE) AS $cookieName) {
            if(substr($cookieName, 0, 5) != 'auth_') {
                continue;
            }
            try {
                $name = $this->checkAuthCookie($cookieName);
                return $name;
            } catch(Exception $e) {}
        }
        return '';
    }

    private function redirectToLogin() {
        header('Location:' . $this->loginUrl . '?_origtarget=' . $_SERVER['REQUEST_URI']);
    }

    public function check() {
        $username = $this->checkAuth();
        if($username === '') {
            $this->redirectToLogin();
            exit(0);
        }

        return $username;
    }
}

?>
