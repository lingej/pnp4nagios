<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Retrieves the PNP config files 
 */
class Auth_Model extends System_Model {
    public $SOCKET       = NULL;
    public $socketPath   = NULL;
    public $socketDOMAIN = NULL;
    public $socketTYPE   = NULL;
    public $socketHOST   = NULL;
    public $socketPORT   = 0;
    public $socketPROTO  = NULL;

    public $ERR_TXT = "";
    public $AUTH_ENABLED = FALSE;
    public $REMOTE_USER = NULL;

    public function __construct() {
        $this->config = new Config_Model;
        $this->config->read_config();
        if($this->config->conf['auth_enabled'] == 1){
            $this->AUTH_ENABLED = TRUE;
            $this->socketPath = $this->config->conf['livestatus_socket'];
        }

        // Try to get the login of the user
        if(isset($_SERVER['REMOTE_USER'])){
            $this->REMOTE_USER = $_SERVER['REMOTE_USER'];
        }
        if($this->REMOTE_USER === NULL && $this->config->conf['auth_multisite_enabled'] == 1) {
            $MSAUTH = new Auth_Multisite_Model($this->config->conf['auth_multisite_htpasswd'],
                                               $this->config->conf['auth_multisite_serials'],
                                               $this->config->conf['auth_multisite_secret'],
                                               $this->config->conf['auth_multisite_login_url']);
            $this->REMOTE_USER = $MSAUTH->check();
            if($this->REMOTE_USER !== null)
                return;
        }

        if($this->AUTH_ENABLED === TRUE && $this->REMOTE_USER === NULL){
            throw new Kohana_exception("error.remote_user_missing");
        }
    }

    public function __destruct() {
        if($this->SOCKET !== NULL) {
            socket_close($this->SOCKET);
            $this->SOCKET = NULL;
        }
    }

    public function connect(){
		$this->getSocketDetails($this->socketPath);
        $this->SOCKET = socket_create($this->socketDOMAIN, $this->socketTYPE, $this->socketPROTO);
        if($this->SOCKET === FALSE) {
            throw new Kohana_exception("error.livestatus_socket_error", socket_strerror(socket_last_error($this->SOCKET)), $this->socketPath);
        }
		if($this->socketDOMAIN === AF_UNIX){
        	$result = @socket_connect($this->SOCKET, $this->socketPATH);
		}else{
        	$result = @socket_connect($this->SOCKET, $this->socketHOST, $this->socketPORT);
		}
        if(!$result) {
            throw new Kohana_exception("error.livestatus_socket_error", socket_strerror(socket_last_error($this->SOCKET)), $this->socketPath);
        }

    }

    private function queryLivestatus($query) {
        if($this->SOCKET === NULL) {
            $this->connect();
        }
        @socket_write($this->SOCKET, $query."\nOutputFormat: json\n\n");
        // Read 16 bytes to get the status code and body size
        $read = @socket_read($this->SOCKET,2048);
        if(!$read) {
            throw new Kohana_exception("error.livestatus_socket_error", socket_strerror(socket_last_error($this->SOCKET)));
        }
        # print Kohana::debug("read ". $read);
        // Catch problem while reading
        if($read === false) {
            throw new Kohana_exception("error.livestatus_socket_error", socket_strerror(socket_last_error($this->SOCKET)));
        }
        
        // Decode the json response
        $obj = json_decode(utf8_encode($read));
        socket_close($this->SOCKET);
        $this->SOCKET = NULL;
        return $obj;

    }

    public function is_authorized($host = FALSE, $service = NULL){
        if($this->AUTH_ENABLED === FALSE){
            return TRUE;
        }

        if($host == "pnp-internal"){
            return TRUE;
        }

        if($service === NULL || $service == "_HOST_" || $service == "Host Perfdata"){
            $users = explode(",", $this->config->conf['allowed_for_all_hosts']);
            if (in_array($this->REMOTE_USER, $users)) {
                return TRUE;
            }
            $query  = "GET hosts\nColumns: name\nFilter: name = $host\nAuthUser: ".$this->REMOTE_USER;
            $result = $this->queryLivestatus($query);
        }else{
            $users = explode(",", $this->config->conf['allowed_for_all_services']);
            if (in_array($this->REMOTE_USER, $users)) {
                return TRUE;
            }
            $query  = "GET services\nColumns: host_name description\nFilter: host_name = $host\nFilter: description = $service\nAuthUser: ".$this->REMOTE_USER;
            $result = $this->queryLivestatus($query);
        }

        if(sizeof($result) > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }


	public function getSocketDetails($string=FALSE){

		if(preg_match('/^unix:(.*)$/',$string,$match) ){
			$this->socketDOMAIN = AF_UNIX;
			$this->socketTYPE   = SOCK_STREAM;
			$this->socketPATH   = $match[1];
			$this->socketPROTO  = 0;
			return;
		}
		if(preg_match('/^tcp:([a-zA-Z0-9-\.]+):([0-9]+)$/',$string,$match) ){
			$this->socketDOMAIN = AF_INET;
			$this->socketTYPE   = SOCK_STREAM;
			$this->socketHOST   = $match[1];
			$this->socketPORT   = $match[2];
			$this->socketPROTO  = SOL_TCP;
			return;
		}
		# Fallback
		if(preg_match('/^\/.*$/',$string,$match) ){
			$this->socketDOMAIN = AF_UNIX;
			$this->socketTYPE   = SOCK_STREAM;
			$this->socketPATH   = $string;
			$this->socketPROTO  = 0;
			return;
		}
		return FALSE;
	}
}
