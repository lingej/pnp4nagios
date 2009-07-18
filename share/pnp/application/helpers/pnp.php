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

	    if (1 == get_magic_quotes_gpc()){
	       $string = stripslashes($string);
	    }
	    $string = preg_replace('/[ :\/\\\\]/', "_", $string);
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
	        return;
	    }
		if( $string == XML_STRUCTURE_VERSION ){
			$string = "valid";
		}else{
			$string = Kohana::lang('common.xml-structure-mismatch', $string, XML_STRUCTURE_VERSION);
		}
		return $string;
	}
	/*
	*
	*/
	public static function zoom_icon($host,$service,$start,$end,$source,$view){
		print "<a href=\"javascript:Gzoom('zoom?host=$host&srv=$service&view=$view&source=$source&end=$end&start=$start');\"><img src=\"media/images/zoom.png\" title=\"Zoom into the Graph\" ></a>\n";
	}


} 
