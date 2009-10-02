<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Template for check_disk
# $Id: check_disk.php 631 2009-05-01 12:20:53Z Le_Loup $
#
#
# RRDtool Options

foreach ($DS as $key=>$val) {
	$ds_name[$key] = str_replace("_","/",$NAME[$key]);
	$opt[$key] = "--vertical-label MB -l 0 -u $MAX[$key] --title \"Filesystem $ds_name[$key]\" ";
	#
	#
	# Graph Definitions
	$def[$key]  = "DEF:var1=$RRDFILE[$key]:$DS[$key]:AVERAGE "; 
	$def[$key] .= "AREA:var1#c6c6c6:\"$servicedesc\\n\" "; 
	$def[$key] .= "LINE1:var1#003300: "; 
	$def[$key] .= "HRULE:$MAX[$key]#003300:\"Size $MAX[$key] MB \" ";
	if ($WARN[$key] != "") {  
		$def[$key] .= "HRULE:$WARN[$key]#ffff00:\"Warning on $WARN[$key] MB \" ";
	}
	if ($CRIT[$key] != "") {  
		$def[$key] .= "HRULE:$CRIT[$key]#ff0000:\"Critical on $CRIT[$key] MB \\n\" ";       
	}
	$def[$key] .= "GPRINT:var1:LAST:\"%6.2lf MB of $MAX[$key] MB used \\n\" ";
	$def[$key] .= "GPRINT:var1:MAX:\"%6.2lf MB max used \\n\" ";
	$def[$key] .= "GPRINT:var1:AVERAGE:\"%6.2lf MB avg used\" ";
}
?>
