<?php
#
# Copyright (c) 2006-2012 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_jmx4perl
# Dataset: connector_nr_requests
# Perfdata: nr_requests=152.8;900;1000
#
$opt[1]  = "-X0 --vertical-label \"Requets\" --title \"Requests $hostname / $servicedesc\" ";
$def[1]  = rrd::def("var1",$RRDFILE[1],$DS[1],"AVERAGE") ;
$def[1] .= rrd::gradient('var1', '#F5F5F5', '#C0C0C0' ); 
$def[1] .= rrd::line1 ("var1", "#B80000", $LABEL[1]);
$def[1] .= rrd::gprint("var1", array("LAST","MAX","AVERAGE"), "%.0lf");
if ($WARN[1] != "") {
    $def[1] .= rrd::hrule($WARN[1],"#FFFF00", "Warning\\n");
}
if ($CRIT[1] != "") {
    $def[1] .= rrd::hrule($CRIT[1],"#FF0000", "Critical");
}
$def[1] .= rrd::comment ("jolokia.org");

?>
