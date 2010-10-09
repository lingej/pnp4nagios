<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Retrieves the PNP config files 
 */
class Auth_Model extends Model {
    public $SOCKET = NULL;
    public $socketPath = NULL;
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
        if(isset($_SERVER['REMOTE_USER'])){
            $this->REMOTE_USER = $_SERVER['REMOTE_USER'];
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
        $this->SOCKET = socket_create(AF_UNIX, SOCK_STREAM, 0);
        if($this->SOCKET === FALSE) {
            throw new Kohana_exception("error.livestatus_socket_error", socket_strerror(socket_last_error($this->SOCKET)), $this->socketPath);
        }
        $result = @socket_connect($this->SOCKET, $this->socketPath);
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
}
