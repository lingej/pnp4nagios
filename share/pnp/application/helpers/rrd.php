<?php defined('SYSPATH') OR die('No direct access allowed.');
/*
*
* RRDTool Helper Class 
*/
class rrd_Core {
    /*
    * 
    */
    public static function color($num=0, $row='normal'){
        $colors['normal'] = array('#003399','#00ccff','#00ff99','#009900','#ffff66','#ff6600','#cc3399','#6600cc','#660066');
        if($colors[$row][$num] != ""){
            return $colors[$row][$num];
        }else{
            return $colors['normal'][0];
        }
    }
    public static function cut($string, $length=10, $align='left'){
        if($align == 'left'){
            $format = "%-".$length."s";
        }else{
            $format = "%".$length."s";
        }
        $s = sprintf($format,$string);
        return $s;
    }
} 
