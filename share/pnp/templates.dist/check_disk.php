<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Template for check_disk
# $Id: check_disk.php 631 2009-05-01 12:20:53Z Le_Loup $
#
#
# RRDtool Options
$opt[1] = "--vertical-label MB -l 0 -u $MAX[1] --title \"Filesystem $hostname / $servicedesc\" ";
#
#
# Graph Definitions
$def[1] = "DEF:var1=$rrdfile:$DS[1]:AVERAGE "; 
$def[1] .= "AREA:var1#c6c6c6:\"$servicedesc\\n\" "; 
$def[1] .= "LINE1:var1#003300: "; 
$def[1] .= "HRULE:$MAX[1]#003300:\"Size $MAX[1] MB \" ";
if ($WARN[1] != "") {  
	$def[1] .= "HRULE:$WARN[1]#ffff00:\"Warning on $WARN[1] MB \" ";
}
if ($CRIT[1] != "") {  
	$def[1] .= "HRULE:$CRIT[1]#ff0000:\"Critical on $CRIT[1] MB \\n\" ";       
}
$def[1] .= "GPRINT:var1:LAST:\"%6.2lf MB of $MAX[1] MB used \\n\" ";
$def[1] .= "GPRINT:var1:MAX:\"%6.2lf MB max used \\n\" ";
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.2lf MB avg used\" ";
?>
