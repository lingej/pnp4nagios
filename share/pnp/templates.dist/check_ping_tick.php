<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_icmp [Multigraph]
#
# RTA
#
$ds_name[1] = "Round Trip Times";
$opt[1]  = "--lower-limit=0 --vertical-label \"RTA\" --title \"Ping times\" ";
$opt[1] .=  rrd::darkteint();
$def[1]  =  rrd::def("var1", $RRDFILE[1], $DS[1], "AVERAGE") ;
$def[1] .=  rrd::ticker("var1", $WARN[1], $CRIT[1]) ;
$def[1] .=  rrd::line1("var1", "#0000ff", "Round Trip Times") ;
$def[1] .=  rrd::gprint("var1", array("LAST", "MAX", "AVERAGE"), "%6.2lf $UNIT[1]") ;

if($WARN[1] != ""){
	if($UNIT[1] == "%%"){ $UNIT[1] = "%"; };
  	$def[1] .= rrd::hrule($WARN[1], "#FFFF00", "Warning  ".$WARN[1].$UNIT[1]."\\n");
}
if($CRIT[1] != ""){
	if($UNIT[1] == "%%"){ $UNIT[1] = "%"; };
  	$def[1] .= rrd::hrule($CRIT[1], "#FF0000", "Critical  ".$CRIT[1].$UNIT[1]."\\n");
}
#
# Packets Lost
$ds_name[2] = "Packets Lost";
$opt[2] = "--vertical-label \"Packets lost\" -l0 -u105 --title \"Packets lost\" ";

$opt[2] .=  rrd::darkteint();
$def[2]  =  rrd::def("var1", $RRDFILE[2], $DS[2], "AVERAGE");
$def[2] .=  rrd::gradient("var1", "ff5c00", "ffdc00", "Packets Lost", 20) ;
$def[2] .=  rrd::ticker("var1", $WARN[2], $CRIT[2]) ;
$def[2] .=  rrd::gprint("var1", array("LAST", "MAX", "AVERAGE"), "%3.0lf $UNIT[2]") ;
$def[2] .=  rrd::line1("var1", "#000000") ;

$def[2] .= rrd::hrule("100", "#0000ff") ;

if($WARN[2] != ""){
	if($UNIT[2] == "%%"){ $UNIT[2] = "%"; };
  	$def[2] .= rrd::hrule($WARN[2], "#FFFF00", "Warning  ".$WARN[2].$UNIT[2]."\\n");
}
if($CRIT[2] != ""){
	if($UNIT[2] == "%%"){ $UNIT[2] = "%"; };
  	$def[2] .= rrd::hrule($CRIT[2], "#FF0000", "Critical  ".$CRIT[2].$UNIT[2]."\\n");
}

?>
