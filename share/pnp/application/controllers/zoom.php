<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Zoom controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge 
 * @license    GPL
 */
class Zoom_Controller extends System_Controller  {


    public function __construct()
    {
        parent::__construct();
        $this->template          = $this->add_view('zoom');
        #$this->tpl               = $this->input->get('tpl');
        $this->graph_width       = $this->config->conf['zgraph_width'];
        $this->graph_height      = $this->config->conf['zgraph_height'];
    }

    public function index()
    {
        #$this->source  = intval($this->input->get('source'));
        #$this->view    = "";

        #if(isset($_GET['view']) && $_GET['view'] != "" ){
        #    $this->view = pnp::clean($_GET['view']);
        #}else{
        #    $this->view = $this->config->conf['overview-range'];
        #}

        #
        #  Limit startto 2000/01/01
        #
        $start_limit = strtotime("2000/01/01");
        $this->start = abs((int)$this->start);
        if($this->start < $start_limit)
        $this->start = $start_limit;
        #
        # Limit end to now + one hour 
        #    
        $end_limit = time() + 3600;
        $this->end = abs((int)$this->end);
        if($this->end > $end_limit)
            $this->end = $end_limit;

        $this->data->getTimeRange($this->start,$this->end,$this->view);

        if(isset($this->tpl) && $this->tpl != 'undefined' ){
            if($this->start && $this->end ){
                    $this->session->set("start", $this->start);
                    $this->session->set("end", $this->end);
            }
            $this->template->tpl     = $this->tpl;
            $this->template->view    = $this->view;
            $this->template->source  = $this->source;
            $this->template->end     = $this->end;
            $this->template->start   = $this->start;
            $this->url               = "?tpl=".$this->tpl;
            $this->template->graph_height = $this->graph_height;
            $this->template->graph_width  = $this->graph_width;
        }elseif(isset($this->host) && isset($this->service)){
            if($this->start && $this->end ){
                $this->session->set("start", $this->start);
                $this->session->set("end", $this->end);
            }
            $this->template->host    = $this->host;
            $this->template->srv     = $this->service;
            $this->template->view    = $this->view;
            $this->template->source  = $this->source;
            $this->template->end     = $this->end;
            $this->template->start   = $this->start;
            $this->url               = "?host=".urlencode($this->host)."&srv=".urlencode($this->service);
            $this->template->graph_height = $this->graph_height;
            $this->template->graph_width  = $this->graph_width;
        }else{
            url::redirect("/graph");
        }
    }
}
