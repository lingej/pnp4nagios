<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Image controller.
 *
 * @package    pnp4nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Image_Controller extends System_Controller  {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // Disable auto-rendering
        $this->auto_render = FALSE;
        
        if($this->input->get('w') != "" )
            $this->rrdtool->config->conf['graph_width'] = intval($this->input->get('w'));
        if($this->input->get('graph_width') != "" )
            $this->rrdtool->config->conf['graph_width'] = intval($this->input->get('graph_width'));

        if($this->input->get('h') != "" )
            $this->rrdtool->config->conf['graph_height'] = intval($this->input->get('h'));
        if($this->input->get('graph_height') != "" )
            $this->rrdtool->config->conf['graph_height'] = intval($this->input->get('graph_height'));

        if($this->input->get('graph_only') !== null)
            $this->rrdtool->config->conf['graph_only'] = 1;

        if($this->input->get('no_legend') !== null)
            $this->rrdtool->config->conf['no_legend'] = 1;

        $this->data->getTimeRange($this->start,$this->end,$this->view);

        if($this->tpl != ""){
            $this->data->buildDataStruct('__special',$this->tpl,$this->view,$this->source);
            #print Kohana::debug($this->data->STRUCT);
            $image = $this->rrdtool->doImage($this->data->STRUCT[0]['RRD_CALL']);
            $this->rrdtool->streamImage($image);
        }elseif(isset($this->host) && isset($this->service)){
            $this->data->buildDataStruct($this->host,$this->service,$this->view,$this->source);
            if($this->auth->is_authorized($this->data->MACRO['AUTH_HOSTNAME'], $this->data->MACRO['AUTH_SERVICEDESC']) === FALSE)
                $this->rrdtool->streamImage("ERROR: NOT_AUTHORIZED"); 

            #print Kohana::debug($this->data->STRUCT);
            if(sizeof($this->data->STRUCT) > 0){
                $image = $this->rrdtool->doImage($this->data->STRUCT[0]['RRD_CALL']);
            }else{
                $image = FALSE;
            }
            $this->rrdtool->streamImage($image); 
        }else{
            url::redirect("start", 302);
        }
    }


}
