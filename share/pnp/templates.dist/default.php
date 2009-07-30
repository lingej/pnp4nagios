<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Default Template used if no other template is found.
# Don`t delete this file ! 
# $Id: default.php 555 2008-11-16 16:35:59Z pitchfork $
#
#
# Define some colors ..
#
$_WARNRULE = '#FFFF00';
$_CRITRULE = '#FF0000';
$_AREA     = '#EACC00';
$_LINE     = '#000000';
#
# Initial Logic ...
#

foreach ($DS as $key=>$val) {

	$warning = "";
	$minimum = "";
	$critical = "";
	$warning = "";
	$vlabel = "";
	$lower = "0";
	
	if ($WARN[$key] != "") {
		$warning = $WARN[$key];
	}
	if ($CRIT[$key] != "") {
		$critical = $CRIT[$key];
	}
	if ($MIN[$key] != "") {
		$lower = " --lower=" . $MIN[$key];
		$minimum = $MIN[$key];
	}
	if ($MAX[$key] != "") {
		$upper = " --upper=" . $MAX[$key];
		$maximum = $MAX[$key];
	}
	if ($UNIT[$key] == "%%") {
		$vlabel = "%";
	}
	else {
		$vlabel = $UNIT[$key];
	}

	$opt[$key] = '--vertical-label "' . $vlabel . '" --title "' . $hostname . ' / ' . $servicedesc . '"' . $lower;

	$def[$key]  = "DEF:var1=$RRDFILE[$key]:$DS[$key]:AVERAGE ";
	$def[$key] .= "AREA:var1" . $_AREA . ":\"$NAME[$key] \" ";
	$def[$key] .= "LINE1:var1" . $_LINE . ":\"\" ";
	$def[$key] .= "GPRINT:var1:LAST:\"%3.4lf $UNIT[$key] LAST \" ";
	$def[$key] .= "GPRINT:var1:MAX:\"%3.4lf $UNIT[$key] MAX \" ";
	$def[$key] .= "GPRINT:var1:AVERAGE:\"%3.4lf $UNIT[$key] AVERAGE \\n\" ";
	if ($warning != "") {
		$def[$key] .= "HRULE:" . $warning . $_WARNRULE . ':"Warning on  ' . $warning . '\n" ';
	}
	if ($critical != "") {
		$def[$key] .= "HRULE:" . $critical . $_CRITRULE . ':"Critical on ' . $critical . '\n" ';
	}
	$def[$key] .= 'COMMENT:"Default Template\r" ';
	$def[$key] .= 'COMMENT:"Check Command ' . $TEMPLATE[$key] . '\r" ';
}
?>
