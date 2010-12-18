<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Popup controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge 
 * @license    GPL
 */
class Popup_Controller extends System_Controller  {


    public function __construct()
    {
        parent::__construct();
        $this->template          = $this->add_view('popup');
    }

    public function index()
    {
        $this->source  = $this->input->get('source');
        $this->view    = "";

        if(isset($_GET['view']) && $_GET['view'] != "" ){
            $this->view = pnp::clean($_GET['view']);
        }else{
            $this->view = $this->config->conf['overview-range'];
        }

        if(isset($this->config->conf['popup-width']) &&$this->config->conf['popup-width'] != ""){ 
            $this->imgwidth = $this->config->conf['popup-width'];
        }else{
            $this->imgwidth = FALSE;
        }

        $this->data->getTimeRange($this->start,$this->end,$this->view);

        if(isset($this->host) && isset($this->service)){
            $this->service = pnp::clean($this->service);
            $this->host    = pnp::clean($this->host);
            $this->data->buildDataStruct($this->host,$this->service,$this->view);
            $this->template->host      = $this->host;
            $this->template->srv       = $this->service;
            $this->template->view      = $this->view;
            $this->template->source    = $this->source;
            $this->template->end       = $this->end;
            $this->template->start     = $this->start;
            $this->template->imgwidth  = $this->imgwidth;
        }else{
            url::redirect("/graph");
        }
    }
}
