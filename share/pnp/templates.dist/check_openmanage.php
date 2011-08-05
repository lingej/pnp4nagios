<?php
#
# PNP4Nagios template for check_openmanage 
# Author: 	Trond Hasle Amundsen
# Contact: 	t.h.amundsen@usit.uio.no
# Website:      http://folk.uio.no/trondham/software/check_openmanage.html
# Date: 	2011-06-01
#
# $Id: check_openmanage.php 20353 2011-06-06 13:10:52Z trondham $
#
# Copyright (C) 2008-2011 Trond H. Amundsen
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
# General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

# Array with different colors
$colors = array("0022ff", "22ff22", "ff0000", "00aaaa", "ff00ff",
		"ffa500", "cc0000", "0000cc", "0080C0", "8080C0",
		"FF0080", "800080", "688e23", "408080", "808000",
		"000000", "00FF00", "0080FF", "FF8000", "800000",
		"FB31FB");

# Counters
$f = 0;      # fan probe counter
$t = 0;      # temp probe counter
$a = 0;      # amp probe counter
$v = 0;      # volt probe counter
$e = 0;      # enclosure counter

# Flags
$visited_amp  = 0;

# IDs
$id_temp1 = 1;
$id_temp2 = 2;
$id_watt  = 3;
$id_amp   = 4;
$id_volt  = 5;
$id_fan   = 6;
$id_enc   = 7;

# Enclosure id
$enclosure_id = '';

# Default title
$def_title = 'Dell OpenManage';

# Temperature unit
if (!defined('tempunit_defined')) {
    define('tempunit_defined', 1);

    function tempunit($arg) 
    {
	$unit   = 'unknown';
	$vlabel = 'unknown';
	    
	switch ($arg) {
	default:
	    $vlabel = "Celsius";
	    $unit = "°C";
	    break;
	case "F":
	    $vlabel = "Fahrenheit";
	    $unit = "°F";
	    break;
	case "K":
	    $vlabel = "Kelvin";
	    $unit = "K";
	    break;
	case "R":
	    $vlabel = "Rankine";
	    $unit = "°R";
	    break;
	}
	return array($unit, $vlabel);
    }
}


# Determine if we're running in legacy mode
$legacy = "no";
foreach ($this->DS as $KEY=>$VAL) {
    if (preg_match('/^(pwr_mon_|fan_|temp_|enclosure_|p\d+|f\d+|t\d+|e.+?t\d+)/', $VAL['LABEL'])) {
	$legacy = "yes";
	break;
    }
}

#------------------------------------------------------
#  MAIN LOOP
#------------------------------------------------------
if ($legacy == "yes") {  # --legacy--

    $count = 0;  # general counter
    $PWRcolor = "66FF00";

    # Flags
    $visited_fan  = 0;
    $visited_temp = 0;
    $visited_pwr  = 0;

    # Loop through the performance data
    foreach ($this->DS as $KEY=>$VAL) {
        
	# AMPERAGE PROBE
	if(preg_match('/^pwr_mon_/', $VAL['NAME']) || preg_match('/^p\d+[aw]$/', $VAL['NAME'])) {

	    # Watt
	    if (preg_match('/system/', $VAL['NAME']) || preg_match('/^p\d+w$/', $VAL['NAME'])) {
        
		# Long label
		$VAL['NAME'] = preg_replace('/^pwr_mon_\d+_/', '', $VAL['NAME']);
		$VAL['NAME'] = preg_replace('/_/', ' ', $VAL['NAME']);

		# Short label
		$VAL['NAME'] = preg_replace('/^p(\d+)w$/', 'Power Probe $1', $VAL['NAME']);

		++$count;
		$ds_name[$count] = "Power Consumption";
		$vlabel = "Watt";

		$title = $ds_name[$count];

		$opt[$count] = "--slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";

		if(isset($def[$count])){
		    $def[$count] .= rrd::def("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
		}
		else {
		    $def[$count] = rrd::def("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
		}
		$def[$count] .= rrd::area("var$KEY", "#".$PWRcolor, $VAL['NAME']);
		$def[$count] .= rrd::line1("var$KEY","#000000");
		$def[$count] .= rrd::gprint("var$KEY",array("LAST", "MAX", "AVERAGE"), "%6.0lf W");
	    }
	    # Ampere
	    elseif (preg_match('/current/', $VAL['NAME']) || preg_match('/^p\d+a$/', $VAL['NAME'])) {

		# Long label
		$VAL['NAME'] = preg_replace('/^pwr_mon_\d+_/', '', $VAL['NAME']);
		$VAL['NAME'] = preg_replace('/_/', ' ', $VAL['NAME']);
		$VAL['NAME'] = preg_replace('/ current \d+$/', '', $VAL['NAME']);
		$VAL['NAME'] = preg_replace('/ps/', 'PowerSupply', $VAL['NAME']);

		# Short label
		$VAL['NAME'] = preg_replace('/^p(\d+)a$/', 'Amperage Probe $1', $VAL['NAME']);
                
		if ($visited_pwr == 0) {
		    ++$count;
		    $visited_pwr = 1;
		}
		$ds_name[$count] = "Amperage Probes";
		$vlabel = "Ampere";

		$title = $ds_name[$count];

		$opt[$count] = "-X0 --lower-limit 0 --slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";
		if(isset($def[$count])){
		    $def[$count] .= rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
		}
		else {
		    $def[$count] = rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
		}
		$def[$count] .= rrd::line1("var$KEY", "#".$colors[$a], $VAL['NAME']) ;
		$def[$count] .= rrd::area("var$KEY","#".$colors[$a++]."20") ;
		$def[$count] .= rrd::gprint("var$KEY", array("LAST", "MAX", "AVERAGE"),"%4.2lf A") ;
	    }
	}

	# FANS (RPMs)
	if(preg_match('/^fan_/', $VAL['NAME']) || preg_match('/^f\d+$/', $VAL['NAME'])){
	    if ($visited_fan == 0) {
		++$count;
		$visited_fan = 1;
	    }

	    # Long label
	    $VAL['NAME'] = preg_replace('/^fan_\d+_/', '', $VAL['NAME']);
	    $VAL['NAME'] = preg_replace('/_rpm$/', '', $VAL['NAME']);
	    $VAL['NAME'] = preg_replace('/_/', ' ', $VAL['NAME']);

	    # Short label
	    $VAL['NAME'] = preg_replace('/^f(\d+)$/', 'Fan Probe $1', $VAL['NAME']);

	    $ds_name[$count] = "Fan Speed";

	    $opt[$count] = "-X0 --slope-mode --vertical-label \"RPMs\" --title \"$def_title: Fan Speeds\" ";
	    if(isset($def[$count])){
		$def[$count] .= rrd::def("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
	    }
	    else {
		$def[$count] = rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE");
	    }
	    $def[$count] .= rrd::line1("var$KEY", "#".$colors[$f++],$VAL['NAME']);
	    $def[$count] .= rrd::gprint("var$KEY", array("LAST", "MAX", "AVERAGE"),"%6.0lf RPM");
	}
        
	# TEMPERATURES (Celsius)
	if(preg_match('/^temp_/', $VAL['NAME']) || preg_match('/^t\d+$/', $VAL['NAME'])){
	    if ($visited_temp == 0) {
		++$count;
		$visited_temp = 1;
	    }

	    # Long label
	    $VAL['NAME'] = preg_replace('/^temp_\d+_/', '', $VAL['NAME']);
	    $VAL['NAME'] = preg_replace('/_/', ' ', $VAL['NAME']);

	    # Short label
	    $VAL['NAME'] = preg_replace('/^t(\d+)$/', 'Temperature Probe $1', $VAL['NAME']);

	    $ds_name[$count] = "Chassis Temperatures";

	    $warnThresh = "INF";
	    $critThresh = "INF";

	    if ($VAL['WARN'] != "") {
		$warnThresh = $VAL['WARN'];
	    }
	    if ($VAL['CRIT'] != "") {
		$critThresh = $VAL['CRIT'];
	    }

	    $opt[$count] = "--slope-mode --vertical-label \"Celsius\" --title \"$def_title: Chassis Temperatures\" ";
	    if(isset($def[$count])){
		$def[$count] .= rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
	    }
	    else {
		$def[$count] = rrd::def("var$KEY", $VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
	    }
	    $def[$count] .= rrd::line1("var$KEY", "#".$colors[$t++], $VAL['NAME']);
	    $def[$count] .= rrd::gprint("var$KEY",array("LAST", "MAX", "AVERAGE"),"%6.0lf C");
	}
        
	# ENCLOSURE TEMPERATURES (Celsius)
	if(preg_match('/^enclosure_(?P<id>.+?)_temp_\d+$/', $VAL['NAME'], $matches)
	   || preg_match('/^e(?P<id>.+?)t\d+$/', $VAL['NAME'], $matches)) {
	    $this_id = $matches['id'];

	    if ($enclosure_id != $this_id) {
		$e = 0;
		++$count;
		$enclosure_id = $this_id;
	    }

	    # Long label
	    $VAL['NAME'] = preg_replace('/^enclosure_.+?_temp_(\d+)$/', 'Probe $1', $VAL['NAME']);

	    # Short label
	    $VAL['NAME'] = preg_replace('/^e.+?t(\d+)$/', 'Probe $1', $VAL['NAME']);

	    $ds_name[$count] = "Enclosure $enclosure_id Temperatures";

	    $warnThresh = "INF";
	    $critThresh = "INF";

	    if ($VAL['WARN'] != "") {
		$warnThresh = $VAL['WARN'];
	    }
	    if ($VAL['CRIT'] != "") {
		$critThresh = $VAL['CRIT'];
	    }

	    $opt[$count] = "--slope-mode --vertical-label \"Celsius\" --title \"$def_title: Enclosure $enclosure_id Temperatures\" ";

	    if(isset($def[$count])){
		$def[$count] .= rrd::def("var$KEY", $VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
	    }
	    else {
		$def[$count] = rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
	    }
	    $def[$count] .= rrd::line1("var$KEY","#".$colors[$e++],$VAL['NAME']) ;
	    $def[$count] .= rrd::gprint("var$KEY", array("LAST", "MAX", "AVERAGE"),"%6.0lf C");
	}
    }
}
else {  # --new--
	
    # Loop through the performance data
    foreach ($this->DS as $KEY=>$VAL) {
	
	$label = $VAL['LABEL'];

	# TEMPERATURES (AMBIENT)
	if (preg_match('/^T/', $label) && preg_match('/Ambient/', $label)) {

	    # Temperature unit and vertical label
	    list ($unit, $vlabel) = tempunit($VAL['UNIT']);

	    # Long label
	    $label = preg_replace('/^T(\d+)_(.+)/', '$2', $label);
	    $label = preg_replace('/_/', ' ', $label);

	    # Short label
	    $label = preg_replace('/^T(\d+)$/', 'Probe $1', $label);

	    $ds_name[$id_temp1] = "Temperatures";

	    $opt[$id_temp1] = "--slope-mode --vertical-label \"$vlabel\" --title \"$def_title: Ambient Temperature\" ";
	    if(isset($def[$id_temp1])){
		$def[$id_temp1] .= rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
	    }
	    else {
		$def[$id_temp1] = rrd::def("var$KEY", $VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
	    }

	    # fancy graphing
	    $def[$id_temp1] .= rrd::gradient("var$KEY","#114480", "#1144dc", $label, 10, "50%");
	    $def[$id_temp1] .= rrd::gprint("var$KEY",array("LAST", "MAX", "AVERAGE"),"%5.1lf $unit");

	    # insert extra vertical space if we have thresholds
	    if ($VAL['WARN'] != "" || $VAL['CRIT'] != "") {
		$def[$id_temp1] .= "COMMENT:\\s ";
	    }

	    # warning threshold
	    if ($VAL['WARN'] != "") {
		$warnThresh = $VAL['WARN'];
		$def[$id_temp1] .= rrd::cdef("warn$KEY", "var$KEY,$warnThresh,GT,var$KEY,UNKN,IF");
		$def[$id_temp1] .= rrd::gradient("warn$KEY","#c4c400", "#ffff00","Above Upper Warning Threshold\: $warnThresh $unit\\l", 10, "50%");
	    }
	
	    # critical threshold
	    if ($VAL['CRIT'] != "") {
		$critThresh = $VAL['CRIT'];
		$def[$id_temp1] .= rrd::cdef("crit$KEY", "var$KEY,$critThresh,GT,var$KEY,UNKN,IF");
		$def[$id_temp1] .= rrd::gradient("crit$KEY","#800000", "#dc0000","Above Upper Critical Threshold\: $critThresh $unit\\l", 10, "50%");
	    }
	}

	# TEMPERATURES
	if (preg_match('/^T/', $label) && !preg_match('/Ambient/', $label)) {

	    # Temperature unit and vertical label
	    list ($unit, $vlabel) = tempunit($VAL['UNIT']);

	    # Long label
	    $label = preg_replace('/^T(\d+)_(.+)/', '$2', $label);
	    $label = preg_replace('/_/', ' ', $label);

	    # Short label
	    $label = preg_replace('/^T(\d+)$/', 'Probe $1', $label);

	    $ds_name[$id_temp2] = "Temperatures";

	    $opt[$id_temp2] = "--slope-mode --vertical-label \"$vlabel\" --title \"$def_title: Chassis Temperatures\" ";
	    if (isset($def[$id_temp2])) {
		$def[$id_temp2] .= rrd::def("var$KEY", $VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
	    }
	    else {
		$def[$id_temp2] = rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
	    }
	    $def[$id_temp2] .= rrd::line1("var$KEY", "#".$colors[$t++], rrd::cut($label,20) );
	    $def[$id_temp2] .= rrd::gprint("var$KEY", array("LAST", "MAX", "AVERAGE"), "%4.1lf $unit");
	}

	# WATTAGE PROBE
	if (preg_match('/^W/', $label)) {

	    # Long label
	    $label = preg_replace('/^W(\d+)_(.+)/', '$2', $label);
	    $label = preg_replace('/_/', ' ', $label);

	    # Short label
	    $label = preg_replace('/^W(\d+)$/', 'Probe $1', $label);

	    $ds_name[$id_watt] = "Power Consumption";
	    $vlabel = "Watt";

	    $title = $ds_name[$id_watt];

	    $opt[$id_watt] = "--slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";

	    if(isset($def[$id_watt])){
		$def[$id_watt] .= rrd::def("var$KEY",$VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
	    }
	    else {
		$def[$id_watt] = rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE") ;
	    }

	    # calculate kWh and BTU
            $def[$id_watt] .= "VDEF:tot$KEY=var$KEY,TOTAL ";
	    # rrd:vdef is broken in pnp4nagios 0.6.13 # $def[$id_watt] .= rrd::vdef("tot$KEY","var$KEY,TOTAL");
	    $def[$id_watt] .= rrd::cdef("kwh$KEY","var$KEY,POP,tot$KEY,1000,/,60,/,60,/");
	    $def[$id_watt] .= rrd::cdef("btu$KEY","kwh$KEY,3412.3,*");

	    # fancy graphing
	    $def[$id_watt] .= rrd::gradient("var$KEY","#800000","#dc0000","$label", 10, "50%");

	    # print avg, max and min
	    $def[$id_watt] .= rrd::gprint("var$KEY", array("LAST", "MAX", "AVERAGE"),"%6.0lf W");

	    # print kWh and BTU for time period
	    $def[$id_watt] .= rrd::comment("\\s");
	    $def[$id_watt] .= rrd::comment("    Total power used in time period\:");
	    $def[$id_watt] .= rrd::gprint("kwh$KEY","AVERAGE","%10.2lf kWh\l");
	    $def[$id_watt] .= rrd::comment("                                    ");
	    $def[$id_watt] .= rrd::gprint("btu$KEY","AVERAGE","%10.2lf BTU\l");
	}

	# AMPERAGE PROBE
	if (preg_match('/^A/', $label)) {

	    $first = 0;
	    if ($visited_amp == 0) {
		$first = 1;
		$visited_amp = 1;
	    }

	    # Long label
	    $label = preg_replace('/^A(\d+)_(.+)/', '$2', $label);
	    $label = preg_replace('/_/', ' ', $label);

	    # Short label
	    $label = preg_replace('/^A(\d+)$/', 'Probe $1', $label);

	    $ds_name[$id_amp] = "Amperage Probes";
	    $vlabel = "Ampere";

	    $title = $ds_name[$id_amp];

	    $opt[$id_amp] = "-X0 --lower-limit 0 --slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";
	    if(isset($def[$id_amp])){
		$def[$id_amp] .= rrd::def("var$KEY",$VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	    }
	    else {
		$def[$id_amp]  = rrd::def("var$KEY",$VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
	    }

	    $space = strlen($label) < 16 ? str_repeat(' ', 16 - strlen($label)) : ' ';

	    $def[$id_amp] .= rrd::cdef("tier$KEY", "var$KEY,10,/");
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."b7::STACK ";
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."bf::STACK ";
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."c7::STACK ";
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."cf::STACK ";
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."d7::STACK ";
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."df::STACK ";
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."e7::STACK ";
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."ef::STACK ";
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."f7::STACK ";
	    $def[$id_amp] .= "AREA:tier$KEY#".$colors[$a]."ff:\"$label$space\":STACK ";
	    $a++;

	    if ($first) {
		$def[$id_amp] .= rrd::cdef("sum$KEY", "var$KEY,0,+");
	    }
	    else {
		$def[$id_amp] .= rrd::cdef("sum$KEY", "sum".($KEY-1).",var$KEY,+");
	    }
	    $def[$id_amp] .= rrd::line1("sum$KEY", "#555555");
	    $def[$id_amp] .= rrd::gprint("var$KEY", array("LAST", "MAX", "AVERAGE"),"%6.1lf A last") ;
	}

	# VOLTAGE PROBE
	if (preg_match('/^V/', $label)) {

	    # Long label
	    $label = preg_replace('/^V(\d+)_(.+)/', '$2', $label);
	    $label = preg_replace('/_/', ' ', $label);

	    # Short label
	    $label = preg_replace('/^V(\d+)$/', 'Probe $1', $label);
		
	    $ds_name[$id_volt] = "Voltage Probes";
	    $vlabel = "Volts";

	    $title = $ds_name[$id_volt];

	    $opt[$id_volt] = "--slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";
	    if(isset($def[$id_volt])){
		$def[$id_volt] .= rrd::def("var$KEY", $VAL['RRDFILE'], $VAL['DS'],"AVERAGE");
	    }
	    else {
		$def[$id_volt] = rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'],"AVERAGE");
	    }
	    $def[$id_volt] .= rrd::line1("var$KEY", "#".$colors[$v++], rrd::cut($label,18) ) ;
	    $def[$id_volt] .= rrd::gprint("var$KEY", array("LAST", "MAX", "AVERAGE"),"%8.2lf A");
	}

	# FANS (RPMs)
	if (preg_match('/^F/', $label)) {

	    # Long label
	    $label = preg_replace('/^F(\d+)_(.+)/', '$2', $label);
	    $label = preg_replace('/_/', ' ', $label);

	    # Short label
	    $label = preg_replace('/^F(\d+)$/', 'Probe $1', $label);

	    $ds_name[$id_fan] = "Fan Probes";

	    $opt[$id_fan] = "-X0 --slope-mode --vertical-label \"RPMs\" --title \"$def_title: Fan Speeds\" ";
	    if(isset($def[$id_fan])){
		$def[$id_fan] .= rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'], "AVERAGE") ;
	    }
	    else {
		$def[$id_fan] = rrd::def("var$KEY",$VAL['RRDFILE'],$VAL['DS'], "AVERAGE") ; 
	    }
	    $def[$id_fan] .= rrd::line1("var$KEY", "#".$colors[$f++], rrd::cut($label,18) ) ;
	    $def[$id_fan] .= rrd::gprint("var$KEY", array("LAST", "MAX", "AVERAGE"), "%6.0lf RPM");
	}
	
	# ENCLOSURE TEMPERATURES (Celsius)
	if (preg_match('/^E(?P<encl>.+?)_t(emp_)?(?P<probe>\d+)/', $label, $matches)) {

	    $this_id     = $matches['encl'];
	    $probe_index = $matches['probe'];

	    if ($enclosure_id != $this_id) {
		$e = 0;
		$id_enc++;
		$enclosure_id = $this_id;
	    }

	    # Label
	    $label = "Probe $probe_index";

	    $ds_name[$id_enc] = "Enclosure $enclosure_id Temperatures";

	    $opt[$id_enc] = "--slope-mode --vertical-label \"Celsius\" --title \"$def_title: Enclosure $enclosure_id Temperatures\" ";

	    if(isset($def[$id_enc])){
		$def[$id_enc] .= rrd::def("var$KEY", $VAL['RRDFILE'],$VAL['DS'], "AVERAGE") ;
	    }
	    else {
		$def[$id_enc] = rrd::def("var$KEY", $VAL['RRDFILE'],$VAL['DS'], "AVERAGE") ;
	    }
	    $def[$id_enc] .= rrd::line1("var$KEY", "#".$colors[$e++], rrd::cut($label, 14) );
	    $def[$id_enc] .= rrd::gprint("var$KEY", array("LAST", "MAX", "AVERAGE"),"%6.1lf C");
	}
    }
}

?>
