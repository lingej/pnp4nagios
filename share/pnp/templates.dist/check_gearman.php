<?php
#
# Copyright (c) 2006-2011 Joerg Linge (http://www.pnp4nagios.org)
# Template used for check_gearman whitch is part of mod-gearman
# http://labs.consol.de/nagios/mod-gearman
#
$i=0;
$color['waiting'] = '#F46312';
$color['running'] = '#0354E4';
$color['worker']  = '#00C600';

foreach ($this->DS as $KEY=>$VAL) {
	if(preg_match('/(.*)_([^_].*)$/',$VAL['LABEL'],$matches)){
		$queue = $matches[1];
		$state = $matches[2];
		if($state == "waiting"){
			$i++;
			$opt[$i]='';
			$def[$i]='';
		}
		$opt[$i] = "-l0 --title \"Gearman Queue '$queue'\" ";
		#
		$ds_name[$i] = "$queue";
		$def[$i] .= rrd::def("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
		$def[$i] .= rrd::line1("var$KEY", $color[$state], rrd::cut($state,16));
		$def[$i] .= rrd::gprint("var$KEY", array('LAST', 'MAX', 'AVERAGE'), "%6.2lf".$VAL['UNIT']) ;
	}else{
		$i++;
		$opt[$i] = "-l0 --title \"Gearman Statistics\" ";
		#
		$ds_name[$i] = $VAL['NAME'];
		$def[$i]  = rrd::def("var$KEY", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE") ;
		$def[$i] .= rrd::line1("var$KEY", '#00C600', rrd::cut($VAL['NAME'],16));
		$def[$i] .= rrd::gprint("var$KEY", array('LAST', 'MAX', 'AVERAGE'), "%6.2lf") ;
	}
}
