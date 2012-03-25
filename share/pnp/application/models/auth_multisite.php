<?php defined('SYSPATH') OR die('No direct access allowed.');


class Auth_Multisite_Model {
    private $htpasswdPath;
    private $secretPath;

    public function __construct($htpasswdPath, $secretPath) {
        $this->htpasswdPath = $htpasswdPath;
        $this->secretPath = $secretPath;

        if(!file_exists($this->htpasswdPath)) {
            throw new Kohana_exception("error.auth_multisite_missing_htpasswd");
        }

        if(!file_exists($this->secretPath)) {
            throw new Kohana_exception("error.auth_multisite_missing_secret");
        }
    }

    private function loadHtpasswd() {
        $creds = array();
        foreach(file($this->htpasswdPath) AS $line) {
            list($username, $pwhash) = explode(':', $line, 2);
            $creds[$username] = rtrim($pwhash);
        }
        return $creds;
    }

    private function loadSecret() {
        return trim(file_get_contents($this->secretPath));
    }

    private function generateHash($username, $now, $pwhash) {
        $secret = $this->loadSecret();
        return md5($username . $now . $pwhash . $secret);
    }

    private function checkAuthCookie($cookieName) {
        if(!isset($_COOKIE[$cookieName]) || $_COOKIE[$cookieName] == '') {
            throw new Exception();
        }

        list($username, $issueTime, $cookieHash) = explode(':', $_COOKIE[$cookieName], 3);

        // FIXME: Check expire time?
        
        $users = $this->loadHtpasswd();
        if(!isset($users[$username])) {
            throw new Exception();
        }
        $pwhash = $users[$username];

        // Validate the hash
        if($cookieHash != $this->generateHash($username, $issueTime, $pwhash)) {
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

    public function check() {
        $username = $this->checkAuth();
        if($username === '') {
            // FIXME: Get the real path to multisite
            header('Location:../../check_mk/login.py?_origin=' . $_SERVER['REQUEST_URI']);
            exit(0);
        }

        return $username;
    }
}

?>
