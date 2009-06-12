<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * RRD controller.
 *
 * @package    pnp4nagios 
 * @author     Joerg Linge
 * @license    GPL
 */
class Rrd_Controller extends System_Controller  {

	public function __construct()
	{
		parent::__construct();
		$this->template->body          = $this->add_view('body');
	}

	public function index()
	{
		url::redirect('start', 302);

	}

	public function host($host=false)
	{
		#$this->template->body          = $this->add_view('body');
		$this->template->body->graph_content = $this->add_view('graph_content');
		$this->template->body->header        = $this->add_view('header');
		$this->template->body->search_box    = $this->add_view('search_box');
		$this->template->body->status_box    = $this->add_view('status_box');
		$this->template->title = "TEST";

		if($host===false) {
			$this->template->body->header->title = "Find first Host";
		}else{
			$this->template->body->header->title = "Find all Services for Host $host";
		}
	}

	public function service($host=false, $service=false)
	{
		#$this->template->body          = $this->add_view('body');
		$this->template->body->graph_content = $this->add_view('graph_content');
		$this->template->body->header        = $this->add_view('header');
		$this->template->body->search_box    = $this->add_view('search_box');
		$this->template->body->status_box    = $this->add_view('status_box');
		$this->template->title = "TEST";

		if($host===false && $service === false){
			$this->template->body->header->title = "Find first Host";
		}elseif($service === false){
			$this->template->body->header->title = "Find all Service for Host $host";
		}else{
			$this->template->body->header->title = "Find service $service for Host $host";
		}
	}

	

}
