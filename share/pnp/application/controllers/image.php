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
    
        $tpl     = $this->input->get('tpl');
        $host    = $this->input->get('host');
        $service = $this->input->get('srv');
        $start   = $this->input->get('start');
        $end     = $this->input->get('end');
        $view    = 0; //fake value
        $source  = "";

        if($this->input->get('view') != "" )
            $view = $this->input->get('view') ;

        if($this->input->get('source') )
            $source = $this->input->get('source') ;

        $this->data->getTimeRange($start,$end,$view);

        if(isset($tpl)){
            $tpl    = pnp::clean($tpl);
            $this->data->buildDataStruct('__special',$tpl,$view,$source);
            #print Kohana::debug($this->data->STRUCT);
            $image = $this->rrdtool->doImage($this->data->STRUCT[0]['RRD_CALL']);
            $this->rrdtool->streamImage($image);
        }elseif(isset($host) && isset($service)){
            $service = pnp::clean($service);
            $host    = pnp::clean($host);
            $this->data->buildDataStruct($host,$service,$view,$source);
            #print Kohana::debug($this->data->STRUCT);
            $image = $this->rrdtool->doImage($this->data->STRUCT[0]['RRD_CALL']);
            $this->rrdtool->streamImage($image); 
        }else{
            print "ERROR";
        }
    }


}
