<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Ajax controller.
 *
 * @package    PNP4Nagios 
 * @author     Joerg Linge 
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

	public function basket($action=FALSE,$host=FALSE,$service=FALSE){
		// Disable auto-rendering
        $this->auto_render = FALSE;

       	$basket = array();

		if($action == "list"){
			echo "List Action $host $service";
        	$basket = $this->session->get("basket");
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
		}elseif($action == "add"){
			if($host==FALSE && $service==FALSE){
				return;
			}

			echo "Add Action $host $service";
        	$basket = $this->session->get("basket");
			if(!is_array($basket)){
        		$basket[] = "$host::$service";
			}else{
				if(in_array("$host::$service",$basket)){
					echo "already in";
				}else{
        			$basket[] = "$host::$service";
				}
			}
        	$this->session->set("basket", $basket);
		}elseif($action == "delete"){
			if($host==FALSE && $service==FALSE){
				return;
			}
			echo "Delete All Action";
        	$basket = $this->session->get("basket");
			$new_basket = array();
			foreach($basket as $item){
				if($item == "$host::$service"){
					continue;
				}
				$new_basket[] = $item;
			}
			$this->session->set("basket", $new_basket);
		}elseif($action == "delete_all"){
        	$this->session->delete("basket");
			echo "Delete All Action";
		}else{
			echo "Action $action not known";
		}
	}

}
