<?php
#
# check_openmanage 3.4.6
# Author: 	Trond Hasle Amundsen
# Contact: 	t.h.amundsen@usit.uio.no
# Date: 	2009-08-25
# 

$colors = array("0022ff", "22ff22", "ff0000", "00aaaa", "ff00ff",
		"ffa500", "cc0000", "0000cc", "0080C0", "8080C0",
		"FF0080", "800080", "688e23", "408080", "808000",
		"000000", "00FF00", "0080FF", "FF8000", "800000",
		"FB31FB");


$PWRcolor = "FFFF00";
$AMPcolor = "ff00ff";

# counters
$count=0;  # general counter
$f = 0;    # fan probe counter
$t = 0;    # temp probe counter
$a = 0;    # amp probe counter
$e = 0;    # enclosure counter

# flags
$visited_fan  = 0;
$visited_temp = 0;
$visited_pwr  = 0;

# enclosure id
$enclosure_id = '';

# loop through the performance data
foreach ($DS as $i) {
    #$def[$i] = "";
    # AMPERAGE PROBE (Watts)
    if(preg_match('/^pwr_mon_/',$NAME[$i]) && $UNIT[$i] == 'W') {
	$NAME[$i] = preg_replace('/^pwr_mon_\d+_/', '', $NAME[$i]);

	++$count;
	$ds_name[$count] = "Power Consumption";
	$vlabel = "Watt";

	$title = $ds_name[$count];
 
	$opt[$count] = "--slope-mode --vertical-label \"$vlabel\" --title \"(Dell OMSA) $title\" ";
 
	$def[$count] = "DEF:var$i=$rrdfile:$DS[$i]:AVERAGE " ;

        $def[$count] .= "CDEF:c${i}shading2=var$i,0.98,* ";
        $def[$count] .= "CDEF:c${i}shading10=var$i,0.90,* ";
        $def[$count] .= "CDEF:c${i}shading15=var$i,0.85,* ";
        $def[$count] .= "CDEF:c${i}shading20=var$i,0.80,* ";
        $def[$count] .= "CDEF:c${i}shading25=var$i,0.75,* ";
        $def[$count] .= "CDEF:c${i}shading30=var$i,0.70,* ";
        $def[$count] .= "CDEF:c${i}shading35=var$i,0.65,* ";
        $def[$count] .= "CDEF:c${i}shading40=var$i,0.60,* ";
        $def[$count] .= "CDEF:c${i}shading45=var$i,0.55,* ";
        $def[$count] .= "CDEF:c${i}shading50=var$i,0.50,* ";
        $def[$count] .= "CDEF:c${i}shading55=var$i,0.45,* ";
        $def[$count] .= "CDEF:c${i}shading60=var$i,0.40,* ";
        $def[$count] .= "CDEF:c${i}shading65=var$i,0.35,* ";
        $def[$count] .= "CDEF:c${i}shading70=var$i,0.30,* ";
        $def[$count] .= "CDEF:c${i}shading75=var$i,0.25,* ";
        $def[$count] .= "CDEF:c${i}shading80=var$i,0.20,* ";
        $def[$count] .= "CDEF:c${i}shading85=var$i,0.15,* ";

	$def[$count] .= "AREA:var$i#$PWRcolor:\"$NAME[$i]\" " ;

        $def[$count] .= "AREA:c${i}shading2#F9000022: ";
        $def[$count] .= "AREA:c${i}shading10#E1000011: ";
        $def[$count] .= "AREA:c${i}shading15#D2000011: ";
        $def[$count] .= "AREA:c${i}shading20#C3000011: ";
        $def[$count] .= "AREA:c${i}shading25#B4000011: ";
        $def[$count] .= "AREA:c${i}shading30#A5000011: ";
        $def[$count] .= "AREA:c${i}shading35#96000011: ";
        $def[$count] .= "AREA:c${i}shading40#87000011: ";
        $def[$count] .= "AREA:c${i}shading45#78000011: ";
        $def[$count] .= "AREA:c${i}shading50#69000011: ";
        $def[$count] .= "AREA:c${i}shading55#5A000011: ";
        $def[$count] .= "AREA:c${i}shading60#4B000011: ";
        $def[$count] .= "AREA:c${i}shading65#3C000011: ";
        $def[$count] .= "AREA:c${i}shading70#2D000011: ";
        $def[$count] .= "AREA:c${i}shading75#18000011: ";
        $def[$count] .= "AREA:c${i}shading80#0F000011: ";
        $def[$count] .= "AREA:c${i}shading85#00000011: ";

	$def[$count] .= "LINE:var$i#000000: " ;

	$def[$count] .= "GPRINT:var$i:LAST:\"%6.0lf $UNIT[$i] last \" ";
	$def[$count] .= "GPRINT:var$i:MAX:\"%6.0lf $UNIT[$i] max \" ";
	$def[$count] .= "GPRINT:var$i:AVERAGE:\"%6.2lf $UNIT[$i] avg \\n\" ";
    }

    # AMPERAGE PROBES (Ampere)
    if(preg_match('/^pwr_mon_/',$NAME[$i]) && $UNIT[$i] == 'A') {
	$NAME[$i] = preg_replace('/^pwr_mon_\d+_/', '', $NAME[$i]);

	if ($visited_pwr == 0) {
	    ++$count;
	    $visited_pwr = 1;
	}
	$ds_name[$count] = "Amperage Probe";
	$vlabel = "Ampere";

	$title = $ds_name[$count];
 
	$opt[$count] = "-X0 --lower-limit 0 --slope-mode --vertical-label \"$vlabel\" --title \"(Dell OMSA) $title\" ";
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

	$ds_name[$count] = "Fan Speed";
 
	$opt[$count] = "-X0 --slope-mode --vertical-label \"RPMs\" --title \"(Dell OMSA) Fan Speeds\" ";

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

	$ds_name[$count] = "Chassis Temperatures";

	$warnThresh = "INF";
	$critThresh = "INF";

	if ($WARN[$i] != "") {
	    $warnThresh = $WARN[$i];
	}
	if ($CRIT[$i] != "") {
	    $critThresh = $CRIT[$i];
	}

	$opt[$count] = "--slope-mode --vertical-label \"Celcius\" --title \"(Dell OMSA) Chassis Temperatures\" ";
 
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

	$opt[$count] = "--slope-mode --vertical-label \"Celcius\" --title \"(Dell OMSA) Enclosure $enclosure_id Temperatures\" ";

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
