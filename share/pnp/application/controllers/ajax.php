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

	public function basket($action=FALSE){
		// Disable auto-rendering
        $this->auto_render = FALSE;
		$host = false;
		$service=false;
       	$basket = array();

		if($action == "list"){
			echo "List Action $host $service";
        	$basket = $this->session->get("basket");
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
		}elseif($action == "add"){
			$item = $_POST['item'];
			echo "<li id=\"$item\">$item<a id=\"$item\" href=ajax/basket/delete/$item onClick=\"return false;\">del</a></li>\n";
        	$basket = $this->session->get("basket");
			if(!is_array($basket)){
        		$basket[] = "$item";
			}else{
				if(!in_array($item,$basket)){
        			$basket[] = $item;
				}
			}
        	$this->session->set("basket", $basket);
		}elseif($action == "delete"){
        	$basket = $this->session->get("basket");
			$item_to_remove = $_POST['item'];
			$new_basket = array();
			foreach($basket as $item){
				if($item ==  $item_to_remove){
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
