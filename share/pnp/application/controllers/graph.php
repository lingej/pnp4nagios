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
		$this->template->graph->basket_box    = $this->add_view('basket_box');

		$this->start   = $this->input->get('start');
		$this->end     = $this->input->get('end');
		$this->view    = "";

		if(isset($_GET['view']) && $_GET['view'] != "" )
			$this->view = pnp::clean($_GET['view']);

		$this->data->getTimeRange($this->start,$this->end,$this->view);


		// Service Details
		if($this->host != "" && $this->service != ""){
		    $this->service = pnp::clean($this->service);
		    $this->host    = pnp::clean($this->host);
			$this->url     = "?host=".$this->host."&srv=".$this->service;
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
			// Timerange Box Vars
			$this->template->graph->timerange_box = $this->add_view('timerange_box');
		    $this->template->graph->timerange_box->timeranges = $this->data->TIMERANGE;
			//
		// Host Overview
		}elseif($this->host != ""){
		    $this->host    = pnp::clean($this->host);
			if($this->view == ""){
				$this->view = $this->config->conf['overview-range'];
			}
			$this->url     = "?host=".$this->host;
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
		   	    	$this->data->buildDataStruct($this->host,$service['name'],$this->view);
		    }
		}else{
		    $this->host = $this->data->getFirstHost();
		    if(isset($this->host)){
		    	url::redirect("/graph?host=$this->host");
		    }else{
				// FIXME 
				throw new Kohana_User_Exception('Hostname not set ;-)', "RTFM my Friend, RTFM!");
		    }			
		}
		$this->template->graph->icon_box      = $this->add_view('icon_box');
		$this->template->graph->icon_box->position = "graph";
		$this->template->graph->logo_box      = $this->add_view('logo_box');
		$this->template->graph->header->title = $this->title;
	}
}
