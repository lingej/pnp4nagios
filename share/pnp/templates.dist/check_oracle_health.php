<?php
#
# Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
#

$name = $NAME[1];

if ($name == "connection_time") {
	include 'check_oracle_health_connection-time.php';
}

elseif (preg_match ('/connected|invalid_/',$name)) {
	include 'integer.php';
}

elseif ($name == "pga_in_memory_sort_ratio") {
	include 'check_oracle_health_pga-in-memory-sort-ratio.php';
}

elseif ($name == "redo_io_traffic") {
	include 'check_oracle_health_redo-io-traffic.php';
}

elseif ($name == "switch_interval") {
	include 'check_oracle_health_switch-interval.php';
}

elseif ($name == "tablespace_usage") {
	include 'check_oracle_health_tablespace-usage.php';
}
else {
	include 'default.php';
}
?>
