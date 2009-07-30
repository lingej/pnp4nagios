<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Template for check_disk
# $Id: check_disk.php 631 2009-05-01 12:20:53Z Le_Loup $
#
#
# RRDtool Options

foreach ($DS as $i) {

	$opt[1] = "--vertical-label MB -l 0 -u $MAX[$i] --title \"Filesystem $hostname / $servicedesc\" ";
	#
	#
	# Graph Definitions
	$def[$i]  = "DEF:var1=$RRDFILE[$i]:$DS[$i]:AVERAGE "; 
	$def[$i] .= "AREA:var1#c6c6c6:\"$servicedesc\\n\" "; 
	$def[$i] .= "LINE1:var1#003300: "; 
	$def[$i] .= "HRULE:$MAX[$i]#003300:\"Size $MAX[$i] MB \" ";
	if ($WARN[$i] != "") {  
		$def[$i] .= "HRULE:$WARN[$i]#ffff00:\"Warning on $WARN[$i] MB \" ";
	}
	if ($CRIT[$i] != "") {  
		$def[$i] .= "HRULE:$CRIT[$i]#ff0000:\"Critical on $CRIT[$i] MB \\n\" ";       
	}
	$def[$i] .= "GPRINT:var1:LAST:\"%6.2lf MB of $MAX[$i] MB used \\n\" ";
	$def[$i] .= "GPRINT:var1:MAX:\"%6.2lf MB max used \\n\" ";
	$def[$i] .= "GPRINT:var1:AVERAGE:\"%6.2lf MB avg used\" ";
}
?>
