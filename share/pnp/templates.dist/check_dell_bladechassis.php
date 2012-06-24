<?php
#
# PNP4Nagios template for check_dell_bladechassis
# http://folk.uio.no/trondham/software/check_dell_bladechassis.html
#
# $Id: check_dell_bladechassis.php 16833 2010-03-16 13:54:15Z trondham $
#

# Array with different colors
$colors = array("0022ff", "22ff22", "ff0000", "00aaaa", "ff00ff",
		"ffa500", "cc0000", "0000cc", "0080C0", "8080C0",
		"FF0080", "800080", "688e23", "408080", "808000",
		"000000", "00FF00", "0080FF", "FF8000", "800000",
		"FB31FB");

# Color for power usage in watts
$PWRcolor = "66FF00";

# Color for amperage usage in amperes
$AMPcolor = "FFCC00";

# Counters
$count = 0;  # general counter
$v = 0;      # volt probe counter
$a = 0;      # amp probe counter

# Flags
$visited_amp  = 0;
$visited_volt = 0;

# Title
$def_title = 'Dell Blade Enclosure';

# loop through the performance data
foreach ($DS as $i) {

    # Total Wattage
    if(preg_match('/^total_watt/',$NAME[$i])) {
	$NAME[$i] = 'Total Power Usage';

	++$count;
	$ds_name[$count] = "Total Power Consumption";
	$vlabel = "Watt";

	$title = $ds_name[$count];
 
	$opt[$count] = "--slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";
 
	if(isset($def[$count])){
	    $def[$count] .= "DEF:var$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
	}
	else {
	    $def[$count] = "DEF:var$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
	}
        $def[$count] .= "AREA:var$i#$PWRcolor:\"$NAME[$i]\" " ;
        $def[$count] .= "LINE:var$i#000000: " ;
	$def[$count] .= "GPRINT:var$i:LAST:\"%6.0lf W last \" ";
	$def[$count] .= "GPRINT:var$i:MAX:\"%6.0lf W max \" ";
	$def[$count] .= "GPRINT:var$i:AVERAGE:\"%6.2lf W avg \\n\" ";
    }

    # Total Amperage
    if(preg_match('/^total_amp/',$NAME[$i])) {
	$NAME[$i] = 'Total Current';

	++$count;
	$ds_name[$count] = "Total Amperage";
	$vlabel = "Ampere";

	$title = $ds_name[$count];
 
	$opt[$count] = "-X0 --slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";
 
	if(isset($def[$count])){
	    $def[$count] .= "DEF:var$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
	}
	else {
	    $def[$count] = "DEF:var$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
	}
        $def[$count] .= "AREA:var$i#$AMPcolor:\"$NAME[$i]\" " ;
        $def[$count] .= "LINE:var$i#000000: " ;
	$def[$count] .= "GPRINT:var$i:LAST:\"%4.2lf A last \" ";
	$def[$count] .= "GPRINT:var$i:MAX:\"%4.2lf A max \" ";
	$def[$count] .= "GPRINT:var$i:AVERAGE:\"%4.4lf A avg \\n\" ";
    }

    # Voltage per PSU
    if(preg_match('/^volt_/',$NAME[$i])){
	if ($visited_volt == 0) {
	    ++$count;
	    $visited_volt = 1;
	}
	
	$NAME[$i] = preg_replace('/^volt_ps(\d+)/', 'PowerSupply $1', $NAME[$i]);

	$ds_name[$count] = "PS Voltage";
	$vlabel = "Volt";

	$opt[$count] = "-X0 --slope-mode --vertical-label \"$vlabel\" --title \"$def_title: Power Supply Voltage\" ";
 
	if(isset($def[$count])){
	    $def[$count] .= "DEF:var$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
	}
	else {
	    $def[$count] = "DEF:var$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
	}
	$def[$count] .= "LINE:var$i#".$colors[$v++].":\"$NAME[$i]\" " ;
	$def[$count] .= "GPRINT:var$i:LAST:\"%3.2lf V last \" ";
	$def[$count] .= "GPRINT:var$i:MAX:\"%3.2lf V max \" ";
	$def[$count] .= "GPRINT:var$i:AVERAGE:\"%3.2lf V avg \\n\" ";
    }
 
    # Amperage per PSU
    if(preg_match('/^amp_/',$NAME[$i])){
	if ($visited_amp == 0) {
	    ++$count;
	    $visited_amp = 1;
	}

	$NAME[$i] = preg_replace('/^amp_ps(\d+)/', 'PowerSupply $1', $NAME[$i]);

	$ds_name[$count] = "PS Amperage";
	$vlabel = "Ampere";

	$opt[$count] = "-X0 --slope-mode --vertical-label \"$vlabel\" --title \"$def_title: Power Supply Amperage\" ";
 
	if(isset($def[$count])){
	    $def[$count] .= "DEF:var$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
	}
	else {
	    $def[$count] = "DEF:var$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
	}
	$def[$count] .= "LINE:var$i#".$colors[$a++].":\"$NAME[$i]\" " ;
	$def[$count] .= "GPRINT:var$i:LAST:\"%2.3lf A last \" ";
	$def[$count] .= "GPRINT:var$i:MAX:\"%2.3lf A max \" ";
	$def[$count] .= "GPRINT:var$i:AVERAGE:\"%2.3lf A avg \\n\" ";
    }

}
?>
