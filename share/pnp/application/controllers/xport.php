<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Xport controller.
 * 
 * @package    pnp4nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Xport_Controller extends System_Controller  {

    public function __construct()
    {
        parent::__construct();
        // Disable auto-rendering
        $this->auto_render = FALSE;
        $this->data->getTimeRange($this->start,$this->end,$this->view);

    }

    public function xml(){
        if(isset($this->host) && isset($this->service)){
            $this->data->buildXport($this->host,$this->service);
            if($this->auth->is_authorized($this->data->MACRO['AUTH_HOSTNAME'], $this->data->MACRO['AUTH_SERVICEDESC']) === FALSE){
                header('Content-Type: application/xml');
                print "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
                print "<NAGIOS>\n";
                print "<ERROR>not authorized</ERROR>\n";
                print "</NAGIOS>\n";
                exit;
            }
            $data    = $this->rrdtool->doXport($this->data->XPORT);
            header('Content-Type: application/xml');
            print $data; 
        }else{
            throw new Kohana_Exception('error.xport-host-service');
        }
    }

    public function json(){
        if(isset($this->host) && isset($this->service)){
            $this->data->buildXport($this->host,$this->service);
            if($this->auth->is_authorized($this->data->MACRO['AUTH_HOSTNAME'], $this->data->MACRO['AUTH_SERVICEDESC']) === FALSE){
                header('Content-type: application/json');
                print json_encode("not authorized");                
                exit;
            }
            $data    = $this->rrdtool->doXport($this->data->XPORT);
            $json    = json_encode(simplexml_load_string($data));
            header('Content-type: application/json');
            print $json; 
        }else{
            throw new Kohana_Exception('error.xport-host-service');
        }
    }

    public function csv(){
        if(isset($this->host) && isset($this->service)){
            $this->data->buildXport($this->host,$this->service);
            if($this->auth->is_authorized($this->data->MACRO['AUTH_HOSTNAME'], $this->data->MACRO['AUTH_SERVICEDESC']) === FALSE){
                header("Content-Type: text/plain; charset=UTF-8");
                print "not authorized";                
                exit;
            }
            $data = $this->rrdtool->doXport($this->data->XPORT);
            $csv = $this->data->xml2csv($data);
            header("Content-Type: text/plain; charset=UTF-8");
            print $csv;
        }else{
            throw new Kohana_Exception('error.xport-host-service');
        }
    }


}
