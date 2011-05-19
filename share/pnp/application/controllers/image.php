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
    
        $this->tpl     = $this->input->get('tpl');
        $this->start   = $this->input->get('start');
        $this->end     = $this->input->get('end');
        $this->view    = $this->config->conf['overview-range']; //default value
        $this->source  = NULL;

        if($this->input->get('view') != "" )
            $this->view = $this->input->get('view') ;

        if($this->input->get('source') )
            $this->source = intval($this->input->get('source')) ;

        if($this->input->get('w') != "" )
            $this->rrdtool->config->conf['graph_width'] = intval($this->input->get('w'));
        if($this->input->get('graph_width') != "" )
            $this->rrdtool->config->conf['graph_width'] = intval($this->input->get('graph_width'));

        if($this->input->get('h') != "" )
            $this->rrdtool->config->conf['graph_height'] = intval($this->input->get('h'));
        if($this->input->get('graph_height') != "" )
            $this->rrdtool->config->conf['graph_height'] = intval($this->input->get('graph_height'));

        $this->data->getTimeRange($this->start,$this->end,$this->view);

        if(isset($this->tpl)){
            $this->tpl    = pnp::clean($this->tpl);
            $this->data->buildDataStruct('__special',$this->tpl,$this->view,$this->source);
            #print Kohana::debug($this->data->STRUCT);
            $image = $this->rrdtool->doImage($this->data->STRUCT[0]['RRD_CALL']);
            $this->rrdtool->streamImage($image);
        }elseif(isset($this->host) && isset($this->service)){
            $this->service = pnp::clean($this->service);
            $this->host    = pnp::clean($this->host);
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
