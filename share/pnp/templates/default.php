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
$WARNRULE   = '#FFFF00';
$CRITRULE   = '#FF0000';
$AREA       = '#EACC00';
$LINE       = '#000000';
#
# Initial Logic ...
#

foreach ($DS as $i=>$value) {
	$warning = "";
	$minimum = "";
	$critical = "";
	$warning = "";
	$vlabel = "";
	$lower = "";
	
	if ($WARN[$i] != "") {
		$warning = $WARN[$i];
	}
	if ($CRIT[$i] != "") {
		$critical = $CRIT[$i];
	}
	if ($MIN[$i] != "") {
		$lower = " --lower=" . $MIN[$i];
		$minimum = $MIN[$i];
	}
	if ($MAX[$i] != "") {
		$upper = " --upper=" . $MAX[$i];
		$maximum = $MAX[$i];
	}
	if ($UNIT[$i] == "%%") {
		$vlabel = "%";
	}
	else {
		$vlabel = $UNIT[$i];
	}

	$ds_name[$i] = $NAME[$i];
	$opt[$i] = '--vertical-label "' . $vlabel . '" --title "' . $hostname . ' / ' . $servicedesc . '"' . $lower;

	$def[$i] = "DEF:var1=$RRDFILE[$i]:$DS[$i]:AVERAGE ";
	$def[$i] .= "AREA:var1" . $AREA . ":\"$NAME[$i] \" ";
	$def[$i] .= "LINE1:var1" . $LINE . ":\"\" ";
	$def[$i] .= "GPRINT:var1:LAST:\"%3.4lf $UNIT[$i] LAST \" ";
	$def[$i] .= "GPRINT:var1:MAX:\"%3.4lf $UNIT[$i] MAX \" ";
	$def[$i] .= "GPRINT:var1:AVERAGE:\"%3.4lf $UNIT[$i] AVERAGE \\n\" ";
	if ($warning != "") {
		$def[$i] .= "HRULE:" . $warning . $WARNRULE . ':"Warning on  ' . $warning . '\n" ';
	}
	if ($critical != "") {
		$def[$i] .= "HRULE:" . $critical . $CRITRULE . ':"Critical on ' . $critical . '\n" ';
	}
	$def[$i] .= 'COMMENT:"Default Template\r" ';
	$def[$i] .= 'COMMENT:"Check Command ' . $TEMPLATE[$i] . '\r" ';
}
?>
