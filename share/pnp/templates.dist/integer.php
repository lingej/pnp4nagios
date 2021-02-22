<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Template for integer values 
#
$def[1] = "";
$opt[1] = "";
$colors = array('#000000', '#0f0', '#ff0', '#f00', '#f0f', '#00f', '#0ff');
foreach ( $DS as $KEY => $VAL ){
	$opt[1] .= "--alt-y-grid -l 0 --vertical-label \"$LABEL[$KEY]\"  --title \"$LABEL[$KEY]\" ";
	$def[1] .= "DEF:var_float$KEY=$RRDFILE[$KEY]:$DS[$KEY]:MAX " ;
	$def[1] .= "CDEF:var$KEY=var_float$KEY,FLOOR " ;
	$def[1] .= "LINE1:var$KEY$colors[$KEY]:\"$LABEL[$KEY]\" " ;

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
?>
