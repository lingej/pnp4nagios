<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# PNP Runtime Informations 
#
# Runtime 
#
$opt[1] = "--vertical-label \"$UNIT[1]\" --title \"Runtime of process_perfdata.pl\" ";
#
#
#
#$s=$this->STRUCT['TIMERANGE']['end'];
$ds_name[1] = "Runtime";
$def[1] =  rrd::def("var1", $RRDFILE[1], $DS[1], "AVERAGE") ;
$def[1] .= rrd::cdef("t_var1","var1,14400,TREND");
if ($WARN[1] != "") {
	$def[1] .= rrd::hrule($WARN[1], "#FFFF00");
}
if ($CRIT[1] != "") {
	$def[1] .= rrd::hrule($CRIT[1], "#FF0000");
}
$def[1] .= rrd::gradient("var1", "ffffff", "#33cccc", rrd::cut("Runtime",10));
$def[1] .= rrd::line1("var1","#339999");
$def[1] .= rrd::gprint("var1", array('LAST', 'MAX', 'AVERAGE'), "%6.2lf$UNIT[1]") ;
$def[1] .= rrd::line1("t_var1", "#ff9999", rrd::cut("4h trend",10));
$def[1] .= rrd::gprint("t_var1", array('LAST', 'MAX', 'AVERAGE'), "%6.2lf$UNIT[1]") ;

#
# Lines processed and RRD errors 
#
$opt[2] = "--vertical-label \"Counter\" --title \"Number of updates\" ";
#
#
#
$ds_name[2] = "Errors and updates";
$def[2] = '';
for($i=2; $i <= sizeof($DS); $i++) {
$def[2] .=  rrd::def("var$i", $RRDFILE[$i], $DS[$i] , "AVERAGE") ;
$def[2] .= rrd::line1("var$i", rrd::color($i), rrd::cut(ucfirst($LABEL[$i]),12) );
$def[2] .= rrd::gprint("var$i", array('LAST', 'MAX', 'AVERAGE'), "%4.0lf$UNIT[$i]");
}
?>
