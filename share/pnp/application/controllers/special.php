<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Graph controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Special_Controller extends System_Controller  {

    public function __construct()
    {
        parent::__construct();
        $this->template        = $this->add_view('template');
        $this->template->graph = $this->add_view('graph');
        $this->templates       = $this->data->getSpecialTemplates();
        $this->data->GRAPH_TYPE = 'special';
        if($this->tpl == ''){
            if($this->templates)
                $this->tpl = $this->templates[0];
                url::redirect('special?tpl='.$this->tpl, 302);
        }
    }

    public function index(){
	$this->url = "?tpl=".$this->tpl;
        $this->template->zoom_header   = $this->add_view('zoom_header');
        $this->template->zoom_header->graph_width  = ($this->config->conf['graph_width'] + 140);
        $this->template->zoom_header->graph_height = ($this->config->conf['graph_height'] + 230);
        $this->template->graph->graph_content = $this->add_view('graph_content_special');
        $this->template->graph->graph_content->graph_width = ($this->config->conf['graph_width'] + 85);
        $this->template->graph->graph_content->timerange_select = $this->add_view('timerange_select');
        $this->template->graph->header        = $this->add_view('header');
        $this->template->graph->search_box    = $this->add_view('search_box');
        $this->template->graph->service_box   = $this->add_view('special_templates_box');
        #$this->template->graph->status_box    = $this->add_view('status_box');
        #$this->template->graph->basket_box    = $this->add_view('basket_box');
        $this->template->graph->widget_menu   = $this->add_view('widget_menu');
        $this->template->graph->graph_content->widget_graph  = $this->add_view('widget_graph');
        #print Kohana::debug($services);
        $this->data->buildDataStruct('__special',$this->tpl,$this->view);
        $this->template->graph->icon_box      = $this->add_view('icon_box');
        $this->template->graph->icon_box->position = "special";
        $this->template->graph->logo_box      = $this->add_view('logo_box');
        // Timerange Box Vars
        $this->template->graph->timerange_box = $this->add_view('timerange_box');
        $this->template->graph->timerange_box->timeranges = $this->data->TIMERANGE;
        $this->template->graph->header->title        = $this->data->MACRO['TITLE'];
        //print Kohana::debug($this->data);
    }

}
