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
