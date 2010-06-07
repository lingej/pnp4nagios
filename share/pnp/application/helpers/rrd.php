<?php defined('SYSPATH') OR die('No direct access allowed.');
/*
*
* RRDTool Helper Class 
*/
class rrd_Core {
    /*
    * 
    */
    public static function color($num=0 , $alpha='FF'){
        $r = array('99','66','ff','CC','00','33');
        $colors = array();
        $num   = intval($num);
        foreach($r as $ri){
            foreach($r as $gi){
                foreach($r as $bi){
                    $colors[] = sprintf("#%s%s%s%s",$ri,$gi,$bi,$alpha);
                }
            }
        }

        if(array_key_exists($num, $colors)){
            return $colors[$num];
        }else{
            return $colors[0];
        }
    }

    /*
     * Gradient Function
     * Concept by Stefan Triep
     */
    public static function gradient($vname=FALSE, $start_color='#0000a0', $end_color='#f0f0f0', $label=FALSE, $steps=10){
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'vname' is missing");   
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
        
        for ($i=$steps; $i>0; $i--){
            $spline .=  sprintf("CDEF:%s%d=%s,100,/,%d,* ",$spline_vname,$i,$vname,round((100 / $steps) * $i) );
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
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'vname' is missing");   
        }else{
            $line .= "AREA:".$vname;
        }
        if($color === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Paramter 'color' is missing");   
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
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'vname' is missing");   
        }else{
            $line .= "LINE".$type.":".$vname;
        }
        if($color === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Paramter 'color' is missing");   
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

    public static function gprint($vname=FALSE, $cf="AVERAGE", $text=FALSE){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'vname' is missing");   
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

    public static function def($vname=FALSE, $rrdfile=FALSE, $ds=FALSE, $cf="AVERAGE"){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'vname' is missing");   
        }
        if($rrdfile === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Paramter 'rrdfile' is missing");   
        }
        if($rrdfile === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Third Paramter 'ds' is missing");   
        }
        $line = sprintf("DEF:%s=%s:%s:%s ",$vname,$rrdfile,$ds,$cf);
        return $line;
    }

    public static function cdef($vname=FALSE, $rpn=FALSE){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'vname' is missing");   
        }
        if($rpn === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Paramter 'rpn' is missing");   
        }
        $line = sprintf("CDEF:%s=%s ",$vname,$rpn);
        return $line;
    }

    public static function vdef($vname=FALSE, $rpn=FALSE){
        $line = "";
        if($vname === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'vname' is missing");   
        }
        if($rrdfile === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Paramter 'rpn' is missing");   
        }
        $line = sprintf("VDEF:%s=%s ",$vname,$rpn);
        return $line;
    }

    public static function hrule($value=FALSE, $color=FALSE, $text=FALSE){
        $line = "";
        if($value === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ ."() First Paramter 'value' is missing");   
        }
        if($color === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Paramter 'color' is missing");   
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
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'value' is missing");   
        }
        if($color === FALSE){
            throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Paramter 'color' is missing");   
        }
        $line = sprintf("TICK:%s%s",$vname,$color);
        if($fraction != FALSE)
            $line .= ":$fraction";

        if($label != FALSE)
            $line .= ":$label";
        
        $line .= " ";
        return $line;
    }


	public static function alerter($vname=FALSE, $label=FALSE, $warning=FALSE, $critical=FALSE, $opacity = 'ff', $unit, $color_green = '#00ff00', $color_btw   = '#ffff00', $color_red   = '#ff0000', $line_col = '#ffffff') {
	
		if($vname === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'vname' is missing");
		}
		if($label === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Paramter 'label' is missing");
		}
		if($warning === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Third Paramter 'warning' is missing");
		}
		if($critical === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Fourth Paramter 'critical' is missing");
		}
		$line = "";
		$line .= "CDEF:green=".$vname.",".$warning.",LT,".$vname.",UNKN,IF ";
		$line .= "CDEF:btw=".$vname.",".$critical.",LT,".$vname.",UNKN,IF ";
		$line .= "CDEF:blue=btw,".$warning.",GT,btw,UNKN,IF ";
		$line .= "CDEF:red=".$vname.",".$critical.",GT,".$vname.",UNKN,IF ";
		$line .= rrd::area("green", $color_green.$opacity, $label);
		$line .= rrd::area("blue", $color_btw.$opacity);
		$line .= rrd::area("red", $color_red.$opacity);
		$line .= rrd::line1($vname,$line_col,$label);
	
	    return $line;
    }

	public static function ticker($vname=FALSE, $warning=FALSE, $critical=FALSE, $fraction = -0.05, $opacity = 'ff', $color_green = '#00ff00', $color_btw = '#ffff00', $color_red = '#ff0000') {

		if($vname === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() First Paramter 'vname' is missing");
		}
		if($warning === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Second Paramter 'warning' is missing");
		}
		if($critical === FALSE){
			throw new Kohana_exception("rrd::". __FUNCTION__ . "() Third Paramter 'critical' is missing");
		}
		$line = "";
		$line .= "CDEF:green=".$vname.",".$warning.",LT,".$vname.",UNKN,IF ";
		$line .= "CDEF:btw=".$vname.",".$critical.",LT,".$vname.",UNKN,IF ";
		$line .= "CDEF:blue=btw,".$warning.",GT,btw,UNKN,IF ";
		$line .= "CDEF:red=".$vname.",".$critical.",GT,".$vname.",UNKN,IF ";
		$line .= "CDEF:green2=green,0,EQ,0.000001,green,IF ";
		$line .= rrd::tick("green2", $color_green.$opacity, $fraction);
		$line .= rrd::tick("blue", $color_btw.$opacity, $fraction);
		$line .= rrd::tick("red", $color_red.$opacity, $fraction);
	
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
} 
