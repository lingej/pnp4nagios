<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Template for check_dns
#

$opt[1] = "--lower-limit=$MIN[1] --vertical-label $UNIT[1]  --title \"DNS Response Time\" ";


$def[1] =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .= "AREA:var1#FF6600:\"DNS Response Time\" " ;
$def[1] .= "LINE1:var1#000000:\"\" " ;

if ($WARN[1] != "") {
        $def[1] .= "HRULE:$WARN[1]#FFFF00 ";
}
if ($CRIT[1] != "") {
        $def[1] .= "HRULE:$CRIT[1]#FF0000 ";
}

$def[1] .= "GPRINT:var1:LAST:\"%3.4lg %s$UNIT[1] LAST \" "; 
$def[1] .= "GPRINT:var1:MAX:\"%3.4lg %s$UNIT[1] MAX \" "; 
$def[1] .= "GPRINT:var1:AVERAGE:\"%3.4lg %s$UNIT[1] AVERAGE \" "; 
?>
