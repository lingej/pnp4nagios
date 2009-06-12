<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Default controller.
 * Does not require login but should display default page
 *
 * @package    NINJA
 * @author     op5 AB
 * @license    GPL
 */
class Ajax_Controller extends System_Controller  {

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		url::redirect("start", 302); 
	}

	public function search() {
		// Disable auto-rendering
                $this->auto_render = FALSE;
	
		$query     = $this->input->get('query');
		$result    = array();
		$result['query'] = $query;
		$result['suggestions'] = array();
	        if(strlen($query)>=1) {
                     $hosts = $this->data->getHosts();
                     foreach($hosts as $host){
                         if(preg_match("/$query/i",$host['name'])){
				array_push($result['suggestions'],$host['name']);
                         }
                     }
		     echo json_encode($result);
                }
	}

	public function popup() {
	    $host    = $this->input->get('host');
	    $service = $this->input->get('srv');
	    $timet   = time();
	    echo html::image("image?host=$host&srv=$service&time=$timet");
	}
}
