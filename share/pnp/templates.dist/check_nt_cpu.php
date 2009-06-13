<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_nt -v CPULOAD
# $Id: check_nt_cpu.php 631 2009-05-01 12:20:53Z Le_Loup $
#
#
$opt[1] = "--vertical-label \"$UNIT[1]\" -u $MAX[1] -l $MIN[1]  --title \"CPU Load for $hostname / $servicedesc\" ";
#
#
#
$def[1] =  "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
#$def[1] .= "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
#$def[1] .= "DEF:var3=$rrdfile:$DS[3]:AVERAGE " ;
if ($WARN[1] != "") {  
	$def[1] .= "HRULE:$WARN[1]#FFFF00 ";
}
if ($CRIT[1] != "") {  
	$def[1] .= "HRULE:$CRIT[1]#FF0000 ";
}
#$def[1] .= "AREA:var3#FF0000:\"Load 15\" " ;
#$def[1] .= "GPRINT:var3:LAST:\"%6.2lf last\" " ;
#$def[1] .= "GPRINT:var3:AVERAGE:\"%6.2lf avg\" " ;
#$def[1] .= "GPRINT:var3:MAX:\"%6.2lf max\\n\" " ;
#$def[1] .= "AREA:var2#EA8F00:\"Load 5 \" " ;
#$def[1] .= "GPRINT:var2:LAST:\"%6.2lf last\" " ;
#$def[1] .= "GPRINT:var2:AVERAGE:\"%6.2lf avg\" " ;
#$def[1] .= "GPRINT:var2:MAX:\"%6.2lf max\\n\" " ;
$def[1] .= "AREA:var1#EACC00:\"$NAME[1] \" " ;
$def[1] .= "LINE1:var1#000000 " ;
$def[1] .= "GPRINT:var1:LAST:\"%6.2lf$UNIT[1] last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.2lf$UNIT[1] avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%6.2lf$UNIT[1] max\\n\" ";

?>
