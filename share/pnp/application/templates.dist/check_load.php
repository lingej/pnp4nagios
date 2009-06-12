<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_load
# $Id: check_load.php 627 2009-04-23 11:14:06Z pitchfork $
#
#
$opt[0] = "--vertical-label Load -l0  --title \"CPU Load for $hostname / $servicedesc\" ";
#
#
#
$def[0] =  "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[0] .= "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
$def[0] .= "DEF:var3=$rrdfile:$DS[3]:AVERAGE " ;
if ($WARN[0] != "") {
    $def[0] .= "HRULE:$WARN[0]#FFFF00 ";
}
if ($CRIT[0] != "") {
    $def[0] .= "HRULE:$CRIT[0]#FF0000 ";       
}
$def[0] .= "AREA:var3#FF0000:\"Load 15\" " ;
$def[0] .= "GPRINT:var3:LAST:\"%6.2lf last\" " ;
$def[0] .= "GPRINT:var3:AVERAGE:\"%6.2lf avg\" " ;
$def[0] .= "GPRINT:var3:MAX:\"%6.2lf max\\n\" " ;
$def[0] .= "AREA:var2#EA8F00:\"Load 5 \" " ;
$def[0] .= "GPRINT:var2:LAST:\"%6.2lf last\" " ;
$def[0] .= "GPRINT:var2:AVERAGE:\"%6.2lf avg\" " ;
$def[0] .= "GPRINT:var2:MAX:\"%6.2lf max\\n\" " ;
$def[0] .= "AREA:var1#EACC00:\"load 1 \" " ;
$def[0] .= "GPRINT:var1:LAST:\"%6.2lf last\" " ;
$def[0] .= "GPRINT:var1:AVERAGE:\"%6.2lf avg\" " ;
$def[0] .= "GPRINT:var1:MAX:\"%6.2lf max\\n\" ";
?>
