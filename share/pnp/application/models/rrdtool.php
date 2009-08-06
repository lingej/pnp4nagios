<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Retrieves and manipulates current status of hosts (and services?)
 */
class Rrdtool_Model extends Model
{

    private $RRD_CMD   = FALSE;
    /*
    *
    *
    */
    public function __construct(){
        $this->config = new Config_Model();
        $this->config->read_config();
        #print Kohana::debug($this->config->views);
    }

    private function rrdtool_execute() {
        $descriptorspec = array (
            0 => array ("pipe","r"), // stdin is a pipe that the child will read from
            1 => array ("pipe","w"), // stdout is a pipe that the child will write to
            2 => array ("pipe","w") // stderr is a pipe that the child will write to
        );

		if(!isset($this->config->conf['rrdtool']) )
	    	return FALSE;

		$rrdtool = $this->config->conf['rrdtool'] . " - ";
		$command = $this->RRD_CMD;
		$process = proc_open($rrdtool, $descriptorspec, $pipes);
		$debug = Array();
		$data = "";
        if (is_resource($process)) {
            fwrite($pipes[0], $command);
            fclose($pipes[0]);

            $data  = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
	    	return $data;
        }
    }

    public function doImage($RRD_CMD, $out='STDOUT') {
        $conf = $this->config->conf;
        # construct $command to rrdtool
        if(isset($conf['RRD_DAEMON_OPTS']) && $conf['RRD_DAEMON_OPTS'] != '' ){
            $command = " graph --daemon=" . $conf['RRD_DAEMON_OPTS'] . " - ";
        }else{
            $command = " graph - ";
        }

        if($conf['graph_opt']){
            $command .= $conf['graph_opt'];
        }
        if(is_numeric($conf['graph_width'])){
            $conf['graph_width'] = abs($conf['graph_width']);
            $command .= " --width=".$conf['graph_width'];
        }
        if(is_numeric($conf['graph_height'])){
            $conf['graph_height'] = abs($conf['graph_height']);
            $command .= " --height=".$conf['graph_height'];
        }

        $command .= $RRD_CMD;
		$this->RRD_CMD = $command;
        $data  = $this->rrdtool_execute();
		if($data){
        	return $data;
		}else{
			return FALSE;
        }
    }

	/*
	*
	*/
	public function doXport($RRD_CMD){
		$conf = $this->config->conf;
        if(isset($conf['RRD_DAEMON_OPTS']) && $conf['RRD_DAEMON_OPTS'] != '' ){
            $command = " xport --daemon=" . $conf['RRD_DAEMON_OPTS'] . " - ";
        }else{
            $command = " xport ";
        }
        $command .= $RRD_CMD;
		$this->RRD_CMD = $command;
        $data = $this->rrdtool_execute();
		$data = preg_replace('/OK.*/','',$data);
        if($data){
            return $data;
        }else{
            return FALSE;
        }
	}

    public function streamImage($data = FALSE){
		if (preg_match('/^ERROR/', $data)) {
	    	$debug['msg'] = trim($data);
	    	$debug['cmd'] = $this->format_rrd_debug( $this->config->conf['rrdtool'] . $this->RRD_CMD) ;
	    	#throw new Kohana_User_Exception('RRDtool Error', "<pre>".$debug['cmd']."</pre>");
	    	$im = @imagecreate(597, 150)
                 or die("Cannot Initialize new GD image stream");
            $background_color = imagecolorallocate($im, 255, 255, 255);
            $text_color = imagecolorallocate($im, 255, 0, 0);
            imagestring($im, 1, 8, 8,  $debug['msg'], $text_color);
	    	header("Content-type: image/png");	   
            imagepng($im);
            imagedestroy($im);
        }else{
	    	header("Content-type: image/png");	   
	    	echo $data;
		}
    }

	public function saveImage($data = FALSE){
        $img = array();
        $img['file'] = tempnam($this->config->conf['temp'],"PNP");
		if(!$fh = fopen($img['file'],'w') ){
			throw new Kohana_Exception('save-rrd-image', $img['file']);
		}
		fwrite($fh, $data);
		fclose($fh);
        if (function_exists('imagecreatefrompng')) {
                $image = imagecreatefrompng($img['file']);
                imagepng($image, $img['file']);
                list ($img['width'], $img['height'], $img['type'], $img['attr']) = getimagesize($img['file']);
        }
        return $img;
	}


    private function format_rrd_debug($data) {
        $data = preg_replace('/(HRULE|VDEF|DEF|CDEF|GPRINT|LINE|AREA|COMMENT)/','\<br>${1}', $data);
        return $data;
    }   
}
