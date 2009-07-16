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
		$this->template->graph   = $this->add_view('graph');
		$this->host              = $this->input->get('host');
		$this->service           = $this->input->get('srv');
	}

	public function index()
	{
		$this->template->graph->graph_content = $this->add_view('graph_content');
		$this->template->graph->header        = $this->add_view('header');
		$this->template->graph->search_box    = $this->add_view('search_box');
		$this->template->graph->service_box   = $this->add_view('service_box');
		$this->template->graph->status_box    = $this->add_view('status_box');

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
			// Status Box Vars
		    $this->template->graph->status_box->host     = $this->host;
		    $this->template->graph->status_box->lhost    = $this->host;
		    $this->template->graph->status_box->service  = $this->data->MACRO['DISP_SERVICEDESC'];
		    $this->template->graph->status_box->lservice = $this->data->MACRO['SERVICEDESC'];
		    $this->template->graph->status_box->timet    = date($this->config->conf['date_fmt'],$this->data->MACRO['TIMET']);
			// Service Box Vars
		    $this->template->graph->service_box->services = $services;
		    $this->template->graph->service_box->host = $this->host;
		}elseif($this->host != ""){
		    $this->host    = pnp::clean($this->host);
			$this->url     = "?host=".$this->host;
		    $this->title   = "Start $this->host";
		    $services = $this->data->getServices($this->host);
			// Status Box Vars
		    $this->template->graph->status_box->host    = $this->host;
		    $this->template->graph->status_box->lhost   = $this->host;
		    $this->template->graph->status_box->shost   = pnp::shorten($this->host);
		    $this->template->graph->status_box->timet   = date($this->config->conf['date_fmt'],$this->data->MACRO['TIMET']);
			// Service Box Vars
			$this->template->graph->service_box->services = $services;
		    $this->template->graph->service_box->host = $this->host;
			// Timerange Box Vars
			$this->template->graph->timerange_box = $this->add_view('timerange_box');
		    $this->template->graph->timerange_box->timeranges = $this->data->TIMERANGE;

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
		$this->template->graph->icon_box      = $this->add_view('icon_box');
		$this->template->graph->logo_box      = $this->add_view('logo_box');
		$this->template->graph->header->title = $this->title;
	}
}
