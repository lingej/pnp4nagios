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
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
			if($stderr){
				$data = "ERROR: ".$stderr;
			}
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

		$width = 0;
		$height = 0;
		if ($out == 'PDF'){
        	if($conf['pdf_graph_opt']){
            	$command .= $conf['pdf_graph_opt'];
        	}
			if (isset($conf['pdf_width']) && is_numeric($conf['pdf_width'])){
				$width = abs($conf['pdf_width']);
			}
			if (isset($conf['pdf_height']) && is_numeric($conf['pdf_height'])){
				$height = abs($conf['pdf_height']);
			}
		}else{
        	if($conf['graph_opt']){
            	$command .= $conf['graph_opt'];
        	}
        	if(is_numeric($conf['graph_width'])){
            	$width = abs($conf['graph_width']);
        	}
        	if(is_numeric($conf['graph_height'])){
            	$height = abs($conf['graph_height']);
        	}
		}

		if ($width > 0){
			$command .= " --width=$width";
		}
		if ($height > 0){
			$command .= " --height=$height";
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
            $command = " xport --daemon=" . $conf['RRD_DAEMON_OPTS'];
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
	    	$data .= $this->format_rrd_debug( $this->config->conf['rrdtool'] . $this->RRD_CMD) ;
            // Set font size
            $font_size = 1.5;

            $ts=explode("\n",$data);
            $width=0;
            foreach ($ts as $k=>$string) {
                $width=max($width,strlen($string));
            }

  			$width  = imagefontwidth($font_size)*$width;
			if($width <= $this->config->conf['graph_width']+100){
				$width = $this->config->conf['graph_width']+100;
			}
  			$height = imagefontheight($font_size)*count($ts);
			if($height <= $this->config->conf['graph_height']+60){
				$height = $this->config->conf['graph_height']+60;
			}
  			$el=imagefontheight($font_size);
  			$em=imagefontwidth($font_size);
  			// Create the image pallette
  			$img = imagecreatetruecolor($width,$height);
  			// Dark red background
  			$bg = imagecolorallocate($img, 0xAA, 0x00, 0x00);
  			imagefilledrectangle($img, 0, 0,$width ,$height , $bg);
  			// White font color
  			$color = imagecolorallocate($img, 255, 255, 255);

  			foreach ($ts as $k=>$string) {
    			// Length of the string
    			$len = strlen($string);
    			// Y-coordinate of character, X changes, Y is static
    			$ypos = 0;
    			// Loop through the string
    			for($i=0;$i<$len;$i++){
      				// Position of the character horizontally
      				$xpos = $i * $em;
      				$ypos = $k * $el;
      				// Draw character
     		 		imagechar($img, $font_size, $xpos, $ypos, $string, $color);
      				// Remove character from string
      				$string = substr($string, 1);     
    			}
			}
	    	header("Content-type: image/png");	   
            imagepng($img);
            imagedestroy($img);
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
        $data = preg_replace('/(HRULE|VDEF|DEF|CDEF|GPRINT|LINE|AREA|COMMENT)/',"\n\${1}", $data);
        return $data;
    }   
}
