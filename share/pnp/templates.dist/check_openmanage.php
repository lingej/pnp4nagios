<?php
#
# PNP4Nagios template for check_openmanage 
# Author: 	Trond Hasle Amundsen
# Contact: 	t.h.amundsen@usit.uio.no
# Website:  http://folk.uio.no/trondham/software/check_openmanage.html
# Date: 	2009-08-25

# Array with different colors
$colors = array("0022ff", "22ff22", "ff0000", "00aaaa", "ff00ff",
	"ffa500", "cc0000", "0000cc", "0080C0", "8080C0",
	"FF0080", "800080", "688e23", "408080", "808000",
	"000000", "00FF00", "0080FF", "FF8000", "800000",
	"FB31FB");

# Color for power usage in watts
$PWRcolor = "66FF00";

# Counters
$count = 0;  # general counter
$f = 0;      # fan probe counter
$t = 0;      # temp probe counter
$a = 0;      # amp probe counter
$e = 0;      # enclosure counter

# Flags
$visited_fan  = 0;
$visited_temp = 0;
$visited_pwr  = 0;

# Enclosure id
$enclosure_id = '';

# Default title
$def_title = 'Dell OpenManage';

# Loop through the performance data
foreach ($DS as $i) {
	
	# AMPERAGE PROBE (Watts)
	if(preg_match('/^pwr_mon_/',$NAME[$i]) && $UNIT[$i] == 'W') {
		$NAME[$i] = preg_replace('/^pwr_mon_\d+_/', '', $NAME[$i]);
		$NAME[$i] = preg_replace('/_/', ' ', $NAME[$i]);
		
		++$count;
		$ds_name[$count] = "Power Consumption";
		$vlabel = "Watt";
		
		$title = $ds_name[$count];
		
		$opt[$count] = "--slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";
		
		$def[$count] = "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;
		$def[$count] .= "AREA:var$i#$PWRcolor:\"$NAME[$i]\" " ;
		$def[$count] .= "LINE:var$i#000000: " ;
		$def[$count] .= "GPRINT:var$i:LAST:\"%6.0lf $UNIT[$i] last \" ";
		$def[$count] .= "GPRINT:var$i:MAX:\"%6.0lf $UNIT[$i] max \" ";
		$def[$count] .= "GPRINT:var$i:AVERAGE:\"%6.2lf $UNIT[$i] avg \\n\" ";
	}
	
	# AMPERAGE PROBES (Ampere)
	if(preg_match('/^pwr_mon_/',$NAME[$i]) && $UNIT[$i] == 'A') {
		$NAME[$i] = preg_replace('/^pwr_mon_\d+_/', '', $NAME[$i]);
		$NAME[$i] = preg_replace('/_/', ' ', $NAME[$i]);
		$NAME[$i] = preg_replace('/ current \d+$/', '', $NAME[$i]);
		$NAME[$i] = preg_replace('/ps/', 'PowerSupply', $NAME[$i]);
		
		if ($visited_pwr == 0) {
			++$count;
			$visited_pwr = 1;
		}
		$ds_name[$count] = "Amperage Probes";
		$vlabel = "Ampere";
		
		$title = $ds_name[$count];
		
		$opt[$count] = "-X0 --lower-limit 0 --slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";
		if(isset($def[$count])){
		    $def[$count] .= "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;
		}else{
		    $def[$count] = "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;
		}
		$def[$count] .= "LINE:var$i#".$colors[$a].":\"$NAME[$i]\" " ;
		$def[$count] .= "AREA:var$i#".$colors[$a++]."20: " ;
		$def[$count] .= "GPRINT:var$i:LAST:\"%4.2lf $UNIT[$i] last \" ";
		$def[$count] .= "GPRINT:var$i:MAX:\"%4.2lf $UNIT[$i] max \" ";
		$def[$count] .= "GPRINT:var$i:AVERAGE:\"%4.4lf $UNIT[$i] avg \\n\" ";
	}
	
	# FANS (RPMs)
	if(preg_match('/^fan_/',$NAME[$i])){
		if ($visited_fan == 0) {
			++$count;
			$visited_fan = 1;
		}
		
		$NAME[$i] = preg_replace('/^fan_\d+_/', '', $NAME[$i]);
		$NAME[$i] = preg_replace('/_rpm$/', '', $NAME[$i]);
		$NAME[$i] = preg_replace('/_/', ' ', $NAME[$i]);
		
		$ds_name[$count] = "Fan Speed";
		
		$opt[$count] = "-X0 --slope-mode --vertical-label \"RPMs\" --title \"$def_title: Fan Speeds\" ";
		if(isset($def[$count])){
		    $def[$count] .= "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;
		}else{
		    $def[$count] = "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;
		}
		$def[$count] .= "LINE:var$i#".$colors[$f++].":\"$NAME[$i]\" " ;
		$def[$count] .= "GPRINT:var$i:LAST:\"%6.0lf RPM last \" ";
		$def[$count] .= "GPRINT:var$i:MAX:\"%6.0lf RPM max \" ";
		$def[$count] .= "GPRINT:var$i:AVERAGE:\"%6.2lf RPM avg \\n\" ";
	}
	
	# TEMPERATURES (Celcius)
	if(preg_match('/^temp_/',$NAME[$i])){
		if ($visited_temp == 0) {
			++$count;
			$visited_temp = 1;
		}
		$NAME[$i] = preg_replace('/^temp_\d+_/', '', $NAME[$i]);
		$NAME[$i] = preg_replace('/_/', ' ', $NAME[$i]);
		
		$ds_name[$count] = "Chassis Temperatures";
		
		$warnThresh = "INF";
		$critThresh = "INF";
		
		if ($WARN[$i] != "") {
			$warnThresh = $WARN[$i];
		}
		if ($CRIT[$i] != "") {
			$critThresh = $CRIT[$i];
		}
		
		$opt[$count] = "--slope-mode --vertical-label \"Celcius\" --title \"$def_title: Chassis Temperatures\" ";
		if(isset($def[$count])){
		    $def[$count] .= "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;
		}else{
		    $def[$count] = "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;
		}
		$def[$count] .= "LINE:var$i#".$colors[$t++].":\"$NAME[$i]\" " ;
		
		$def[$count] .= "GPRINT:var$i:LAST:\"%6.0lf $UNIT[$i] last \" ";
		$def[$count] .= "GPRINT:var$i:MAX:\"%6.0lf $UNIT[$i] max \" ";
		$def[$count] .= "GPRINT:var$i:AVERAGE:\"%6.2lf $UNIT[$i] avg \\n\" ";
	}
	
	# ENCLOSURE TEMPERATURES (Celcius)
	if(preg_match('/^enclosure_(?<id>.+?)_temp_\d+$/', $NAME[$i], $matches)){
		$this_id = $matches['id'];
		
		if ($enclosure_id != $this_id) {
			$e = 0;
			++$count;
			$enclosure_id = $this_id;
		}
		$NAME[$i] = preg_replace('/^enclosure_.+?_temp_(\d+)$/', 'Probe $1', $NAME[$i]);
		
		$ds_name[$count] = "Enclosure $enclosure_id Temperatures";
		
		$warnThresh = "INF";
		$critThresh = "INF";
		
		if ($WARN[$i] != "") {
			$warnThresh = $WARN[$i];
		}
		if ($CRIT[$i] != "") {
			$critThresh = $CRIT[$i];
		}
		
		$opt[$count] = "--slope-mode --vertical-label \"Celcius\" --title \"$def_title: Enclosure $enclosure_id Temperatures\" ";
		
		if(isset($def[$count])){
		    $def[$count] .= "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;
		}else{
		    $def[$count] = "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;
		}
		$def[$count] .= "LINE:var$i#".$colors[$e++].":\"$NAME[$i]\" " ;
		
		$def[$count] .= "GPRINT:var$i:LAST:\"%6.0lf $UNIT[$i] last \" ";
		$def[$count] .= "GPRINT:var$i:MAX:\"%6.0lf $UNIT[$i] max \" ";
		$def[$count] .= "GPRINT:var$i:AVERAGE:\"%6.2lf $UNIT[$i] avg \\n\" ";
	}
	
}
?>
