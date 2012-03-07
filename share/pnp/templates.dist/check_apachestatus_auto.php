<?php
#
# Copyright (c) 2012 Joerg Linge (pitchfork@pnp4nagios.org)
# Plugin: check_apachestatus_auto (http://www.spreendigital.de/blog/nagios/?#check_apachestatus_auto)
#
# Worker
#
$i=0;
$def[$i] = "";
$opt[$i]     = " --title 'Worker'";
$ds_name[$i] = "Workers";
$regex = '/Idle/';
$color = '#00ff00';
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match($regex, $VAL['NAME'])) {
	$def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[$i]    .= rrd::area    ("var".$KEY, $color ,rrd::cut($VAL['NAME'],12), 'STACK' );
	$def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.0lf");
    }
}
$regex = '/Busy/';
$color = '#ff0000';
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match($regex, $VAL['NAME'])) {
	$def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[$i]    .= rrd::area    ("var".$KEY, $color, rrd::cut($VAL['NAME'],12), 'STACK' );
	$def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.0lf");
    }
}
#
# Slots
#
$i++;
$def[$i] = "";
$opt[$i]     = " --title 'Slots'";
$ds_name[$i] = "Slots";
$regex = '/^Slots$/';
$color = '#ff0000';
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match($regex, $VAL['NAME'])) {
	$def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[$i]    .= rrd::area    ("var".$KEY, $color,rrd::cut($VAL['NAME'],12) );
	$def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.0lf");
   }
}
$regex = '/^OpenSlots$/';
$color = '#00ff00';
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match($regex, $VAL['NAME'])) {
	$def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[$i]    .= rrd::area    ("var".$KEY, $color,rrd::cut($VAL['NAME'],12) );
	$def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.0lf");
   }
}
#
# Requests per Second 
#
$i++;
$def[$i]     = "";
$opt[$i]     = " --title Requests/s";
$ds_name[$i] = "Requests/s";
$regex = '/ReqPerSec/';
$color = '#000000';
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match($regex, $VAL['NAME'])) {
	$def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[$i]    .= rrd::line1   ("var".$KEY, $color, rrd::cut($VAL['NAME'],16), 'STACK' );
	$def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.1lf/s");
   }
}
#
# Bytes per Second 
#
$i++;
$def[$i]     = "";
$opt[$i]     = " --title 'Bytes per Second'";
$ds_name[$i] = "Bytes/s";
$regex='/BytesPerSec/';
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match($regex, $VAL['NAME'])) {
	$def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[$i]    .= rrd::line1   ("var".$KEY, rrd::color($KEY),rrd::cut($VAL['NAME'],16), 'STACK' );
	$def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.1lf %sb/s");
   }
}
#
# Stats 
#
$i++;
$def[$i]     = "";
$opt[$i]     = " --title 'Worker States'";
$ds_name[$i] = "Worker States";
$regex='/Starting|DNS|Reading|Sending|Keepalive|Closing|Logging|Finishing/';
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match($regex, $VAL['NAME'])) {
	$def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[$i]    .= rrd::line1   ("var".$KEY, rrd::color($KEY),rrd::cut($VAL['NAME'],16), 'STACK' );
	$def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.0lf".$VAL['UNIT']);
   }
}
?>
