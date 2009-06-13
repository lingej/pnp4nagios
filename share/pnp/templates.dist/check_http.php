<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_http
# $Id: check_http.php 367 2008-01-23 18:10:31Z pitchfork $
#
# Response Time
#
$opt[1] = "--vertical-label \"$UNIT[1]\" --title \"Response Times $hostname / $servicedesc\" ";
#
#
#
$def[1] =  "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
if ($WARN[1] != "") {
	$def[1] .= "HRULE:$WARN[1]#FFFF00 ";
}
if ($CRIT[1] != "") {
	$def[1] .= "HRULE:$CRIT[1]#FF0000 ";
}
$def[1] .= "AREA:var1#EACC00:\"$NAME[1] \" " ;
$def[1] .= "LINE1:var1#000000 " ;
$def[1] .= "GPRINT:var1:LAST:\"%6.2lf$UNIT[1] last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.2lf$UNIT[1] avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%6.2lf$UNIT[1] max\\n\" ";


#
# Filesize
#
$opt[2] = "--vertical-label \"$UNIT[2]\" --title \"Size $hostname / $servicedesc\" ";
#
#
#
$def[2] =  "DEF:var1=$rrdfile:$DS[2]:AVERAGE " ;
if ($WARN[2] != "") {
	$def[2] .= "HRULE:$WARN[2]#FFFF00 ";
}
if ($CRIT[2] != "") {
	$def[2] .= "HRULE:$CRIT[2]#FF0000 ";
}
$def[2] .= "AREA:var1#EACC00:\"$NAME[2] \" " ;
$def[2] .= "LINE1:var1#000000 " ;
$def[2] .= "GPRINT:var1:LAST:\"%6.2lf$UNIT[2] last\" " ;
$def[2] .= "GPRINT:var1:AVERAGE:\"%6.2lf$UNIT[2] avg\" " ;
$def[2] .= "GPRINT:var1:MAX:\"%6.2lf$UNIT[2] max\\n\" ";


?>
