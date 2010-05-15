<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Template for check_disk
#
# RRDtool Options

foreach ($this->DS as $KEY=>$VAL) {
	$ds_name[$KEY] = str_replace("_","/",$VAL['NAME']);
	$opt[$KEY] = "--vertical-label MB -l 0 -u " . $VAL['MAX'] . " --title \"Filesystem " . $ds_name[$KEY] . "\" ";
	# Graph Definitions
	$def[$KEY]  = "DEF:var1=" . $VAL['RRDFILE'] . ":" . $VAL['DS'] .":AVERAGE "; 
	$def[$KEY] .= "AREA:var1#c6c6c6:\"" . $ds_name[$KEY] . "\\l\" "; 
	$def[$KEY] .= "LINE1:var1#003300: "; 
	$def[$KEY] .= "HRULE:" . $VAL['MAX'] . "#003300:\"Size " . $VAL['MAX'] . "  MB \" ";
	if ($VAL['WARN'] != "") {  
		$def[$KEY] .= "HRULE:" . $VAL['WARN'] . "#ffff00:\"Warning on " . $VAL['WARN'] . " MB \" ";
	}
	if ($VAL['CRIT'] != "") {  
		$def[$KEY] .=  "HRULE:" . $VAL['CRIT'] . "#ff0000:\"Critical on " . $VAL['CRIT'] . " MB \\n\" ";       
	}
	$def[$KEY] .= "GPRINT:var1:LAST:\"%6.2lf MB of " . $VAL['MAX'] . " MB used \\n\" ";
	$def[$KEY] .= "GPRINT:var1:MAX:\"%6.2lf MB max used \\n\" ";
    $def[$KEY] .= "GPRINT:var1:AVERAGE:\"%6.2lf MB avg used\" ";
}
?>
