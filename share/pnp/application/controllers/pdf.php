<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Graph controller.
 *
 * @package    PNP4Nagios
 * @author     Joerg Linge
 * @license    GPL
 */
class Pdf_Controller extends System_Controller  {

	#public $csrf_config = false;
	#public $route_config = false;

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{

		$this->host              = $this->input->get('host');
		$this->service           = $this->input->get('srv');
		$start   = $this->input->get('start');
		$end     = $this->input->get('end');
		$view    = FALSE;

		if(isset($_GET['view']) && $_GET['view'] != "" )
			$view = pnp::clean($_GET['view']);

		$this->data->getTimeRange($start,$end,$view);

		// Service Details
		if($this->host != "" && $this->service != ""){
		    $this->service = pnp::clean($this->service);
		    $this->host    = pnp::clean($this->host);
			$this->url      = "?host=".$this->host."&srv=".$this->service;
		    $services      = $this->data->getServices($this->host);
		    $this->data->buildDataStruct($this->host,$this->service,$view);
		// Host Overview
		}elseif($this->host != ""){
		    $this->host    = pnp::clean($this->host);
			if($view == FALSE){
				$view = $this->config->conf['overview-range'];
			}
			$this->url     = "?host=".$this->host;
		    $this->title   = "Start $this->host";
		    $services = $this->data->getServices($this->host);
		    foreach($services as $service){
				if($service['state'] == 'active')
		   	    	$this->data->buildDataStruct($this->host,$service['name'],$view);
		    }
		}else{
		    $this->host = $this->data->getFirstHost();
		    if(isset($this->host)){
		    	url::redirect("/graph?host=$this->host");
		    }else{
				throw new Kohana_User_Exception('Hostname not set ;-)', "RTFM my Friend, RTFM!");
		    }			
		}
		/*
		* PDF Output
		*/
		$pdf = new PDF;
		$pdf->AliasNbPages();
		$pdf->SetAutoPageBreak('off');
		$pdf->SetMargins(12.5,25,10);
		$pdf->AddPage();

		$pdf->SetCreator('Created with PNP');
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(120, 4, '', 0, 1, 'L');
		// Title
		$pdf->CELL(120, 5, $this->host." -- ".$this->service, 0, 1);
		foreach($this->data->STRUCT as $data){
			$pdf->CELL(120, 5, $data['TIMERANGE']['title']." ".$data['TIMERANGE']['f_start']." <-> ".$data['TIMERANGE']['f_end'], 0, 1);
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
            //Move to the right
            $this->Cell(80);
            //Title
            $this->Cell(30, 10, "Title", 0, 1, 'C');
            //Line break
            #$this->Ln(20);
        }

        //Page footer
        function Footer() {
            //Position at 1.5 cm from bottom
            $this->SetY(-20);
            //Arial italic 8
            $this->SetFont('Arial', 'I', 8);
            //Page number
            $this->Cell(0, 10, "Footer" . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

