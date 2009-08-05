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
		$host     = false;
		$service  = false;
       	$basket   = array();

		if($action == "list"){
        	$basket = $this->session->get("basket");
			if(is_array($basket) && sizeof($basket) > 0){
				foreach($basket as $item){
					echo "<span id=\"basket_action_remove\"><a title=\"Remove Item\" id=\"".$item."\"><img width=12px height=12px src=\"".url::base()."media/images/remove.png\"></a>".$item."</span><br>\n";
				}
			}
		}elseif($action == "add"){
			$item = $_POST['item'];
        	$basket = $this->session->get("basket");
			if(!is_array($basket)){
        		$basket[] = "$item";
			}else{
				if(!in_array($item,$basket)){
        			$basket[] = $item;
				}
			}
        	$this->session->set("basket", $basket);
			foreach($basket as $item){
				echo "<span id=\"basket_action_remove\"><a title=\"Remove Item\" id=\"".$item."\"><img width=12px height=12px src=\"".url::base()."media/images/remove.png\"></a>".$item."</span><br>\n";
			}
		}elseif($action == "remove"){
        	$basket = $this->session->get("basket");
			$item_to_remove = $_POST['item'];
			$new_basket = array();
			foreach($basket as $item){
				if($item ==  $item_to_remove){
					continue;
				}
				$new_basket[] = $item;
			}
			$basket = $new_basket;
			$this->session->set("basket", $basket);
			foreach($basket as $item){
				echo "<span id=\"basket_action_remove\"><a title=\"Remove Item\" id=\"".$item."\"><img width=12px height=12px src=\"".url::base()."media/images/remove.png\"></a>".$item."</span><br>\n";
			}
		}elseif($action == "remove-all"){
        	$this->session->delete("basket");
		}else{
			echo "Action $action not known";
		}
       	$basket = $this->session->get("basket");
		if(is_array($basket) && sizeof($basket) == 0){
			echo "basket is empty";
		}else{
			echo "<a class=\"multi0\" href=\"".url::base()."page/basket\">ajax show basket</a>";
		}
	}

}
