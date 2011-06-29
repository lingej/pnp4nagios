<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_http
#
# Response Time
#
$opt[1] = "--vertical-label \"$UNIT[1]\" --title \"Response Times $hostname / $servicedesc\" ";
#
#
#
$def[1] =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
if ($WARN[1] != "") {
	$def[1] .= "HRULE:$WARN[1]#FFFF00 ";
}
if ($CRIT[1] != "") {
	$def[1] .= "HRULE:$CRIT[1]#FF0000 ";
}
#$def[1] .= "AREA:var1#EACC00:\"$NAME[1] \" " ;
$def[1] .= rrd::gradient("var1", "66CCFF", "0000ff", "$NAME[1]"); 
$def[1] .= "LINE1:var1#666666 " ;
$def[1] .= rrd::gprint("var1", array("LAST","MAX","AVERAGE"), "%6.2lf $UNIT[1]");

#
# Filesize
#
$opt[2] = "--vertical-label \"$UNIT[2]\" --title \"Size $hostname / $servicedesc\" ";
#
#
#
$def[2] =  "DEF:var1=$RRDFILE[2]:$DS[2]:AVERAGE " ;
if ($WARN[2] != "") {
	$def[2] .= "HRULE:$WARN[2]#FFFF00 ";
}
if ($CRIT[2] != "") {
	$def[2] .= "HRULE:$CRIT[2]#FF0000 ";
}
$def[2] .= rrd::gradient("var1", "66CCFF", "0000ff", "$NAME[2]"); 
$def[2] .= "LINE1:var1#333333 " ;
$def[2] .= rrd::gprint("var1", array("LAST","MAX","AVERAGE"), "%6.2lf %s$UNIT[2]");

?>
