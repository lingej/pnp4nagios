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
	
		$this->host    = $this->input->get('host');
		$this->service = $this->input->get('srv');
		$this->start   = $this->input->get('start');
		$this->end     = $this->input->get('end');
		$this->view    = 0; //fake value
		$this->source  = "";

		if($this->input->get('view') != "" )
			$this->view = $this->input->get('view') ;

		if($this->input->get('source') )
			$this->source = $this->input->get('source') ;

		$this->data->getTimeRange($this->start,$this->end,$this->view);

	}

	public function xml(){
		if(isset($this->host) && isset($this->service)){
		    $this->service = pnp::clean($this->service);
		    $this->host    = pnp::clean($this->host);
			$this->data->buildXport($this->host,$this->service);
		    $data    = $this->rrdtool->doXport($this->data->XPORT);
			header('Content-Type: application/xml');
			print $data; 
		}else{
		    print "ERROR";
		}
	}

	public function csv(){
		if(isset($this->host) && isset($this->service)){
		    $this->service = pnp::clean($this->service);
		    $this->host    = pnp::clean($this->host);
			$this->data->buildXport($this->host,$this->service);
		    $data = $this->rrdtool->doXport($this->data->XPORT);
		    $csv = $this->data->xml2csv($data);
			header("Content-Type: text/plain; charset=UTF-8");
			print $csv;
		}else{
		    print "ERROR";
		}
	}


}
