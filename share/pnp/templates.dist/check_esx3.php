<?php
/*
License: GPL
Copyright (c) 2009 op5 AB
Author: Mattias Ryrlen <dev@op5.com>
Contributor(s): Joerg Linge <joerg.linge@pnp4nagios.org>

For direct contact with any of the op5 developers send a mail to dev@op5.com
Discussions are directed to the mailing list op5-users@op5.com,
see http://lists.op5.com/mailman/listinfo/op5-users

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Requires:
  pnp4nagios 0.4.14+
  enable_recursive_template_search enabled in configfile
  check_esx3.pl from http://git.op5.org/git/?p=nagios/op5plugins.git;a=summary
*/

$color_list = array(
					1 => "#ff77ee", // Purple
					2 => "#fed409", // Yellow
					3 => "#007dd0", // Blue
					4 => "#ee0a04", // Red
					5 => "#56a901", // Green
					6 => "#ff6600", // Orange
					7 => "#a4a4a4", // Grey
                    8 => "#336633"  // darker green
				);

$opt[1] = '';
$def[1] = '';
$filled = 0;
$base   = "1024";
$cdef   = "";
$vlabel = "";

// Specific settings based on first DataSource, if we want to customize it.
switch ($NAME[1]) {
	case "cpu_usage":
		$vlabel = "Percent";
		$opt[1] .= "--lower-limit=0 --upper-limit=105 ";
		break;
	case "mem_usage":
		$vlabel = "Percent";
		$opt[1] .= "--lower-limit=0 --upper-limit=105 ";
		break;
	case "net_receive":
		$vlabel = "Kb/sec";
		break;
	case "cpu_usagemhz":
		$vlabel = "CPU Usage";
		$filled = 1;
		break;
	default:
		break;
}

$opt[1] .= " --imgformat=PNG --title=\" $hostname / $servicedesc\" --base=$base --vertical-label=\"$vlabel\" --slope-mode ";
$opt[1] .= "--watermark=\"http://www.op5.com template: $TEMPLATE[1]\" ";
$opt[1] .= "--units-exponent=0 ";

for ($i = 1; $i <= count($DS); $i++) {
	$def[1] .= "DEF:ds$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
	$def[1] .= "CDEF:var$i=ds$i ";

	if (isset($color)) {
		$color_list = $color;
	}

	/* If we have few datasources we fill the area below with a semitransparent version of basecolor
	   This makes the graph look more "modern" */
	if ($filled || count($DS) <= 3) {
		$def[1] .= "AREA:var$i". $color_list[$i] . "32 ";
	}
	$def[1] .= "LINE1:var$i" . $color_list[$i] . "FF:\"$NAME[$i]\t\" ";
	$def[1] .= "GPRINT:var$i:LAST:\"Cur\\:%8.2lf $UNIT[$i]\" ";
	$def[1] .= "GPRINT:var$i:AVERAGE:\"Avg\\:%8.2lf $UNIT[$i]\" ";
	$def[1] .= "GPRINT:var$i:MAX:\"Max\\:%8.2lf $UNIT[$i]\\n\" ";
}

for ($i = 1; $i <= count($DS); $i++) {
	if ($UNIT[$i] == "%%") {
		$UNIT[$i] = "%";
	}

	if (isset($WARN[$i]) && $WARN[$i] != "") {
		$def[1] .= "HRULE:$WARN[$i]#FFFF00:\"Warning ($NAME[$i])\: " . $WARN[$i] . " " . $UNIT[$i] . " \\n\" " ;
	}

	if (isset($CRIT[$i]) && $CRIT[$i] != "") {
		$def[1] .= "HRULE:$CRIT[$i]#FF0000:\"Critical ($NAME[$i])\: " . $CRIT[$i] . " " . $UNIT[$i] . " \\n\" " ;
	}
}
?>
