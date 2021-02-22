<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * PDF controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Pdf_Controller extends System_Controller  {

    public function __construct(){
        parent::__construct();

        $this->use_bg           = 0;
        $this->bg               = $this->config->conf['background_pdf'];
        $this->pdf_page_size    = $this->config->conf['pdf_page_size'];
        $this->pdf_margin_left  = $this->config->conf['pdf_margin_left'];
        $this->pdf_margin_top   = $this->config->conf['pdf_margin_top'];
        $this->pdf_margin_right = $this->config->conf['pdf_margin_right'];

        // Define PDF background per url option 
        if(isset($this->bg) && $this->bg != ""){
            if( is_readable( Kohana::config( 'core.pnp_etc_path')."/".$this->bg ) ){
                $this->bg = Kohana::config('core.pnp_etc_path')."/".$this->bg;
            }else{
                $this->bg = $this->config->conf['background_pdf'];
            }
        }
        // Use PDF background if readable
        if(is_readable($this->bg)){
            $this->use_bg = 1;
        }

    }

    public function index(){

        $this->tpl       = pnp::clean($this->input->get('tpl'));
        $this->type      = "normal";

        $this->data->getTimeRange($this->start,$this->end,$this->view);

        // Service Details
        if($this->host != "" && $this->service != ""){
            $this->data->buildDataStruct($this->host,$this->service,$this->view);
        // Host Overview
        }elseif($this->host != ""){
            if($this->view == ""){
                $this->view = $this->config->conf['overview-range'];
            }
            $services = $this->data->getServices($this->host);
            foreach($services as $service){
                if($service['state'] == 'active')
                       $this->data->buildDataStruct($this->host,$service['name'],$this->view);
            }
        // Special Templates
        }elseif($this->tpl != ""){
            $this->data->buildDataStruct('__special',$this->tpl,$this->view);
            $this->type = 'special';
        }else{
            $this->host = $this->data->getFirstHost();
            if(isset($this->host)){
                url::redirect("/graph?host=$this->host");
            }else{
                throw new Kohana_User_Exception('Hostname not set ;-)', "RTFM my Friend, RTFM!");
            }            
        }
        #throw new Kohana_Exception(print_r($this->data->STRUCT,TRUE));
        /*
        * PDF Output
        */
        $pdf = new PDF("P", "mm", $this->pdf_page_size);
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak('off');
        $pdf->SetMargins($this->pdf_margin_left,$this->pdf_margin_top,$this->pdf_margin_right);
        $pdf->AddPage();
        if($this->use_bg){
                $pdf->setSourceFile($this->bg);
                $tplIdx = $pdf->importPage(1,'/MediaBox');
                $pdf->useTemplate($tplIdx);
        }
        $pdf->SetCreator('Created with PNP');
        $pdf->SetFont('Arial', '', 10);
        // Title
        $header = TRUE;
        foreach($this->data->STRUCT as $key=>$data){
            if($key != 0){
                $header = FALSE;
            } 
            if ($pdf->GetY() > 200) {
                $pdf->AddPage();
                if($this->use_bg){$pdf->useTemplate($tplIdx);}
            }
            if($this->type == 'normal'){
                if($data['LEVEL'] == 0){
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->CELL(120, 10, $data['MACRO']['DISP_HOSTNAME']." -- ".$data['MACRO']['DISP_SERVICEDESC'], 0, 1);
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->CELL(120, 5, $data['TIMERANGE']['title']." (".$data['TIMERANGE']['f_start']." - ".$data['TIMERANGE']['f_end'].")", 0, 1);
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->CELL(120, 5, "Datasource ".$data["ds_name"], 0, 1);
                }else{
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->CELL(120, 5, "Datasource ".$data["ds_name"], 0, 1);
                }
            }elseif($this->type == 'special'){ 
                if($header){
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->CELL(120, 10, $data['MACRO']['TITLE'], 0, 1);
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->CELL(120, 5, $data['TIMERANGE']['title']." (".$data['TIMERANGE']['f_start']." - ".$data['TIMERANGE']['f_end'].")", 0, 1);
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->CELL(120, 5, "Datasource ".$data["ds_name"], 0, 1);
                }else{
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->CELL(120, 5, $data['TIMERANGE']['title']." (".$data['TIMERANGE']['f_start']." - ".$data['TIMERANGE']['f_end'].")", 0, 1);
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->CELL(120, 5, "Datasource ".$data["ds_name"], 0, 1);
                }
            }
            $image = $this->rrdtool->doImage($data['RRD_CALL'],$out='PDF');
            $img = $this->rrdtool->saveImage($image);
            $Y = $pdf->GetY();
            $cell_height = ($img['height'] * 0.23);
            $cell_width = ($img['width'] * 0.23);
            $pdf->Image($img['file'], $this->pdf_margin_left, $Y, $cell_width, $cell_height, 'PNG');
            $pdf->CELL(120, $cell_height, '', 0, 1);
            unlink($img['file']);
        }
        $pdf->Output("pnp4nagios.pdf","I");

    }

    public function page($page){
        $this->start     = $this->input->get('start');
        $this->end       = $this->input->get('end');
        $this->view      = "";

        if(isset($_GET['view']) && $_GET['view'] != "" ){
            $this->view = pnp::clean($_GET['view']);
        }

        $this->data->getTimeRange($this->start,$this->end,$this->view);
        $this->data->buildPageStruct($page,$this->view);
        // Define PDF background per url option
        if(isset($this->data->PAGE_DEF['background_pdf'])){
            if( is_readable( Kohana::config( 'core.pnp_etc_path')."/".$this->data->PAGE_DEF['background_pdf'] ) ){
                $this->bg = Kohana::config('core.pnp_etc_path')."/".$this->data->PAGE_DEF['background_pdf'];
            }
        }
        /*
        * PDF Output
        */
        $pdf = new PDF("P", "mm", $this->pdf_page_size);
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak('off');
        $pdf->SetMargins($this->pdf_margin_left,$this->pdf_margin_top,$this->pdf_margin_right);
        $pdf->AddPage();
        if($this->use_bg){
                $pdf->setSourceFile($this->bg);
                $tplIdx = $pdf->importPage(1,'/MediaBox');
                $pdf->useTemplate($tplIdx);
        }

        $pdf->SetCreator('Created with PNP');
        $pdf->SetFont('Arial', '', 10);
        // Title
        foreach($this->data->STRUCT as $data){
            if ($pdf->GetY() > 200) {
                $pdf->AddPage();
                if($this->use_bg){$pdf->useTemplate($tplIdx);}
            }
            if($data['LEVEL'] == 0){
                $pdf->SetFont('Arial', '', 12);
                $pdf->CELL(120, 10, $data['MACRO']['DISP_HOSTNAME']." -- ".$data['MACRO']['DISP_SERVICEDESC'], 0, 1);
                $pdf->SetFont('Arial', '', 10);
                $pdf->CELL(120, 5, $data['TIMERANGE']['title']." (".$data['TIMERANGE']['f_start']." - ".$data['TIMERANGE']['f_end'].")", 0, 1);
                $pdf->SetFont('Arial', '', 8);
                $pdf->CELL(120, 5, "Datasource ".$data["ds_name"], 0, 1);
            }else{
                $pdf->SetFont('Arial', '', 8);
                $pdf->CELL(120, 5, "Datasource ".$data["ds_name"], 0, 1);
            }
            $image = $this->rrdtool->doImage($data['RRD_CALL'],$out='PDF');
            $img = $this->rrdtool->saveImage($image);
            $Y = $pdf->GetY();
            $cell_height = ($img['height'] * 0.23);
            $cell_width = ($img['width'] * 0.23);
            $pdf->Image($img['file'], $this->pdf_margin_left, $Y, $cell_width, $cell_height, 'PNG');
            $pdf->CELL(120, $cell_height, '', 0, 1);
            unlink($img['file']);
        }
        $pdf->Output("pnp4nagios.pdf","I");
    }

    public function basket(){
        $this->start     = $this->input->get('start');
        $this->end       = $this->input->get('end');
        $this->view      = "";
        if(isset($_GET['view']) && $_GET['view'] != "" ){
            $this->view = pnp::clean($_GET['view']);
        }
        $this->data->getTimeRange($this->start,$this->end,$this->view);
        $basket = $this->session->get("basket");
        if(is_array($basket) && sizeof($basket) > 0){
             $this->data->buildBasketStruct($basket,$this->view);
        }
        //echo Kohana::debug($this->data->STRUCT);
        /*
        * PDF Output
        */
        $pdf = new PDF("P", "mm", $this->pdf_page_size);
        $pdf->AliasNbPages();
        $pdf->SetAutoPageBreak('off');
        $pdf->SetMargins($this->pdf_margin_left,$this->pdf_margin_top,$this->pdf_margin_right);
        $pdf->AddPage();
        if($this->use_bg){
                $pdf->setSourceFile($this->config->conf['background_pdf']);
                $tplIdx = $pdf->importPage(1,'/MediaBox');
                $pdf->useTemplate($tplIdx);
        }

        $pdf->SetCreator('Created with PNP');
        $pdf->SetFont('Arial', '', 10);
        // Title
        foreach($this->data->STRUCT as $data){
            if ($pdf->GetY() > 200) {
                $pdf->AddPage();
                if($this->use_bg){$pdf->useTemplate($tplIdx);}
            }
            if($data['LEVEL'] == 0){
                $pdf->SetFont('Arial', '', 12);
                $pdf->CELL(120, 10, $data['MACRO']['DISP_HOSTNAME']." -- ".$data['MACRO']['DISP_SERVICEDESC'], 0, 1);
                $pdf->SetFont('Arial', '', 10);
                $pdf->CELL(120, 5, $data['TIMERANGE']['title']." (".$data['TIMERANGE']['f_start']." - ".$data['TIMERANGE']['f_end'].")", 0, 1);
                $pdf->SetFont('Arial', '', 8);
                $pdf->CELL(120, 5, "Datasource ".$data["ds_name"], 0, 1);
            }else{
                $pdf->SetFont('Arial', '', 8);
                $pdf->CELL(120, 5, "Datasource ".$data["ds_name"], 0, 1);
            }
            $image = $this->rrdtool->doImage($data['RRD_CALL'],$out='PDF');
            $img = $this->rrdtool->saveImage($image);
            $Y = $pdf->GetY();
            $cell_height = ($img['height'] * 0.23);
            $cell_width = ($img['width'] * 0.23);
            $pdf->Image($img['file'], $this->pdf_margin_left, $Y, $cell_width, $cell_height, 'PNG');
            $pdf->CELL(120, $cell_height, '', 0, 1);
            unlink($img['file']);
        }
        $pdf->Output("pnp4nagios.pdf","I");

    }
}


/*
+
*
*/
require Kohana::find_file('vendor/fpdf', 'fpdf');
require Kohana::find_file('vendor/fpdf', 'fpdi');
class PDF extends FPDI {
        //Page header
        function Header() {
            //Arial bold 10 
            $this->SetFont('Arial', 'B', 10);
        }

        //Page footer
        function Footer() {
            //Position at 1.5 cm from bottom
            $this->SetY(-20);
            //Arial italic 8
            $this->SetFont('Arial', 'I', 8);
            //Page number
            $this->Cell(0, 10, $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

