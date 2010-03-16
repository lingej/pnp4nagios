<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
#

$opt[1] = "--vertical-label \"Interval\" -X0 -l0  --title \"Redolog Switch Interval $hostname / $servicedesc\" ";


$def[1] =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .= "AREA:var1#F2F2F2:\"\" " ;
$def[1] .= "LINE1:var1#F30000:\"Redo Log Switch Interval\" " ;
$def[1] .= "GPRINT:var1:LAST:\"%3.1lf s LAST \" "; 
$def[1] .= "GPRINT:var1:MAX:\"%3.1lf s MAX \" "; 
$def[1] .= "GPRINT:var1:AVERAGE:\"%3.1lf s AVERAGE \" "; 
?>
