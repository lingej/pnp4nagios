<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Json controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Json_Controller extends System_Controller  {

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        // Disable auto-rendering
        $this->auto_render = FALSE;
        // Service Details
        if($this->host != "" && $this->service != ""){
            $services      = $this->data->getServices($this->host);
            $this->data->buildDataStruct($this->host,$this->service,$this->view);
            if($this->auth->is_authorized($this->data->MACRO['AUTH_HOSTNAME'], $this->data->MACRO['AUTH_SERVICEDESC']) === FALSE){
                print json_encode("not authorized");
                exit;
            }
            $i = 0;
            $json = array();
            foreach($this->data->STRUCT as $struct){
                $json[$i]['image_url']   = "host=".$struct['MACRO']['HOSTNAME']."&srv=".$struct['MACRO']['SERVICEDESC']."&source=".$struct['SOURCE']."&view=".$struct['VIEW'];
                $json[$i]['ds_name']     = $struct['ds_name'];
                $json[$i]['start']       = $struct['TIMERANGE']['start'];
                $json[$i]['end']         = $struct['TIMERANGE']['end'];
                $json[$i]['title']       = $struct['TIMERANGE']['title'];
                $i++;
            }
            print json_encode($json);
        // Host Overview
        }elseif($this->host != ""){
            if($this->auth->is_authorized($this->host) === FALSE){
                print json_encode("not authorized");
                exit;
            }
            $services = $this->data->getServices($this->host);
            foreach($services as $service){
                if($service['state'] == 'active'){
                    $this->data->buildDataStruct($this->host,$service['name'],$this->view);
                }    
            }
            $i = 0;
            $json = array();
            foreach($this->data->STRUCT as $struct){
                $json[$i]['image_url']   = "host=".$struct['MACRO']['HOSTNAME']."&srv=".$struct['MACRO']['SERVICEDESC']."&source=".$struct['SOURCE']."&view=".$struct['VIEW'];
                $json[$i]['servicedesc'] = $struct['MACRO']['SERVICEDESC'];
                $json[$i]['ds_name']     = $struct['ds_name'];
                $json[$i]['start']       = $struct['TIMERANGE']['start'];
                $json[$i]['end']         = $struct['TIMERANGE']['end'];
                $json[$i]['title']       = $struct['TIMERANGE']['title'];
                $i++;
            }
            print json_encode($json);
        }else{
            $this->hosts = $this->data->getHosts();
            $i = 0;
            $json = array();
            foreach($this->hosts as $host){
                if($host['state'] == "active"){
                    $json[$i]['hostname'] = $host['name'];
                    $i++;
                }
            }
            print json_encode($json);
        }
    }
}
