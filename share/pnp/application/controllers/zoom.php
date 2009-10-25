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

		$this->start   = $this->input->get('start');
		$this->end     = $this->input->get('end');
		$this->source  = $this->input->get('source');
		$this->view    = "";

        if(isset($_GET['view']) && $_GET['view'] != "" ){
			$this->view = pnp::clean($_GET['view']);
		}else{
			$this->view = $this->config->conf['overview-range'];
		}

		$this->data->getTimeRange($this->start,$this->end,$this->view);

		if(isset($this->host) && isset($this->service)){
            if($this->start && $this->end ){
                    $this->session->set("start", $this->start);
                    $this->session->set("end", $this->end);
            }
			$this->template->host    = $this->host;
			$this->template->srv     = $this->service;
			$this->template->view    = $this->view;
			$this->template->source  = $this->source;
			$this->template->end     = $this->end;
			$this->template->start   = $this->start;
			$this->template->graph_height = $this->config->conf['graph_height'];
			$this->template->graph_width  = $this->config->conf['graph_width'];
		}else{
		    url::redirect("/graph");
		}
	}
}
