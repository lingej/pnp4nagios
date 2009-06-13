<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_nwstat
# $Id: check_nwstat_CONNS.php 367 2008-01-23 18:10:31Z pitchfork $
#
# User Connections
#
$opt[1] = "--vertical-label \"$UNIT[1]\" --title \"User Connections $hostname / $servicedesc\" ";
#
#
#
$def[1] =  "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
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
$def[1] .= "GPRINT:var1:LAST:\"%6.0lf Conns last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.0lf Conns avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%6.0lf Conns max\\n\" ";
$def[1] .= 'COMMENT:"check_nwstat\r" ';
?>
