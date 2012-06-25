<?php
#
# PNP4Nagios template for check_hp_bladechassis
# http://folk.uio.no/trondham/software/check_hp_bladechassis.html
#
# $Id: check_hp_bladechassis.php 16834 2010-03-16 13:55:32Z trondham $
#

# Color for power usage in watts
$PWRcolor = "66FF00";

# Counters
$count = 0;  # general counter

# Title
$def_title = 'HP Blade Enclosure';

# Loop through the performance data
foreach ($DS as $i) {

    # Total Wattage
    if(preg_match('/^total_watt/',$NAME[$i])) {
	$NAME[$i] = 'Total Power Usage';

	++$count;
	$ds_name[$count] = "Total Power Consumption";
	$vlabel = "Watt";

	$title = $ds_name[$count];
 
	$opt[$count] = "--slope-mode --vertical-label \"$vlabel\" --title \"$def_title: $title\" ";
 
        $def[$count] = "DEF:var$i=$RRDFILE[$i]:$DS[$i]:AVERAGE " ;
        $def[$count] .= "AREA:var$i#$PWRcolor:\"$NAME[$i]\" " ;
        $def[$count] .= "LINE:var$i#000000: " ;
	$def[$count] .= "GPRINT:var$i:LAST:\"%6.0lf W last \" ";
	$def[$count] .= "GPRINT:var$i:MAX:\"%6.0lf W max \" ";
	$def[$count] .= "GPRINT:var$i:AVERAGE:\"%6.2lf W avg \\n\" ";
    }

}
?>
