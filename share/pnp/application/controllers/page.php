<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Graph controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Page_Controller extends System_Controller  {

	public function __construct(){
		parent::__construct();
		$this->template->page    = $this->add_view('page');
		$this->template->page->graph_content  = $this->add_view('graph_content');
		$this->template->page->header         = $this->add_view('header');
		$this->template->page->logo_box       = $this->add_view('logo_box');
		
		$this->start   = $this->input->get('start');
		$this->end     = $this->input->get('end');
        $this->view    = "";

        if(isset($_GET['view']) && $_GET['view'] != "" ){
            $this->view = pnp::clean($_GET['view']);
		}else{
			$this->view = $this->config->conf['overview-range'];
		}
        $this->data->getTimeRange($this->start,$this->end,$this->view);

	}

	public function index(){
		$this->page = pnp::clean($this->input->get('page'));
		if($this->page == ""){
			$this->page = $this->data->getFirstPage();
		}
		if($this->page == ""){
			// FIXME i18n 
			throw new Kohana_User_Exception('Page Confg Dir', "No Page Config found in.");	
		}
		$this->data->buildPageStruct($this->page,$this->view);
		// FIXME i18n
		$this->template->page->header->title = "Page: ".$this->data->PAGE_DEF['page_name'];
		$this->url = "?page&page=$this->page";
       	// Timerange Box Vars
       	$this->template->page->timerange_box = $this->add_view('timerange_box');
       	$this->template->page->timerange_box->timeranges = $this->data->TIMERANGE;
		// Pages Box
		$this->pages = $this->data->getPages();
       	$this->template->page->pages_box = $this->add_view('pages_box');
       	$this->template->page->pages_box->pages = $this->pages;
		// Basket Box
		$this->template->page->basket_box      = $this->add_view('basket_box');
		// Icon Box	
		$this->template->page->icon_box      = $this->add_view('icon_box');
		$this->template->page->icon_box->position = "page";

	}

	public function basket(){
		$basket = $this->session->get("basket");
        if(is_array($basket) && sizeof($basket) > 0){
			foreach($basket as $item){
				list($host,$service) = explode("::",$item);
				$this->data->buildDataStruct($host, $service, $this->view);
			}
			// FIXME i18n
			$this->template->page->basket_box      = $this->add_view('basket_box');
			$this->template->page->header->title = "Page: Basket";
			$this->url = "basket?";
   	    	// Timerange Box Vars
   	    	$this->template->page->timerange_box = $this->add_view('timerange_box');
   	    	$this->template->page->timerange_box->timeranges = $this->data->TIMERANGE;
				// Pages Box
			$this->pages = $this->data->getPages();
  	     	$this->template->page->pages_box = $this->add_view('pages_box');
   	    	$this->template->page->pages_box->pages = $this->pages;
			// Icon Box	
			$this->template->page->icon_box      = $this->add_view('icon_box');
			$this->template->page->icon_box->position = "basket";
        }else{
			url::redirect("start", 302);
		}
	}
}
