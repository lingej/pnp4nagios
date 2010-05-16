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
        $this->template->graph = $this->add_view('graph');
        $this->host              = $this->input->get('host');
        $this->service           = $this->input->get('srv');
    }

    public function index()
    {
        $this->template->graph->graph_content = $this->add_view('graph_content');
        $this->template->graph->graph_content->graph_width = ($this->config->conf['graph_width'] + 85);
        $this->template->graph->graph_content->timerange_select = $this->add_view('timerange_select');
        $this->template->graph->header        = $this->add_view('header');
        $this->template->graph->search_box    = $this->add_view('search_box');
        $this->template->graph->service_box   = $this->add_view('service_box');
        $this->template->graph->status_box    = $this->add_view('status_box');
        $this->template->graph->basket_box    = $this->add_view('basket_box');
        $this->template->graph->widget_menu   = $this->add_view('widget_menu');
        $this->template->graph->graph_content->widget_graph  = $this->add_view('widget_graph');
        $this->template->graph->header->title        = "special Template";
    }
}
