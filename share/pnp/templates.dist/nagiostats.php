<?php
# Template used with check_nagiostats written by Jochen Bern
# http://www.monitoringexchange.org/inventory/Check-Plugins/Software/Nagios/check_nagiostats
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
#

$opt[0] = '--title "Check Latency"';
$ds_name[0] = "Check Latency";
$def[0] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/(.*)LAT$/', $VAL['NAME'], $matches)){
   	$i++; 
	$label = ucfirst(strtolower($matches[1]));
	$def[0] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[0] .= rrd::cdef    ("var_sec$KEY", "var$KEY,1000,/");
	$def[0] .= rrd::line1   ("var_sec$KEY", rrd::color($i), rrd::cut($label,10) );
	$def[0] .= rrd::gprint  ("var_sec$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
$opt[1] = '--title "Service Stats"';
$ds_name[1] = "Service Stats";
$def[1] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^NUMSVC(.*)$/', $VAL['NAME'], $matches)){
   	$i++; 
	$label = ucfirst(strtolower($matches[1]));
	$def[1] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[1] .= rrd::line1   ("var$KEY", rrd::color($i), rrd::cut($label,10) );
	$def[1] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
$opt[2] = '--title "Host Stats"';
$ds_name[2] = "Host Stats";
$def[2] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/^NUMHST(.*)$/', $VAL['NAME'], $matches)){
   	$i++; 
	$label = ucfirst(strtolower($matches[1]));
	$def[2] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[2] .= rrd::line1   ("var$KEY", rrd::color($i), rrd::cut($label,10) );
	$def[2] .= rrd::gprint  ("var$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
$opt[3] = '--title "Check Execution Time"';
$ds_name[3] = "Execution Time";
$def[3] = "";
$i = 0;
foreach ($this->DS as $KEY=>$VAL) {
    if(preg_match('/(.*)EXT$/', $VAL['NAME'], $matches)){
   	$i++; 
	$label = ucfirst(strtolower($matches[1]));
	$def[3] .= rrd::def     ("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	$def[3] .= rrd::cdef    ("var_sec$KEY", "var$KEY,1000,/");
	$def[3] .= rrd::line1   ("var_sec$KEY", rrd::color($i), rrd::cut($label,10) );
	$def[3] .= rrd::gprint  ("var_sec$KEY", array("LAST","MAX","AVERAGE"), "%8.2lf");
    }
}
?>
