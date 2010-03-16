<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_nt -v MEMUSE
#
$opt[1] = "-X0 --vertical-label \"Megabyte\" -u $MAX[1] -l 0  --title \"Windows Memory\" ";

$def[1] =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE "; 
$def[1] .= "AREA:var1#00FF00:\"Memory usage\" " ; 
$def[1] .= "LINE1:var1#00000c " ; 
if ($WARN[1] != "") {  
	$def[1] .= "HRULE:$WARN[1]#FFFF00:\"Warning ($WARN[1]MB)\" " ; 
}
if ($CRIT[1] != "") {  
	$def[1] .= "HRULE:$CRIT[1]#FF0000:\"Critical ($CRIT[1]MB)\\n\" " ; 
}
$def[1] .= "GPRINT:var1:LAST:\"%6.2lf %sMB Last \" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.2lf %sMB Average \" " ;
$def[1] .= "GPRINT:var1:MAX:\"%6.2lf %sMB Max \\n\" ";
?>
