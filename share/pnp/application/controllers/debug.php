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
		$this->template->debug   = $this->add_view('debug');
		$this->host              = $this->input->get('host');
		$this->service           = $this->input->get('srv');
	}

	public function index()
	{

		$start   = $this->input->get('start');
		$end     = $this->input->get('end');
		$view    = FALSE;

        if(isset($_GET['view']) && $_GET['view'] != "" )
			$view = pnp::clean($_GET['view']);

		$this->data->getTimeRange($start,$end,$view);

		if(isset($this->host) && isset($this->service)){
		    $this->service = pnp::clean($this->service);
		    $this->host    = pnp::clean($this->host);
			$this->url      = "?host=".$this->host."&srv=".$this->service;
		    $services      = $this->data->getServices($this->host);
		    $this->data->buildDataStruct($this->host,$this->service,$view);
		    $this->title = "Service Details ". $this->host ." -> " . $this->data->MACRO['DISP_SERVICEDESC'];
		}elseif(isset($this->host)){
		    $this->host    = pnp::clean($this->host);
			$this->url     = "?host=".$this->host;
		    $view    	   = 1;
		    $this->title   = "Start $this->host";
		    $services = $this->data->getServices($this->host);
		    $this->title = "Service Overview for $this->host";
		    foreach($services as $service){
			if($service['state'] == 'active')
		   	    $this->data->buildDataStruct($this->host,$service['name'],$view);
		    }
		}else{
		    if(isset($this->host)){
		    	url::redirect("/graph");
		    }else{
				throw new Kohana_User_Exception('Hostname not set ;-)', "RTFM my Friend, RTFM!");
		    }			
		}
	}
}
