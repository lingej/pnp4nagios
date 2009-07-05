<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default controller.
 * Does not require login but should display default page
 *
 * @package    NINJA
 * @author     op5 AB
 * @license    GPL
 */
class Graph_Controller extends System_Controller  {

	#public $csrf_config = false;
	#public $route_config = false;

	public function __construct()
	{
		parent::__construct();
		$this->template->body          = $this->add_view('body');
		$this->config->read_config();
	}

	public function index()
	{
		$this->template->body->graph_content = $this->add_view('graph_content');
		$this->template->body->header        = $this->add_view('header');
		#$this->template->body->header->title = "Start Index";
		$this->template->body->search_box    = $this->add_view('search_box');
		$this->template->body->service_box   = $this->add_view('service_box');
		$this->host    = $this->input->get('host');
		$this->service = $this->input->get('srv');
		$start   = $this->input->get('start');
		$end     = $this->input->get('end');
		$view    = '';

		if($this->input->get('view') )
			$view = pnp::clean($this->input->get('view') );

		$this->data->getTimeRange($start,$end,$view);
		if(isset($this->host) && isset($this->service)){
		    $this->service = pnp::clean($this->service);
		    $this->host    = pnp::clean($this->host);
		    $services = $this->data->getServices($this->host);
		    $this->data->buildDataStruct($this->host,$this->service,$view);
		    $this->title = "Service Details ". $this->host ." -> " . $this->data->MACRO['DISP_SERVICEDESC'];
		    $this->template->body->service_box->services = $services;
		    $this->template->body->service_box->host = $this->host;
		    #print Kohana::debug($this->data->STRUCT);
		}elseif(isset($this->host)){
		    $this->host    = pnp::clean($this->host);
		    $view    = 1;
		    $this->title = "Start $this->host";
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
		$this->template->body->header->title = $this->title;
	}
}
