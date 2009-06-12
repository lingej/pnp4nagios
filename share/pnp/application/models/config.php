<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Retrieves the PNP config files 
 */
class Config_Model extends Model
{
	public $conf = array();
	public $views = array();

	public function read_config(){
		if(getenv('PNP_CONFIG_FILE') != ""){
    			$config = getenv('PNP_CONFIG_FILE');
		}else{
    			$config = "/usr/local/pnp4nagios/etc/config";
		}

		if (is_readable($config . ".php")) {
    			include ($config . ".php");
		}else {
    			throw new Kohana_User_Exception('Main Config not found', "Can´t find $config.php");
		}

		if (is_readable($config . "_local.php")) {
    			include ($config . "_local.php");
		}

		if(!isset($conf['template_dir'])){
        		$conf['template_dir'] = dirname(__file__);
		}
		$this->conf = $conf;
		$this->views = $views;
	}
}
