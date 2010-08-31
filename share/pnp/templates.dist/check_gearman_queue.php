<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# check_gearman.pl --mode=queue --queue=<queuename> 
#
$opt[1] = "-l0 --title \"Gearman Queue Statistics\" ";
#
$ds_name[1] = "Queue Statistics";
$def[1]  = rrd::def("var3", $RRDFILE[3], $DS[3], "AVERAGE") ;
$def[1] .= rrd::gradient("var3", "ffffff", "#33cccc", rrd::cut($NAME[3],10));
$def[1] .= rrd::line1("var3","#339999");
$def[1] .= rrd::gprint("var3", array('LAST', 'MAX', 'AVERAGE'), "%6.2lf$UNIT[1]") ;

$def[1] .= rrd::def("var2", $RRDFILE[2], $DS[2], "AVERAGE") ;
$def[1] .= rrd::line1("var2", "#FF0000", rrd::cut($NAME[2],10));
$def[1] .= rrd::gprint("var2", array('LAST', 'MAX', 'AVERAGE'), "%6.2lf$UNIT[1]") ;

$def[1] .= rrd::def("var1", $RRDFILE[1], $DS[1], "AVERAGE") ;
$def[1] .= rrd::line1("var1", "#FF00FF", rrd::cut($NAME[1],10));
$def[1] .= rrd::gprint("var1", array('LAST', 'MAX', 'AVERAGE'), "%6.2lf$UNIT[1]") ;

