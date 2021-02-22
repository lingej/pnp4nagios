<?php defined('SYSPATH') OR die('No direct access allowed.');
/*
*
* RRDTool Helper Class 
*/
class rrd_Core {

	public static function color_inverse($color){
		$color = str_replace('#', '', $color);
		if (strlen($color) != 6){ return '000000'; }
			$rgb = '';
			for ($x=0;$x<3;$x++){
				$c = 255 - hexdec(substr($color,(2*$x),2));
				$c = ($c < 0) ? 0 : dechex($c);
				$rgb .= (strlen($c) < 2) ? '0'.$c : $c;
			}
			return '#'.$rgb;
	}

    /*
     * Color Function
     * Concept by Stefan Triep
     */
	public static function color($num=0 , $alpha='FF', $scheme=''){
		$colors = array();
		$value = array('cc','ff','99','66');
		$num   = intval($num);

		# check if colour scheme entry exists
		# fall back to old method if not found
		if ( isset( $scheme["$num"] ) ){
			$color = $scheme["$num"] . $alpha;
			return $color;
		}

		foreach($value as $ri){
			for ($z=1;$z<8;$z++) {
				$color = "#";
				if ( ($z & 4) >= 1 ){
					$color .= "$ri";
				} else {
					$color .= "00";
				}
				if ( ($z & 2) >= 1 ){
					$color .= "$ri";
				} else {
					$color .= "00";
				}
				if ( ($z & 1) >= 1 ){
					$color .= "$ri";
				} else {
					$color .= "00";
				}
				$icolor = rrd::color_inverse($color);
				$pos = array_search($color,$colors);
				$ipos = array_search($icolor,$colors);
				if ( $pos == false ) {
					$colors[] = $color . $alpha;
				}
				if ( $ipos == false ) {
					$colors[] = $icolor . $alpha;
				}
			}
		}
		if (array_key_exists($num, $colors)) {
			return $colors[$num];
		} else {
			return $colors[0];
		}
	}

    /*
     * Gradient Function
     * Concept by Stefan Triep
     */
    public static function gradient($vname=FALSE, $start_color='#0000a0', $end_color='#f0f0f0', $label=FALSE, $steps=20, $lower=FALSE){
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");   
        }
        if(preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i',$start_color,$matches)){
            $r1=hexdec($matches[1]);
            $g1=hexdec($matches[2]);
            $b1=hexdec($matches[3]);
        }else{
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Wrong Color Format: '".$start_color."'");   
        }            

        if(preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i',$end_color,$matches)){
            $r2=hexdec($matches[1]);
            $g2=hexdec($matches[2]);
            $b2=hexdec($matches[3]);
        }else{
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Wrong Color Format: '".$end_color."'");   
        }            

        $diff_r=$r2-$r1;
        $diff_g=$g2-$g1;
        $diff_b=$b2-$b1;
        $spline =  "";
        $spline_vname = "var".substr(sha1(rand()),1,4);
        if(preg_match('/^([0-9]{1,3})%$/', $lower, $matches)){
            if($matches[1] > 100)
                throw new Kohana_exception("rrd::". __FUNCTION__ . "() Lower gradient start > 100% is not allowed: '".$lower."'");
            
            $lower   = $matches[1];
            $spline .= sprintf("CDEF:%sminimum=%s,100,/,%d,* ", $vname, $vname, $lower);
        }elseif(preg_match('/^([0-9]+)$/', $lower, $matches)){
            $lower   = $matches[1];
            $spline .= sprintf("CDEF:%sminimum=%s,%d,- ", $vname, $vname, $lower);
        }else{
            $lower   = 0;
            $spline .= sprintf("CDEF:%sminimum=%s,%s,- ", $vname, $vname, $vname);
        }
        # debug
	# $spline .= sprintf("GPRINT:%sminimum:MAX:\"minumum %%lf\\n\" ",$vname);
        for ($i=$steps; $i>0; $i--){
            $spline .=  sprintf("CDEF:%s%d=%s,%sminimum,-,%d,/,%d,*,%sminimum,+ ",$spline_vname,$i,$vname,$vname,$steps,$i,$vname );
            # debug
	    # $spline .= sprintf("GPRINT:%s%d:MAX:\"%22d %%lf\\n\" ",$spline_vname,$i,$i);
        }    
        for ($i=$steps; $i>0; $i--){
            $factor=$i / $steps;
            $r=round($r1 + $diff_r * $factor);
            $g=round($g1 + $diff_g * $factor);
            $b=round($b1 + $diff_b * $factor);
            if (($i==$steps) and ($label!=FALSE)){
                $spline .=  sprintf("AREA:%s%d#%02X%02X%02X:\"%s\" ", $spline_vname,$i,$r,$g,$b,$label);
            }else{
                $spline .=  sprintf("AREA:%s%d#%02X%02X%02X ", $spline_vname,$i,$r,$g,$b);
            }
        }
        return $spline;
    }


    public static function cut($string, $length=18, $align='left'){
        if(strlen($string) > $length){
            $string = substr($string,0,($length-3))."...";
        }
        if($align == 'left'){
            $format = "%-".$length."s";
        }else{
            $format = "%".$length."s";
        }
        $s = sprintf($format,$string);
        return $s;
    }
    
    public static function area($vname=FALSE, $color=FALSE, $text=FALSE, $stack=FALSE){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");   
        }else{
            $line .= "AREA:".$vname;
        }
        if($color === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'color' is missing");   
        }else{
            $line .= $color;
        }
        $line .= ":\"$text\"";
        if($stack != FALSE){
            $line .= ":STACK";
        }
        $line .= " ";
        return $line;
    }
    
    public static function line($type=1,$vname=FALSE, $color=FALSE, $text=FALSE, $stack=FALSE){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");   
        }else{
            $line .= "LINE".$type.":".$vname;
        }
        if($color === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'color' is missing");   
        }else{
            $line .= $color;
        }
        $line .= ":\"$text\"";
        if($stack != FALSE){
            $line .= ":STACK";
        }
        $line .= " ";
        return $line;
    }

    public static function line1($vname=FALSE, $color=FALSE, $text=FALSE, $stack=FALSE){
        return rrd::line(1,$vname, $color,$text, $stack);
    }

    public static function line2($vname=FALSE, $color=FALSE, $text=FALSE, $stack=FALSE){
        return rrd::line(2,$vname, $color,$text, $stack);
    }

    public static function line3($vname=FALSE, $color=FALSE, $text=FALSE, $stack=FALSE){
        return rrd::line(3,$vname, $color,$text, $stack);
    }

    public static function gprint($vname=FALSE, $cf="AVERAGE", $text="%6.2lf %s"){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");   
        }
        
        if(is_array($cf)){
            foreach($cf as $key => $val){
                $line .= sprintf("GPRINT:%s:%s:",$vname,$val);
                if($key == sizeof($cf)-1){
                    $line .= '"'.$text.' '.ucfirst(strtolower($val)).'\\l" ';
                }else{
                    $line .= '"'.$text.' '.ucfirst(strtolower($val)).'" ';
                }
            }
        }else{
            $line .= sprintf("GPRINT:%s:%s:",$vname,$cf);
            $line .= '"'.$text.'" ';
        }
        return $line; 
    }

	/*
	* Function to modify alignment of gprint
	*/
	public static function gprinta($vname=FALSE, $cf="AVERAGE", $text="%6.2lf %s", $align=""){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");
        }
        if($align != ""){
            $align = '\\' . $align;
        }
        if(is_array($cf)){
            foreach($cf as $key => $val){
                $line .= sprintf("GPRINT:%s:%s:",$vname,$val);
                if(($key == sizeof($cf)-1)and($align != "")){
                    $line .= '"'.$text.' '.ucfirst(strtolower($val)).$align.'" ';
                }else{
                    $line .= '"'.$text.' '.ucfirst(strtolower($val)).'" ';
                }
            }
        }else{
            $line .= sprintf("GPRINT:%s:%s:",$vname,$cf);
            $line .= '"'.$text.'" ';
        }
        return $line;
    }

    public static function def($vname=FALSE, $rrdfile=FALSE, $ds=FALSE, $cf="AVERAGE"){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");   
        }
        if($rrdfile === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'rrdfile' is missing");   
        }
        if($rrdfile === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Third Parameter 'ds' is missing");   
        }
        $line = sprintf("DEF:%s=%s:%s:%s ",$vname,$rrdfile,$ds,$cf);
        return $line;
    }

    public static function cdef($vname=FALSE, $rpn=FALSE){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");   
        }
        if($rpn === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'rpn' is missing");   
        }
        $line = sprintf("CDEF:%s=%s ",$vname,$rpn);
        return $line;
    }

    public static function vdef($vname=FALSE, $rpn=FALSE){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");   
        }
        if($rpn === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'rpn' is missing");   
        }
        $line = sprintf("VDEF:%s=%s ",$vname,$rpn);
        return $line;
    }

    public static function hrule($value=FALSE, $color=FALSE, $text=FALSE){
        $line = "";
        if($value === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ ."() First Parameter 'value' is missing");   
        }
        if($color === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'color' is missing");   
        }
	if($value == "~" ) {
	    return "";
	}
        $line = sprintf("HRULE:%s%s:\"%s\" ",$value,$color,$text);
        return $line;
    }
    
    public static function comment($text=FALSE){
        $line = sprintf("COMMENT:\"%s\" ", $text);
        return $line;
    }

    public static function tick($vname=FALSE, $color=FALSE, $fraction=FALSE, $label=FALSE){
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'value' is missing");   
        }
        if($color === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'color' is missing");   
        }
        $line = sprintf("TICK:%s%s",$vname,$color);
        if($fraction != FALSE)
            $line .= ":$fraction";

        if($label != FALSE)
            $line .= ":$label";
        
        $line .= " ";
        return $line;
    }


	public static function alerter($vname=FALSE, $label=FALSE, $warning=FALSE, $critical=FALSE, $opacity = 'ff', $unit, $color_green = '#00ff00', $color_btw   = '#ffff00', $color_red   = '#ff0000', $line_col = '#0000ff') {
	
		if($vname === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");
		}
		if($label === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'label' is missing");
		}
		if($warning === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Third Parameter 'warning' is missing");
		}
		if($critical === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Fourth Parameter 'critical' is missing");
		}
		$line = "";
		$green_vname = "var".substr(sha1(rand()),1,4);
        $btw_vname = "var".substr(sha1(rand()),1,4);
        $blue_vname = "var".substr(sha1(rand()),1,4);
        $red_vname = "var".substr(sha1(rand()),1,4);
        if($warning < $critical){
            $line .= "CDEF:".$green_vname."=".$vname.",".$warning.",LT,".$vname.",UNKN,IF ";
            $line .= "CDEF:".$btw_vname."=".$vname.",".$critical.",LT,".$vname.",UNKN,IF ";
            $line .= "CDEF:".$blue_vname."=".$btw_vname.",".$warning.",GE,".$btw_vname.",UNKN,IF ";
            $line .= "CDEF:".$red_vname."=".$vname.",".$critical.",GE,".$vname.",UNKN,IF ";
        } else {
            $line .= "CDEF:".$green_vname."=".$vname.",".$warning.",GT,".$vname.",UNKN,IF ";
            $line .= "CDEF:".$btw_vname."=".$vname.",".$critical.",GE,".$vname.",UNKN,IF ";
            $line .= "CDEF:".$blue_vname."=".$btw_vname.",".$warning.",LE,".$btw_vname.",UNKN,IF ";
            $line .= "CDEF:".$red_vname."=".$vname.",".$critical.",LT,".$vname.",UNKN,IF ";
        }
		$line .= rrd::area($green_vname, $color_green.$opacity);
		$line .= rrd::area($blue_vname, $color_btw.$opacity);
		$line .= rrd::area($red_vname, $color_red.$opacity);
		$line .= rrd::line1($vname,$line_col,$label);
	
	    return $line;
    }

    public static function alerter_gr($vname=FALSE,$label=FALSE,$warning=FALSE,$critical=FALSE,$opacity='ff',$unit,$color_green='#00ff00',$color_btw='#ffff00',$color_red='#ff0000',$line_col='#0000ff',$start_color="#ffffff") {

        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");
        }
        if($label === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'label' is missing");
        }
        if($warning === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Third Parameter 'warning' is missing");
        }
        if($critical === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Fourth Parameter 'critical' is missing");
        }
        $line = "";
        $green_vname = "var".substr(sha1(rand()),1,4);
        $btw_vname = "var".substr(sha1(rand()),1,4);
        $blue_vname = "var".substr(sha1(rand()),1,4);
        $red_vname = "var".substr(sha1(rand()),1,4);
        $line = "";
        if($warning < $critical){
            $line .= "CDEF:".$green_vname."=".$vname.",".$warning.",LT,".$vname.",UNKN,IF ";
            $line .= "CDEF:".$btw_vname."=".$vname.",".$critical.",LT,".$vname.",UNKN,IF ";
            $line .= "CDEF:".$blue_vname."=".$btw_vname.",".$warning.",GE,".$btw_vname.",UNKN,IF ";
            $line .= "CDEF:".$red_vname."=".$vname.",".$critical.",GE,".$vname.",UNKN,IF ";
        } else {
            $line .= "CDEF:".$green_vname."=".$vname.",".$warning.",GT,".$vname.",UNKN,IF ";
            $line .= "CDEF:".$btw_vname."=".$vname.",".$critical.",GE,".$vname.",UNKN,IF ";
            $line .= "CDEF:".$blue_vname."=".$btw_vname.",".$warning.",LE,".$btw_vname.",UNKN,IF ";
            $line .= "CDEF:".$red_vname."=".$vname.",".$critical.",LT,".$vname.",UNKN,IF ";
        }
        $line .= rrd::gradient($green_vname, $start_color, $color_green.$opacity);
        $line .= rrd::gradient($blue_vname, $start_color, $color_btw.$opacity);
        $line .= rrd::gradient($red_vname, $start_color, $color_red.$opacity);
        $line .= rrd::line1($vname,$line_col,$label);
        return $line;
    }

	public static function ticker($vname=FALSE, $warning=FALSE, $critical=FALSE, $fraction = -0.05, $opacity = 'ff', $color_green = '#00ff00', $color_btw = '#ffff00', $color_red = '#ff0000') {

		if($vname === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'vname' is missing");
		}
		if($warning === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'warning' is missing");
		}
		if($critical === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Third Parameter 'critical' is missing");
		}
		$line = "";
		$green_vname = "var".substr(sha1(rand()),1,4);
        $btw_vname = "var".substr(sha1(rand()),1,4);
        $blue_vname = "var".substr(sha1(rand()),1,4);
        $red_vname = "var".substr(sha1(rand()),1,4);
		$green2_vname = "var".substr(sha1(rand()),1,4);
		$line .= "CDEF:".$green_vname."=".$vname.",".$warning.",LT,".$vname.",UNKN,IF ";
		$line .= "CDEF:".$btw_vname."=".$vname.",".$critical.",LT,".$vname.",UNKN,IF ";
		$line .= "CDEF:".$blue_vname."=".$btw_vname.",".$warning.",GE,".$btw_vname.",UNKN,IF ";
		$line .= "CDEF:".$red_vname."=".$vname.",".$critical.",GE,".$vname.",UNKN,IF ";
		$line .= "CDEF:".$green2_vname."=".$green_vname.",0,EQ,0.000001,".$green_vname.",IF ";
		$line .= rrd::tick($green2_vname, $color_green.$opacity, $fraction);
		$line .= rrd::tick($blue_vname, $color_btw.$opacity, $fraction);
		$line .= rrd::tick($red_vname, $color_red.$opacity, $fraction);
	
	    return $line;
    }

	public static function darkteint(){
		$line = '';
		$line .= '--color=BACK#000000 ';
		$line .= '--color=FONT#F7F7F7 ';
		$line .= '--color=SHADEA#ffffff ';
		$line .= '--color=SHADEB#ffffff ';
		$line .= '--color=CANVAS#000000 ';
		$line .= '--color=GRID#00991A ';
		$line .= '--color=MGRID#00991A ';
		$line .= '--color=ARROW#00FF00 ';

		return $line;
	}

    # http://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
    public static function colbright($hex, $steps) {
        if($hex === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Parameter 'hex' is missing");
        }
        if($steps === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Parameter 'steps' is missing");
        }
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        // Normalize into a six character long hex string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
        }

        // Split into three parts: R, G and B
        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0,min(255,$color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }
        return $return;
    }

    public static function debug($data=FALSE){
        if($data != FALSE){
	    ob_start();

	    var_dump($data);
            $var_dump = ob_get_contents();
            $var_dump = preg_replace('/(HRULE|VDEF|DEF|CDEF|GPRINT|LINE|AREA|COMMENT)/',"\n\${1}", $var_dump);
            ob_end_clean(); 
            throw new Kohana_exception("<pre>".$var_dump."</pre>");
	}
    }

} 
