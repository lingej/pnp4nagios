<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Debug controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge 
 * @license    GPL
 */
class Zoom_Controller extends System_Controller  {


	public function __construct()
	{
		parent::__construct();
		$this->template          = $this->add_view('zoom');
		$this->host              = $this->input->get('host');
		$this->service           = $this->input->get('srv');
	}

	public function index()
	{

		$start   = $this->input->get('start');
		$end     = $this->input->get('end');
		$source  = $this->input->get('source');
		$view    = "";

        if(isset($_GET['view']) && $_GET['view'] != "" ){
			$view = pnp::clean($_GET['view']);
		}else{
			$view = $this->config->conf['overview-range'];
		}

		$this->data->getTimeRange($start,$end,$view);

		if(isset($this->host) && isset($this->service)){
			$this->template->host    = $this->host;
			$this->template->srv     = $this->service;
			$this->template->source  = $source;
			$this->template->end     = $end;
			$this->template->start   = $start;
			$this->template->graph_height = 100;
			$this->template->graph_width  = 500;
		}else{
		    url::redirect("/graph");
		}
	}
}
