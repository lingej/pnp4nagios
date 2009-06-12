<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default controller.
 *
 * @package    pnp4nagios 
 * @author     Joerg Linge
 * @license    GPL
 */
class Start_Controller extends System_Controller  {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		
		if($this->isAuthorizedFor('host_overview' ) ){
		    $host = $this->data->getFirstHost();
		    url::redirect("graph?host=$host", 302);
		}
		$this->template->body          = $this->add_view('body');
		$this->template->body->graph_content = $this->add_view('graph_content');
		$this->template->body->header        = $this->add_view('header');
		$this->template->body->header->title = "Start Index";
		$this->template->body->search_box    = $this->add_view('search_box');
		$this->template->body->status_box    = $this->add_view('status_box');
		$this->template->title = "Start Page";
	}

}
