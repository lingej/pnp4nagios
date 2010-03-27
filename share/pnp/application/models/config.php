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
                $config = Kohana::config('core.pnp_etc_path')."/config";
        }

        if (is_readable($config . ".php")) {
                include ($config . ".php");
        }else {
                throw new Kohana_Exception('error.config-not-found', $config);
        }

        if (is_readable($config . "_local.php")) {
                include ($config . "_local.php");
        }

        $this->conf = $conf;
        $this->views = $views;
    }
}
