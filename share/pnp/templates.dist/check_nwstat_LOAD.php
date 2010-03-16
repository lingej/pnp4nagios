<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_nwstat
#
# CPU Load
#
$opt[1] = " -u 100 -l 0 --vertical-label \"$UNIT[1]\" --title \"CPU Load $hostname / $servicedesc\" ";
#
#
#
$def[1]  =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .=  "CDEF:sp1=var1,100,/,12,* " ;
$def[1] .=  "CDEF:sp2=var1,100,/,30,* " ;
$def[1] .=  "CDEF:sp3=var1,100,/,50,* " ;
$def[1] .=  "CDEF:sp4=var1,100,/,70,* " ;

if ($WARN[1] != "") {
	$def[1] .= "HRULE:$WARN[1]#FFFF00 ";
}
if ($CRIT[1] != "") {
	$def[1] .= "HRULE:$CRIT[1]#FF0000 ";
}
$def[1] .= "AREA:var1#FF5C00:\"$NAME[1] \" " ;
$def[1] .= "AREA:sp4#FF7C00: " ;
$def[1] .= "AREA:sp3#FF9C00: " ;
$def[1] .= "AREA:sp2#FFBC00: " ;
$def[1] .= "AREA:sp1#FFDC00: " ;
$def[1] .= "LINE1:var1#000000 " ;
$def[1] .= "GPRINT:var1:LAST:\"%6.1lf last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.1lf avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%6.1lf max\\n\" ";
$def[1] .= 'COMMENT:"check_nwstat\r" ';
?>
