<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Graph controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Page_Controller extends System_Controller  {

	public function __construct()
	{
		parent::__construct();
		//$this->template->page    = $this->add_view('page');
		$this->page              = pnp::clean($this->input->get('page'));
	}

	public function index()
	{
		$this->start   = $this->input->get('start');
		$this->end     = $this->input->get('end');
		$this->view    = FALSE;

		if(isset($_GET['view']) && $_GET['view'] != "" )
			$this->view = pnp::clean($_GET['view']);

		$this->data->getTimeRange($this->start,$this->end,$this->view);

		if($this->page != "" ){
			$this->data->buildPageStruct($this->page,$this->view);
			print Kohana::debug($this->data->STRUCT);
		}else{
			// FIXME 
			throw new Kohana_User_Exception('No Pagename set', "RTFM my Friend, RTFM!");
		}
	}
}
