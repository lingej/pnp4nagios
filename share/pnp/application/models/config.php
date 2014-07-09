<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Retrieves the PNP config files 
 */
class Config_Model extends System_Model
{
    public $conf = array();
    public $views = array();
    public $scheme = array();

    public function read_config(){
        if(getenv('PNP_CONFIG_FILE') != ""){
            $config = getenv('PNP_CONFIG_FILE');
        }elseif(OMD){
            $config = OMD_SITE_ROOT.'/etc/pnp4nagios/config';
        }else{
            $config = Kohana::config('core.pnp_etc_path')."/config";
        }

        # Default Values
        $conf['doc_language']           = Kohana::config('core.doc_language');
        $conf['graph_width']            = Kohana::config('core.graph_width');
        $conf['graph_height']           = Kohana::config('core.graph_height');
        $conf['zgraph_width']           = Kohana::config('core.zgraph_width');
        $conf['zgraph_height']          = Kohana::config('core.zgraph_height');
        $conf['pdf_width']              = Kohana::config('core.pdf_width');
        $conf['pdf_height']             = Kohana::config('core.pdf_height');
        $conf['right_zoom_offset']      = Kohana::config('core.right_zoom_offset');
        $conf['mobile_devices']         = Kohana::config('core.mobile_devices');
        $conf['pdf_page_size']          = Kohana::config('core.pdf_page_size');
        $conf['pdf_margin_left']        = Kohana::config('core.pdf_margin_left');
        $conf['pdf_margin_right']       = Kohana::config('core.pdf_margin_right');
        $conf['pdf_margin_top']         = Kohana::config('core.pdf_margin_top');
        $conf['auth_multisite_enabled']   = Kohana::config('core.auth_multisite_enabled');
        $conf['auth_multisite_serials']   = Kohana::config('core.auth_multisite_serials');
        $conf['auth_multisite_htpasswd']  = Kohana::config('core.auth_multisite_htpasswd');
        $conf['auth_multisite_secret']    = Kohana::config('core.auth_multisite_secret');
        $conf['auth_multisite_login_url'] = Kohana::config('core.auth_multisite_login_url');
	
	$scheme['Reds']     = array ('#FEE0D2','#FCBBA1','#FC9272','#FB6A4A','#EF3B2C','#CB181D','#A50F15','#67000D');

        $views = Kohana::config('core.views');
        
        if (is_readable($config . ".php")) {
            include ($config . ".php");
        }else {
            throw new Kohana_Exception('error.config-not-found', $config.'.php');
        }

        // Load optional config files
        // a) the _local.php config
        // b) all .php files which do not start with a "." in config.d/
        $custom_configs = array($config . "_local.php");
        if (file_exists($config . ".d") && is_dir($config . ".d")) {
            $dh = opendir($config . ".d");
            while (($file = readdir($dh)) !== false) {
                if ($file[0] != '.' && substr($file, -4) == '.php') {
                    $custom_configs[] = $config . ".d/" .$file;
                }
            }
            closedir($dh);
        }

	foreach($custom_configs AS $config_file) {
            if (is_readable($config_file)) {
                $array_a = $views;
                $views = array();
                include ($config_file);
                $array_b = $views;
                if(sizeof($views) == 0 ){
                    $views = $array_a;
                }
            }
        }

        // Use graph_height & graph_width from URL if present
        // Hint: In Kohana 3 Input class is removed
        $input = Input::instance();
        if($input->get('h') != "" ) $conf['graph_height'] = intval($input->get('h'));
        if($input->get('w') != "" ) $conf['graph_width']  = intval($input->get('w'));
        if($input->get('graph_height') != "" ) $conf['graph_height'] = intval($input->get('graph_height'));
        if($input->get('graph_width')  != "" ) $conf['graph_width']  = intval($input->get('graph_width'));
        $this->conf = $conf;
        $this->views = $views;
        $this->scheme = $scheme;
    }
}
