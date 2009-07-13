<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Graph controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Graph_Controller extends System_Controller  {

	#public $csrf_config = false;
	#public $route_config = false;

	public function __construct()
	{
		parent::__construct();
		$this->template->body    = $this->add_view('body');
		$this->host              = $this->input->get('host');
		$this->service           = $this->input->get('srv');
	}

	public function index()
	{
		$this->template->body->graph_content = $this->add_view('graph_content');
		$this->template->body->header        = $this->add_view('header');
		$this->template->body->search_box    = $this->add_view('search_box');
		$this->template->body->service_box   = $this->add_view('service_box');

		$start   = $this->input->get('start');
		$end     = $this->input->get('end');
		$view    = '';

		if($this->input->get('view') )
			$view = pnp::clean($this->input->get('view') );

		$this->data->getTimeRange($start,$end,$view);

		if($this->host != "" && $this->service != ""){
		    $this->service = pnp::clean($this->service);
		    $this->host    = pnp::clean($this->host);
			$this->url      = "?host=".$this->host."&srv=".$this->service;
		    $services      = $this->data->getServices($this->host);
		    $this->data->buildDataStruct($this->host,$this->service,$view);
		    $this->title = "Service Details ". $this->host ." -> " . $this->data->MACRO['DISP_SERVICEDESC'];
		    $this->template->body->service_box->services = $services;
		    $this->template->body->service_box->host = $this->host;
		    #print Kohana::debug($this->data->STRUCT);
		}elseif($this->host != ""){
		    $this->host    = pnp::clean($this->host);
			$this->url     = "?host=".$this->host;
		    $view    	   = 1;
		    $this->title   = "Start $this->host";
		    $services = $this->data->getServices($this->host);
		    $this->template->body->service_box->services = $services;
		    $this->template->body->service_box->host = $this->host;
		    $this->title = "Service Overview for $this->host";
		    foreach($services as $service){
			if($service['state'] == 'active')
		   	    $this->data->buildDataStruct($this->host,$service['name'],$view);
		    }
		}else{
		    $this->host = $this->data->getFirstHost();
		    if(isset($this->host)){
		    	url::redirect("/graph?host=$this->host");
		    }else{
			throw new Kohana_User_Exception('Hostname not set ;-)', "RTFM my Friend, RTFM!");
		    }			
		}
		$this->template->body->icon_box      = $this->add_view('icon_box');
		$this->template->body->logo_box      = $this->add_view('logo_box');
		$this->template->body->header->title = $this->title;
	}
}
