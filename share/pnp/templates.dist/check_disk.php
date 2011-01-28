<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
# Template for check_disk
#
# RRDtool Options

foreach ($this->DS as $KEY=>$VAL) {
# set initial values
	$fmt = '%7.3lf';
	$pct = '';
	$upper = "";
	$maximum = "";
	$divis = 1;
	$return = '\n';
	$unit = "B";
	$label = $unit;
	if ($VAL['UNIT'] != "") {
		$unit = $VAL['UNIT'];
		$label = $unit;
		if ($VAL['UNIT'] == "%%") {
			$label = '%';
			$fmt = '%5.1lf';
			$pct = '%';
		}
	}
	if ($VAL['MAX'] != "") {
		# adjust value and unit, details in .../helpers/pnp.php
		$max = pnp::adjust_unit( $VAL['MAX'].$unit,1024,$fmt );
		$upper = "-u $max[1] ";
		$maximum = "of $max[1] $max[2]$pct used";
		$label = $max[2];
		$divis = $max[3];
		$return = '';
	}
	$ds_name[$KEY] = str_replace("_","/",$VAL['NAME']);
	# set graph labels
	$opt[$KEY]     = "--vertical-label $label -l 0 $upper --title \"Filesystem $ds_name[$KEY]\" ";
	# Graph Definitions
	$def[$KEY]     = rrd::def( "var1", $VAL['RRDFILE'], $VAL['DS'], "AVERAGE" ); 
	# "normalize" graph values
	$def[$KEY]    .= rrd::cdef( "v_n","var1,$divis,/");
	$def[$KEY]    .= rrd::area( "v_n", "#c6c6c6",  $ds_name[$KEY] );
	$def[$KEY]    .= rrd::line1( "v_n", "#003300" );
	# show values in legend
	$def[$KEY]    .= rrd::gprint( "v_n", "LAST", "$fmt $label$pct $maximum ");
	$def[$KEY]    .= rrd::gprint( "v_n", "AVERAGE", "$fmt $label$pct avg used $return");
	# create max line and legend
	if ($VAL['MAX'] != "") {
		$def[$KEY] .= rrd::gprint( "v_n", "MAX", "$fmt $label$pct max used \\n" );
		$def[$KEY] .= rrd::hrule( $max[1], "#003300", "Size of FS  $max[0] \\n");
	}
	# create warning line and legend
	if ($VAL['WARN'] != "") {
		$warn = pnp::adjust_unit( $VAL['WARN'].$unit,1024,$fmt );
		$def[$KEY] .= rrd::hrule( $warn[1], "#ffff00", "Warning  on $warn[0] \\n" );
	}
	# create critical line and legend
	if ($VAL['CRIT'] != "") {
		$crit = pnp::adjust_unit( $VAL['CRIT'].$unit,1024,$fmt );
		$def[$KEY] .= rrd::hrule( $crit[1], "#ff0000", "Critical on $crit[0]\\n" );
	}
}
?>
