<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Template for integer values 
#
# 09/04/2015 - TruePath Technologies Inc. (DRM): Added getRandomColor to fix color array size limit
#
$def[1] = "";
$opt[1] = "";

foreach ( $DS as $KEY => $VAL ){
	$opt[1] .= "--alt-y-grid -l 0 --vertical-label \"$LABEL[$KEY]\"  --title \"$LABEL[$KEY]\" ";
	$def[1] .= "DEF:var_float$KEY=$RRDFILE[$KEY]:$DS[$KEY]:MAX " ;
	$def[1] .= "CDEF:var$KEY=var_float$KEY,FLOOR " ;
	$def[1] .= "LINE1:var$KEY" . getRandomColor() . ":\"$LABEL[$KEY]\" " ;

	if ($WARN[$KEY] != "") {
	    $def[1] .= "HRULE:$WARN[$KEY]#FFFF00 ";
	}
	if ($CRIT[$KEY] != "") {
	    $def[1] .= "HRULE:$CRIT[$KEY]#FF0000 ";
	}

	$def[1] .= "GPRINT:var$KEY:LAST:\"%.0lf $UNIT[$KEY] LAST \" "; 
	$def[1] .= "GPRINT:var$KEY:MAX:\"%.0lf $UNIT[$KEY] MAX \" "; 
	$def[1] .= "GPRINT:var$KEY:AVERAGE:\"%.0lf $UNIT[$KEY] AVERAGE \\n\" "; 
}

# thanks to: http://stackoverflow.com/questions/10708965/generate-random-colors
function getRandomColor() {
    $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
    return $color;
}

?>
