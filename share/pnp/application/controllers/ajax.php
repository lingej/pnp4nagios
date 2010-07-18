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
        $this->auto_render = FALSE;
        // Disable auto-rendering
        $this->auto_render = FALSE;
    }

    public function index(){
        url::redirect("start", 302); 
    }

    public function search() {
        $query     = $this->input->get('term');
        $result    = array();
        if(strlen($query)>=1) {
            $hosts = $this->data->getHosts();
            foreach($hosts as $host){
                if(preg_match("/$query/i",$host['name'])){
                    array_push($result,$host['name']);
                }
            }
            echo json_encode($result);
        }
    }

    public function remove($what){
        if($what == 'timerange'){
            $this->session->delete('start');
            $this->session->delete('end');
            $this->session->set('timerange-reset', 1);
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
                    echo "<span id=\"basket_action_remove\"><a title=\"Remove ".$item."\" id=\"".$item."\"><img width=12px height=12px src=\"".url::base()."media/images/remove.png\"></a>".pnp::shorten($item)."</span><br>\n";
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
                echo "<span id=\"basket_action_remove\"><a title=\"Remove ".$item."\" id=\"".$item."\"><img width=12px height=12px src=\"".url::base()."media/images/remove.png\"></a>".pnp::shorten($item)."</span><br>\n";
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
                echo "<span id=\"basket_action_remove\"><a title=\"Remove ".$item."\" id=\"".$item."\"><img width=12px height=12px src=\"".url::base()."media/images/remove.png\"></a>".pnp::shorten($item)."</span><br>\n";
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
            echo "<a class=\"multi0\" href=\"".url::base(TRUE)."page/basket\">show basket</a>";
        }
    }

}
