<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Default Template used if no other template is found.
# Don`t delete this file ! 
#
# Define some colors ..
#
$_WARNRULE = '#FFFF00';
$_CRITRULE = '#FF0000';
$_AREA     = '#256aef';
$_LINE     = '#000000';
#
# Initial Logic ...
#

foreach ($this->DS as $KEY=>$VAL) {

	$maximum  = "";
	$minimum  = "";
	$critical = "";
	$warning  = "";
	$vlabel   = "";
	$lower    = "";
	$upper    = "";
	
	if ($VAL['WARN'] != "") {
		$warning = $VAL['WARN'];
	}
	if ($VAL['CRIT'] != "") {
		$critical = $VAL['CRIT'];
	}
	if ($VAL['MIN'] != "") {
		$lower = " --lower=" . $VAL['MIN'];
		$minimum = $VAL['MIN'];
	}
	if ($VAL['MAX'] != "") {
		$maximum = $VAL['MAX'];
	}
	if ($VAL['UNIT'] == "%%") {
		$vlabel = "%";
		$upper = " --upper=101 ";
		$lower = " --lower=0 ";
	}
	else {
		$vlabel = $VAL['UNIT'];
	}

	$opt[$KEY] = '--vertical-label "' . $vlabel . '" --title "' . $this->MACRO['DISP_HOSTNAME'] . ' / ' . $this->MACRO['DISP_SERVICEDESC'] . '"' . $upper . $lower;
	$ds_name[$KEY] = $VAL['LABEL'];
	$def[$KEY]  = "DEF:var1=".$VAL['RRDFILE'].":".$VAL['DS'].":AVERAGE ";
	$def[$KEY] .= "AREA:var1" . $_AREA . ":\"".$VAL['NAME']."\" ";
	$def[$KEY] .= "LINE1:var1" . $_LINE . ":\"\" ";
	$def[$KEY] .= "GPRINT:var1:LAST:\"%3.4lf ".$VAL['UNIT']." LAST \" ";
	$def[$KEY] .= "GPRINT:var1:MAX:\"%3.4lf ".$VAL['UNIT']." MAX \" ";
	$def[$KEY] .= "GPRINT:var1:AVERAGE:\"%3.4lf ".$VAL['UNIT']." AVERAGE \\n\" ";
	if ($warning != "") {
		$def[$KEY] .= "HRULE:" . $warning . $_WARNRULE . ':"Warning on  ' . $warning . '\n" ';
	}
	if ($critical != "") {
		$def[$KEY] .= "HRULE:" . $critical . $_CRITRULE . ':"Critical on ' . $critical . '\n" ';
	}
	$def[$KEY] .= 'COMMENT:"Default Template\r" ';
	$def[$KEY] .= 'COMMENT:"Check Command ' . $VAL['TEMPLATE'] . '\r" ';
}
?>
