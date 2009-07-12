<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Xml controller.
 *
 * @package    PNP4Nagios
 * @author     Jorg Linge
 * @license    GPL
 */
class Xml_Controller extends System_Controller  {

	public function __construct()
	{
		parent::__construct();
		$this->config->read_config();
		$this->host    = $this->input->get('host');
		$this->service = $this->input->get('srv');
	}

	public function index()
	{
		$this->auto_render = FALSE;
		if(isset($this->host) && isset($this->service)){
		    $this->service = pnp::clean($this->service);
		    $this->host    = pnp::clean($this->host);
		}elseif(isset($this->host)){
		    $this->host    = pnp::clean($this->host);
			$this->service = "_HOST_";
		}else{
			throw new Kohana_User_Exception('No Options', "RTFM my Friend, RTFM!");
		}
		$xmlfile = $this->config->conf['rrdbase'].$this->host."/".$this->service.".xml";
		if(is_readable($xmlfile)){
			$fh = fopen($xmlfile, 'r');
			header('Content-Type: application/xml');
			fpassthru($fh);
			fclose($fh);
			exit;
		}
		throw new Kohana_User_Exception('File Not found', "File $xmlfile not found");
	}
}
