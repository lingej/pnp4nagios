<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_hpasm
# http://labs.consol.de/lang/en/nagios/check_hpasm/
#
$colors=array("CC3300","CC3333","CC3366","CC3399","CC33CC","CC33FF","336600","336633","336666","336699","3366CC","3366FF","33CC33","33CC66" );
$max_rpm=5400;
$col_f=0;
$col_t=0;

foreach($DS as $KEY => $VAL){
    if(preg_match('/^fan_/',$NAME[$KEY])){
        $ds_name[1] = "Fan Speed";
        $opt[1] = "-X0 --slope-mode -u $max_rpm --vertical-label \"RPMs\"  --title \"HPASM Fan Speed\" ";
	    if(!isset($def[1])){
			$def[1] = "";
		}
        $def[1] .= "DEF:ovar$KEY=$RRDFILE[$KEY]:$DS[$KEY]:AVERAGE " ;
        $def[1] .= "CDEF:var$KEY=ovar$KEY,100,/,$max_rpm,* " ;
        $def[1] .= "LINE:var$KEY#".$colors[$col_f].":\"$NAME[$KEY]\" " ;
        $def[1] .= "GPRINT:var$KEY:LAST:\"%6.0lf RPM LAST \" ";
        $def[1] .= "GPRINT:var$KEY:MAX:\"%6.0lf RPM MAX \" ";
        $def[1] .= "GPRINT:var$KEY:AVERAGE:\"%6.2lf RPM AVERAGE \\n\" ";
	$col_f++;
    }

    if(preg_match('/^temp_/',$NAME[$KEY])){
        $ds_name[2] = "Temperature";
        $opt[2] = "--slope-mode --vertical-label \"Grad Celsius\"  --title \"HPASM Temperature\" ";
	    if(!isset($def[2])){
			$def[2] = "";
		}
        $def[2] .= "DEF:var$KEY=$RRDFILE[$KEY]:$DS[$KEY]:AVERAGE " ;
        $def[2] .= "LINE:var$KEY#".$colors[$col_t].":\"$NAME[$KEY]\\t\" " ;
        $def[2] .= "GPRINT:var$KEY:LAST:\"%6.0lf $UNIT[$KEY] LAST \" ";
        $def[2] .= "GPRINT:var$KEY:MAX:\"%6.0lf $UNIT[$KEY] MAX \" ";
        $def[2] .= "GPRINT:var$KEY:AVERAGE:\"%6.2lf $UNIT[$KEY] AVERAGE \\n\" ";
	$col_t++;
    }
}
?>
