<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Graph controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Graph_Controller extends System_Controller  {

    public function __construct()
    {
        parent::__construct();
        $this->template                = $this->add_view('template');
        if (isset($this->version) && $this->version == "tiny" )
                $this->template->graph   = $this->add_view('graph_tiny');
        else
                $this->template->graph   = $this->add_view('graph');
        $this->template->zoom_header   = $this->add_view('zoom_header');
        $this->template->zoom_header->graph_width  = ($this->config->conf['zgraph_width'] + 140);
        $this->template->zoom_header->graph_height = ($this->config->conf['zgraph_height'] + 230);
        $this->template->graph->icon_box           = $this->add_view('icon_box');
        $this->template->graph->icon_box->position = "graph";
        $this->template->graph->icon_box->xml_icon = TRUE;
        $this->template->graph->icon_box->pdf_icon = TRUE;
    }

    public function index()
    {
        $this->template->graph->graph_content = $this->add_view('graph_content');
        $this->template->graph->graph_content->graph_width = ($this->config->conf['graph_width'] + 85);
        $this->template->graph->graph_content->timerange_select = $this->add_view('timerange_select');
        $this->template->graph->header        = $this->add_view('header');
        $this->template->graph->search_box    = $this->add_view('search_box');
        $this->template->graph->service_box   = $this->add_view('service_box');
        $this->template->graph->basket_box    = $this->add_view('basket_box');
        $this->template->graph->widget_menu   = $this->add_view('widget_menu');
        $this->template->graph->graph_content->widget_graph  = $this->add_view('widget_graph');
        // Change the status box while multisite theme is in use
        if($this->theme == "multisite"){
            $this->template->graph->status_box = $this->add_view('multisite_box');
            $this->template->graph->status_box->base_url = $this->config->conf['multisite_base_url'];
            $this->template->graph->status_box->site     = $this->config->conf['multisite_site'];
        }else{
            $this->template->graph->status_box = $this->add_view('status_box');
        }
        // Service Details
        if($this->host != "" && $this->service != ""){
            $this->service = pnp::clean($this->service);
            $this->host    = pnp::clean($this->host);
            $this->url     = "?host=".urlencode($this->host)."&srv=".urlencode($this->service);
            $services      = $this->data->getServices($this->host);
            #Landingpage for mobile devices
            if($this->isMobileDevice()){
                url::redirect( "mobile/host/".urlencode($this->host)."/".urlencode($this->service) );
            }
            #print Kohana::debug($services);
            $this->data->buildDataStruct($this->host,$this->service,$this->view);
            $this->is_authorized = $this->auth->is_authorized($this->data->MACRO['AUTH_HOSTNAME'], $this->data->MACRO['AUTH_SERVICEDESC']); 

            $this->title = Kohana::lang('common.service-details') . " ". $this->host ." -> " . $this->data->MACRO['DISP_SERVICEDESC'];
            $this->template->graph->graph_content->graph_width = ($this->data->STRUCT[0]['GRAPH_WIDTH'] + 85);
            // Status Box Vars
            $this->template->graph->status_box->host     = $this->data->MACRO['DISP_HOSTNAME'];
            $this->template->graph->status_box->lhost    = $this->data->MACRO['HOSTNAME'];
            $this->template->graph->status_box->service  = $this->data->MACRO['DISP_SERVICEDESC'];
            $this->template->graph->status_box->lservice = $this->data->MACRO['SERVICEDESC'];
            $this->template->graph->status_box->timet    = date($this->config->conf['date_fmt'],intval($this->data->MACRO['TIMET']));
            // Service Box Vars
            $this->template->graph->service_box->services = $services;
            $this->template->graph->service_box->host = $this->host;
            // Timerange Box Vars
            $this->template->graph->timerange_box = $this->add_view('timerange_box');
            $this->template->graph->timerange_box->timeranges = $this->data->TIMERANGE;
            //
        // Host Overview
        }elseif($this->host != ""){
            $this->is_authorized = $this->auth->is_authorized($this->host); 
            $this->host    = pnp::clean($this->host);
            #Landingpage for mobile devices
            if($this->isMobileDevice()){
                url::redirect( "mobile/host/".urlencode($this->host) );
            }
            if($this->view == ""){
                $this->view = $this->config->conf['overview-range'];
            }
            $this->url     = "?host=".$this->host;
            $this->title   = Kohana::lang('common.start'). " ". $this->host;
            $services = $this->data->getServices($this->host);
            // Status Box Vars
            $this->template->graph->status_box->host    = $this->data->MACRO['DISP_HOSTNAME'];
            $this->template->graph->status_box->lhost   = $this->data->MACRO['HOSTNAME'];
            $this->template->graph->status_box->shost   = pnp::shorten($this->data->MACRO['DISP_HOSTNAME']);
            $this->template->graph->status_box->timet   = date($this->config->conf['date_fmt'],intval($this->data->MACRO['TIMET']));
            // Service Box Vars
            $this->template->graph->service_box->services = $services;
            $this->template->graph->service_box->host = $this->host;
            // Timerange Box Vars
            $this->template->graph->timerange_box = $this->add_view('timerange_box');
            $this->template->graph->timerange_box->timeranges = $this->data->TIMERANGE;

            $this->template->graph->icon_box->xml_icon = FALSE;

            $this->title = Kohana::lang('common.service-overview', $this->host);
            foreach($services as $service){
                if($service['state'] == 'active')
                    $this->data->buildDataStruct($this->host,$service['name'],$this->view);
            }
        }else{
            #Landingpage for mobile devices
            if($this->isMobileDevice()){
                url::redirect("mobile");
				return;
            }
            if($this->isAuthorizedFor('host_overview' ) ){
                $this->host = $this->data->getFirstHost();
                if(isset($this->host)){
                    url::redirect("graph?host=".$this->host);
                }else{
                    throw new Kohana_Exception('error.get-first-host');
                }
            }else{
                throw new Kohana_Exception('error.not_authorized_for_host_overview');
            }            
        }
        $this->template->graph->logo_box      = $this->add_view('logo_box');
        $this->template->graph->header->title = $this->title;
    }
}
