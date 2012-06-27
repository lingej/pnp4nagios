<?php
#
# Copyright (c) 2006-2012 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_jmx4perl
# Dataset: memory_gc_time
# Perfdata: 'Copy time'=426.511627906977;10000;20000
#
$opt[1]  = "--vertical-label \"s\" --title \"GC Copy Time $hostname / $servicedesc\" ";
$def[1]  = rrd::def("var1a",$RRDFILE[1],$DS[1],"AVERAGE") ;
$def[1] .= rrd::cdef("var1","var1a,1000,/") ;
$def[1] .=rrd::gradient('var1', '#F5F5F5', '#C0C0C0' ); 
$def[1] .= rrd::line1 ("var1", "#8B0000", $LABEL[1]);
$def[1] .= rrd::gprint("var1", array("LAST","MAX","AVERAGE"), "%6.3lf %ss");
$def[1] .= rrd::comment ("jolokia.org\\r");

?>
