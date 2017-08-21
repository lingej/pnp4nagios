<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
*
* 
*/
class pnp_Core {

    public static function clean($string = FALSE){
        if($string === FALSE){
            return;
        }
        if($string == ""){
            return $string;
        }
        $string = preg_replace('/[ :\/\\\]/', "_", $string);
        $string = htmlspecialchars($string);
        return $string;
    
    }
    public static function shorten($string = FALSE, $length = 25){
        if($string === FALSE){
            return;
        }
        if(strlen($string) > $length){
            $string = substr($string, 0, $length) . "...";
        }
        return $string;
    }
    /*
    *
    */
    public static function xml_version_check($string = FALSE){
        if($string === FALSE){
            return FALSE;
        }
        if( $string == XML_STRUCTURE_VERSION ){
            $string = "valid";
        }else{
            $string = Kohana::lang('error.xml-structure-mismatch', $string, XML_STRUCTURE_VERSION);
        }
        return $string;
    }
    /*
    *
    */
    public static function zoom_icon($host,$service,$start,$end,$source,$view,$graph_width,$graph_height){
        $service = urlencode(urlencode($service));
        $host = urlencode(urlencode($host));
        print "<a href=\"javascript:Gzoom('".url::base(TRUE)."zoom?host=$host&srv=$service&view=$view&source=$source&end=$end&start=$start&graph_width=$graph_width&graph_height=$graph_height');\" title=\"Zoom into the Graph\"><img src=\"".url::base()."media/images/zoom.png\"></a>\n";
    }

    /*
    *
    */
    public static function zoom_icon_special($tpl,$start,$end,$source,$view,$graph_width,$graph_height){
        print "<a href=\"javascript:Gzoom('".url::base(TRUE)."zoom?tpl=$tpl&view=$view&source=$source&end=$end&start=$start&graph_width=$graph_width&graph_height=$graph_height');\" title=\"Zoom into the Graph\"><img src=\"".url::base()."media/images/zoom.png\"></a>\n";
    }

    /*
    *
    */
    public static function add_to_basket_icon($host,$service,$source=FALSE){
        if($source === FALSE){
       	    print "<span id=\"basket_action_add\"><a title=\"".Kohana::lang('common.basket-add-service')."\" id=\"".$host."::".$service."\"><img width=12px height=12px src=\"".url::base()."media/images/add.png\"></a></span>\n";
        }else{
       	    print "<span id=\"basket_action_add\"><a title=\"".Kohana::lang('common.basket-add-item')."\" id=\"".$host."::".$service."::".$source."\"><img width=16px height=16px src=\"".url::base()."media/images/add.png\"></a></span>\n";
        }
    }

    /*
    *
    */
    public static function multisite_link($base_url=FALSE,$site=FALSE,$host=FALSE,$service=FALSE){
        if($host && $service){
            $link = sprintf("'%s/view.py?view_name=service&site=%s&host=%s&service=%s'", $base_url,$site,urlencode($host),urlencode($service));
            return $link;
        }
        if($host){
            $link = sprintf("'%s/view.py?view_name=host&site=%s&host=%s'", $base_url,$site,urlencode($host));
            return $link;
        }
    }

    public static function addToUri($fields = array(),$base = True){
        if(!is_array($fields)){
            return false;
        }
        $get = $_GET;
        if($base === True){
            $uri  = url::base(TRUE);
            $uri .= Router::$current_uri;
        }else{
            $uri  = "";
        }
        $uri .= '?';
        foreach($fields as $key=>$value){
            $get[$key] = $value;
        }
        foreach($get as $key=>$value){
	    if($value === ''){
		continue;
	    }
	    $uri .= $key."=".urlencode($value)."&";
        }
        return rtrim($uri,"&");
    }

    /* "normalize" and adjust value / unit (similar to format string %s in RRDtool)
    *  Parameters in:
    *     value    := number, maybe suffixed by unit string
    *                 examples: 1234, 1.234, 1234M, 1234Kb
    *     base     := base of value (1000, e.g. traffic or 1024, e.g. disk size)
    *     format   := format string
    *  Parameters out:
    *     val_unit := formatted value (including unit)
    *     val_fmt  := formatted value (without leading blanks and unit)
    *     unit     := adjusted unit
    *     divisor  := number used to "normalize" value
    */
    public static function adjust_unit($value,$base=1000,$format='%.3lf'){
        preg_match('/^(-?[0-9\.,]+)\s*(\S?)(\S?)/',$value,$matches);

        $mag = 0;
        $value = $matches[1];
        while ($value >= $base){
            $value /= $base;
            $mag++;
        }
        $pos = 0;
        if ($matches[2] == "%") {
            $unit = '%';
        } else {
            if ($matches[2] == "") {
                $matches[2] = " ";
            }
            if (($matches[2] == "B") or ($matches[2] == "b")) {
                $matches[3] = $matches[2];
                $matches[2] = " ";
            }
            $pos = strpos(' KMGTP',strtoupper($matches[2]));
            $unit = substr(' KMGTP',$mag+$pos,1).$matches[3];
        }
        $val_unit = sprintf ("$format %s", $value, $unit);
        $val_fmt = sprintf ($format, $value);
        $val_fmt = str_replace(' ','',$val_fmt);
        return array ($val_unit,$val_fmt,$unit,pow($base,$mag));
    }

    public static function print_version(){
	return PNP_NAME . "-" . PNP_VERSION . " [ " .  PNP_REL_DATE  . " ]";
    }

}
