<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Template für check_nt -v USEDDISKSPACE -l 
#
# RRDtool Options
$opt[1] = "--vertical-label GB -u $MAX[1] -l 0 --title \"Used Diskspace for $hostname / $servicedesc\" ";
#
#
# Graph Definitions
$def[1]  =  "DEF:var1=$RRDFILE[1]:1:AVERAGE "; 
$def[1] .= "AREA:var1#c6c6c6: ";
$def[1] .= "LINE1:var1#003300: ";
$def[1] .= "HRULE:$MAX[1]#003300:\"Capacity $MAX[1]GB \" ";
if ($WARN[1] != "") {  
	$def[1] .= "HRULE:$WARN[1]#ffff00:\"Warning on $WARN[1]GB  \" ";
}
if ($CRIT[1] != "") {  
	$def[1] .= "HRULE:$CRIT[1]#ff0000:\"Critical on $CRIT[1]GB \\n\" ";       
}
$def[1] .= "GPRINT:var1:LAST:\"%6.2lf GB Last \\n\" ";
$def[1] .= "GPRINT:var1:MAX:\"%6.2lf GB Max \\n\" ";
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.2lf GB Average \" ";
?>
