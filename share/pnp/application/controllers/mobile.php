<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Mobile controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Mobile_Controller extends System_Controller  {

    public function __construct()
    {
        parent::__construct();
        $this->session->set('classic-ui',0);
        $this->template  = $this->add_view('mobile');
    }

    public function index()
    {
        $this->template->home = $this->add_view('mobile_home');
    }
    public function about()
    {
        $this->template->about = $this->add_view('mobile_about');
    }
    public function overview()
    {
        $this->template->overview = $this->add_view('mobile_overview');
        $this->template->overview->hosts = $this->data->getHosts();
    }
    public function host($host=NULL)
    {
        $this->template->host = $this->add_view('mobile_host');
        $this->is_authorized  = $this->auth->is_authorized($host);
        $this->template->host->hostname = $host;
        $this->template->host->services = $this->data->getServices($host);
    }
    public function graph($host=NULL, $service=NULL)
    {
        $this->template->graph = $this->add_view('mobile_graph');
        $this->data->buildDataStruct($host,$service,$this->view);
        $this->is_authorized = $this->auth->is_authorized($this->data->MACRO['AUTH_HOSTNAME'], $this->data->MACRO['AUTH_SERVICEDESC']);
    }
    public function search()
    {
        $this->template->query = $this->add_view('mobile_search');
        $query     = pnp::clean($this->input->post('term'));
        $result    = array();
        if(strlen($query)>=1) {
            $hosts = $this->data->getHosts();
            foreach($hosts as $host){
                if(preg_match("/$query/i",$host['name'])){
                    array_push($result,$host['name']);
                }
            }
        }
        $this->result = $result;
    }
    public function pages($page=NULL)
    {
        $this->is_authorized=TRUE;
        if($this->view == ""){
            $this->view = $this->config->conf['overview-range'];
        }

        $this->page = $page;
        if(is_null($this->page) ){
            $this->template->pages = $this->add_view('mobile_pages');
            $this->template->pages->pages = $this->data->getPages();
            return;
        }
        $this->data->buildPageStruct($this->page,$this->view);
        $this->template->pages = $this->add_view('mobile_graph');
    }
    public function special($tpl=NULL)
    {
        $this->tpl = $tpl;
        if(is_null($this->tpl) ){
            $this->template->special = $this->add_view('mobile_special');
            $this->template->special->templates = $this->data->getSpecialTemplates();
            return;
        }
        $this->data->buildDataStruct('__special',$this->tpl,$this->view);
        $this->template->special = $this->add_view('mobile_graph_special');
    }
    public function go($goto=FALSE)
    {
        if($goto == 'classic'){
            $this->session->set('classic-ui',1);
            url::redirect("graph");    
        }
    }
}
