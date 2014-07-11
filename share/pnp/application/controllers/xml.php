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
    }

    public function index()
    {
        $this->auto_render = FALSE;
        if($this->service == "" && $this->host == ""){
            url::redirect("graph", 302);
        }
        $this->data->readXML($this->host, $this->service);
        if($this->auth->is_authorized($this->data->MACRO['AUTH_HOSTNAME'], $this->data->MACRO['AUTH_SERVICEDESC']) === FALSE){
            header('Content-Type: application/xml');
            print "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
            print "<NAGIOS>\n";
            print "<ERROR>not authorized</ERROR>\n";
            print "</NAGIOS>\n";
            exit;
        }else{
            $xmlfile = $this->config->conf['rrdbase'].$this->host."/".$this->service.".xml";
            if(is_readable($xmlfile)){
                $fh = fopen($xmlfile, 'r');
                header('Content-Type: application/xml');
                fpassthru($fh);
                fclose($fh);
                exit;
            }else{
                header('Content-Type: application/xml');
                print "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
                print "<NAGIOS>\n";
                print "<ERROR>file not found</ERROR>\n";
                print "</NAGIOS>\n";
            }
        }
    }
}
