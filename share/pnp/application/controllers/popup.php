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
        if ( $this->view == "" ){
            $this->view = $this->config->conf['overview-range'];
        }

        $this->imgwidth = pnp::clean($this->input->get('width',$this->config->conf['popup-width']));

        $this->data->getTimeRange($this->start,$this->end,$this->view);

        if(isset($this->host) && isset($this->service)){
            $this->data->buildDataStruct($this->host,$this->service,$this->view,$this->source);
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
