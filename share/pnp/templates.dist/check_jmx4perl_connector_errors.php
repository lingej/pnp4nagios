<?php
#
# Copyright (c) 2006-2012 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_jmx4perl
# Dataset: connector_errors 
# Perfdata: errors=0;90;100
#
$opt[1]  = "--force-rules-legend -X0 --vertical-label \"Errors\" --title \"Connector Errors $hostname / $servicedesc\" ";
$def[1]  = rrd::def("var1",$RRDFILE[1],$DS[1],"AVERAGE") ;
$def[1] .= rrd::area  ("var1", "#D9D9D9");
$def[1] .= rrd::line1 ("var1", "#B10026", $LABEL[1]);
if ($WARN[1] != "") {
        $def[1] .= "HRULE:$WARN[1]#FFFF00 ";
}
if ($CRIT[1] != "") {
        $def[1] .= "HRULE:$CRIT[1]#FF0000 ";
}
$def[1] .= rrd::gprint("var1", array("LAST","MAX","AVERAGE"), "%.0lf");
$def[1] .= rrd::comment ("jolokia.org\\r");

?>
