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
        // Disable auto-rendering
        $this->auto_render = FALSE;
    }

    public function index(){
        url::redirect("start", 302); 
    }

    public function search() {
        $query     = pnp::clean($this->input->get('term'));
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
	
	public function filter($what){
        if($what == 'set-sfilter'){
            $this->session->set('sfilter', $_POST['sfilter']);
        }elseif($what == 'set-spfilter'){
			$this->session->set('spfilter', $_POST['spfilter']);
		}elseif($what == 'set-pfilter'){
            $this->session->set('pfilter', $_POST['pfilter']);
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
                printf("<li class=\"ui-state-default %s\" id=\"%s\"><a title=\"%s\" id=\"%s\"><img width=12px height=12px src=\"%smedia/images/remove.png\"></a>%s</li>\n",
                        "basket_action_remove",
                        $item,
                        $item,
                        Kohana::lang('common.basket-remove', $item),
                        url::base(),
                        pnp::shorten($item)
                      );
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
                printf("<li class=\"ui-state-default %s\" id=\"%s\"><a title=\"%s\" id=\"%s\"><img width=12px height=12px src=\"%smedia/images/remove.png\"></a>%s</li>\n",
                        "basket_action_remove",
                        $item,
                        $item,
                        Kohana::lang('common.basket-remove', $item),
                        url::base(),
                        pnp::shorten($item)
                      );
            }
        }elseif($action == "sort"){
            $items = $_POST['items'];
            $basket = explode(',', $items);
            array_pop($basket);
            $this->session->set("basket", $basket);
            foreach($basket as $item){
                printf("<li class=\"ui-state-default %s\" id=\"%s\"><a title=\"%s\" id=\"%s\"><img width=12px height=12px src=\"%smedia/images/remove.png\"></a>%s</li>\n",
                        "basket_action_remove",
                        $item,
                        $item,
                        Kohana::lang('common.basket-remove', $item),
                        url::base(),
                        pnp::shorten($item)
                      );
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
                printf("<li class=\"ui-state-default %s\" id=\"%s\"><a title=\"%s\" id=\"%s\"><img width=12px height=12px src=\"%smedia/images/remove.png\"></a>%s</li>\n",
                        "basket_action_remove",
                        $item,
                        $item,
                        Kohana::lang('common.basket-remove', $item),
                        url::base(),
                        pnp::shorten($item)
                      );
            }
        }elseif($action == "clear"){
            $this->session->delete("basket");
        }else{
            echo "Action $action not known";
        }
        $basket = $this->session->get("basket");
        if(is_array($basket) && sizeof($basket) == 0){
            echo Kohana::lang('common.basket-empty');
        }else{
            echo "<div align=\"center\" class=\"p2\">\n";
            echo "<button id=\"show\">".Kohana::lang('common.basket-show')."</button>\n";
            echo "<button id=\"clear\">".Kohana::lang('common.basket-clear')."</button>\n";
            echo "</div>\n";
        }
    }

}
