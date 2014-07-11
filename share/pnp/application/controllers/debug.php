<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Debug controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge 
 * @license    GPL
 */
class Debug_Controller extends System_Controller  {


    public function __construct()
    {
        parent::__construct();
        $this->template          = $this->add_view('template');
        $this->template->debug   = $this->add_view('debug');
    }

    public function index()
    {

        $this->data->getTimeRange($this->start,$this->end,$this->view);

        if(isset($this->host) && isset($this->service)){
            $this->url      = "?host=".$this->host."&srv=".$this->service;
            if($this->start){
                $this->url .= "&start=".$this->start;
                $this->session->set("start", $this->start);
            }
            if($this->end){
                $this->url .= "&end=".$this->end;
                $this->session->set("end", $this->end);
            }
            $services      = $this->data->getServices($this->host);
            $this->data->buildDataStruct($this->host,$this->service,$this->view);
			$this->is_authorized = $this->auth->is_authorized($this->data->MACRO['AUTH_HOSTNAME'], $this->data->MACRO['AUTH_SERVICEDESC']);
            $this->title = "Service Details ". $this->host ." -> " . $this->data->MACRO['DISP_SERVICEDESC'];
        }elseif(isset($this->host)){
            $this->is_authorized = $this->auth->is_authorized($this->host);
            if($this->view == ""){
                $this->view = $this->config->conf['overview-range'];
            }
            if($this->start){
                $this->url .= "&start=".$this->start;
                $this->session->set("start", $this->start);
            }
            if($this->end){
                $this->url .= "&end=".$this->end;
                $this->session->set("end", $this->end);
            }
            $this->title   = "Start $this->host";
            $services = $this->data->getServices($this->host);
            $this->title = "Service Overview for $this->host";
            foreach($services as $service){
            if($service['state'] == 'active')
                   $this->data->buildDataStruct($this->host,$service['name'],$this->view);
            }
        }else{
            if(isset($this->host)){
                url::redirect("/graph");
            }else{
                throw new Kohana_Exception('error.get-first-host');
            }            
        }
    }
}
