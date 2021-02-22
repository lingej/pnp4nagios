#!/usr/bin/perl
# Copyright (c) 2005-2011 PNP4Nagios Developer Team (http://www.pnp4nagios.org)
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
#
use strict;
use warnings;
use Data::Dumper;
use Getopt::Long;
use File::Find;
use File::Glob;
use Term::ANSIColor;

my $version = 'pnp4nagios-head';

# process command line parameters
use vars qw ( $help $debug $mode $vInfo $PNPCfg $MainCfg $last_check $object);
my $start_options = $0 . " " . join(" ", @ARGV);
Getopt::Long::Configure('bundling');
GetOptions(
	"h|help"     => \$help,
	"d|debug"    => \$debug,
	"m|mode=s"   => \$mode,
	"c|config=s" => \$MainCfg,
	"p|pnpcfg=s" => \$PNPCfg,
	"o|object=s" => \$object,
);

my @modes    = ("bulk", "bulk+npcd", "sync", "npcdmod");
my @products = ("nagios", "icinga");
my @states   = ("OK", "WARN", "CRIT", "UNKN", "INFO", "HINT", "DBG");
my @colors   = ("bold green", "bold yellow", "bold red", "bold blue", "bold blue", "bold yellow", "black on_red");
my %process_perf_data_stats = ('hosts' => 0, 'services' => 0, 'noperf' => 0, 'noperf_but_enabled' => 0 , 0 => 0, 1 => 0);
my %stats = ( 0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 =>0 );
my %sizing = (
	50     => 'sync', 
	200    => 'bulk',
	5000   => 'bulk+npcd',
	10000  => 'npcdmod',
);

if ( ! $MainCfg ){
	usage();
	usage_no_config();
	exit;
}

if ( ! $mode ){
	usage();
	usage_no_mode();
	exit;
}

if ( ! $PNPCfg ){
	usage();
	usage_no_pnpcfg();
	exit;
}

if( ! in_array(\@modes, $mode)){
	usage();
	info("'$mode' is not a valid option",2);
	info("Valid modes are [@modes]",2);
	exit;
}

my %statistics = (
	'OK'   => 0,
	'WARN' => 0,
	'CRIT' => 0,
);


my %cfg      = ();
my %commands = ();
my $uid      = 0;
my $gid      = 0;
my $process_perfdata_cfg = 0;

#
# Begin
#

info("========== Starting Environment Checks ============",4);
info("My version is: ".$version,4);
info("Start Options: ".$start_options, 4);

#
# Read Main config file
# 
process_nagios_cfg();
#
# get the product name
#
my $product = get_product();
if( $product eq 0 ){
	info("Can´t determine product while reading $MainCfg", 4);
	info_and_exit("$MainCfg does not look like a valid config file", 2);
}else{
	info("Running product is '$product'", 0);
}

#
# Read objects cache file to get more information
# Needs a running product
#
check_config_var('object_cache_file', 'exists', 'break');
if( -r $cfg{'object_cache_file'} ){ 
	process_objects_file($cfg{'object_cache_file'});
}else{
	info_and_exit($cfg{'object_cache_file'}. " is not readable", 2);
}

#
# Read resource.cfg
#
check_config_var('resource_file', 'exists', 'break');
if( -r $cfg{'resource_file'} ){
	process_npcd_cfg($cfg{'resource_file'});
}else{
	info_and_exit($cfg{'resource_file'}. " is not readable", 2);
}

#
# Read process_perfdata.cfg
#
if ( ! -d $PNPCfg ){
	info_and_exit("Directory $PNPCfg does not exist",2);
}
if ( ! -d "$PNPCfg/check_commands" ){
	info("Directory $PNPCfg/check_commands does not exist",2);
	info_and_exit("$PNPCfg does not look like a PNP4Nagios config directory",2);
}
my $ppcfg = "$PNPCfg/process_perfdata.cfg";
process_perfdata_cfg($ppcfg);

#
# Read etc/pnp_release file if exists
#
if( -r "$PNPCfg/pnp4nagios_release" ){ 
	process_pnp4nagios_release("$PNPCfg/pnp4nagios_release");
	info("Found PNP4Nagios version ".get_config_var('PKG_VERSION'), 0);
	info("./configure Options ".get_config_var('CONFIGURE_ARGS'), 0) if get_config_var('CONFIGURE_ARGS');
}else{
	info("No pnp4nagios_release file found. This might be an older version of PNP4Nagios", 0);
}

#
# Start Main config checks
#

if(config_var_exists($product.'_user') ){
	my $user = get_config_var($product.'_user');
	$uid  = getpwnam($user);
	info( "Effective User is '$user'", 0);
	if($uid){
		info("User $user exists with ID '$uid'", 0 );
	}else{
		info_and_exit("User $user does not exist", 2 );
	}
}else{
	info_and_exit("Option '".$product."_user' not found in $MainCfg", 2);
}

if(config_var_exists($product.'_group') ){
	my $group = get_config_var($product.'_group');
	$gid  = getgrnam($group);
	info( "Effective group is '$group'", 0);
	if($gid){
		info("Group $group exists with ID '$gid'", 0 );
	}else{
		info_and_exit("Group $group does not exist", 2 );
	}
}else{
	info_and_exit("Option '".$product."_group' not found in $MainCfg", 2);
}

#
# Start sync config checks
#

if($mode eq "sync"){
	info("========== Checking Sync Mode Config  ============",4);

	compare_config_var('process_performance_data',  '1');
	compare_config_var('enable_environment_macros', '1');

	check_config_var('service_perfdata_command', 'exists');
	check_config_var('host_perfdata_command', 'exists');;
	last_info("Needed config options are missing.",5,$last_check);

	# Options not allowed in sync mode
	check_config_var('service_perfdata_file', 'notexists');
	check_config_var('service_perfdata_file_template', 'notexists');
	check_config_var('service_perfdata_file_mode', 'notexists');
	check_config_var('service_perfdata_file_processing_interval', 'notexists');
	check_config_var('service_perfdata_file_processing_command', 'notexists',);
	check_config_var('host_perfdata_file', 'notexists');
	check_config_var('host_perfdata_file_template', 'notexists');
	check_config_var('host_perfdata_file_mode', 'notexists');
	check_config_var('host_perfdata_file_processing_interval', 'notexists');
	check_config_var('host_perfdata_file_processing_command', 'notexists');
	check_config_var('broker_module', 'notexists');
	last_info("Config options are not allowed in sync mode. http://docs.pnp4nagios.org",5,$last_check);

	info(ucfirst($product)." config looks good so far",4);
	info("========== Checking config values ============",4);
	my $command_line;

	$command_line = check_command_definition('service_perfdata_command');
	check_process_perfdata_pl($command_line);

	$command_line = check_command_definition('host_perfdata_command');
	check_process_perfdata_pl($command_line);

}

if($mode eq "bulk"){
	info("========== Checking Bulk Mode Config  ============",4);
	
	compare_config_var('process_performance_data', '1');
	check_config_var('service_perfdata_file', 'exists');
	check_config_var('service_perfdata_file_template', 'exists');
	check_perfdata_file_template(get_config_var('service_perfdata_file_template'));
	check_config_var('service_perfdata_file_mode', 'exists');
	check_config_var('service_perfdata_file_processing_interval', 'exists');
	check_config_var('service_perfdata_file_processing_command', 'exists');

	check_config_var('host_perfdata_file', 'exists');
	check_config_var('host_perfdata_file_template', 'exists');
	check_perfdata_file_template(get_config_var('host_perfdata_file_template'));
	check_config_var('host_perfdata_file_mode', 'exists');
	check_config_var('host_perfdata_file_processing_interval', 'exists');
	check_config_var('host_perfdata_file_processing_command', 'exists');
	last_info("Needed config options are missing.",5,$last_check);

	# Options not allowed in bulk mode
	check_config_var('service_perfdata_command', 'notexists');
	check_config_var('host_perfdata_command', 'notexists');
	check_config_var('broker_module', 'notexists');
	last_info("Config options are not allowed in bulk mode",5,$last_check);

	info(ucfirst($product)." config looks good so far",4);
	info("========== Checking config values ============",4);
	my $command_line;

	$command_line = check_command_definition('service_perfdata_file_processing_command');
	check_process_perfdata_pl($command_line);

	$command_line = check_command_definition('host_perfdata_file_processing_command');
	check_process_perfdata_pl($command_line);
}

if($mode eq "bulk+npcd"){
	info("========== Checking Bulk Mode + NPCD Config  ============",4);
	
	compare_config_var('process_performance_data', '1');
	check_config_var('service_perfdata_file', 'exists');
	check_config_var('service_perfdata_file_template', 'exists');
	check_perfdata_file_template(get_config_var('service_perfdata_file_template'));
	check_config_var('service_perfdata_file_mode', 'exists');
	check_config_var('service_perfdata_file_processing_interval', 'exists');
	check_config_var('service_perfdata_file_processing_command', 'exists');

	check_config_var('host_perfdata_file', 'exists');
	check_config_var('host_perfdata_file_template', 'exists');
	check_perfdata_file_template(get_config_var('host_perfdata_file_template'));
	check_config_var('host_perfdata_file_mode', 'exists');
	check_config_var('host_perfdata_file_processing_interval', 'exists');
	check_config_var('host_perfdata_file_processing_command', 'exists');
	last_info("Needed config options are missing. http://docs.pnp4nagios.org",5,$last_check);

	# Options not allowed in bulk mode
	check_config_var('service_perfdata_command', 'notexists');
	check_config_var('host_perfdata_command', 'notexists');
	check_config_var('broker_module', 'notexists');
	last_info("Config options are not allowed in bulk mode with npcd",5,$last_check);

	info(ucfirst($product)." config looks good so far",4);
	info("========== Checking config values ============",4);
	my $command_line;

	my $npcd_cfg = check_proc_npcd(get_config_var($product.'_user'));
	
	if( -r $npcd_cfg){
		info("$npcd_cfg is used by npcd and readable",0);
	}else{
		info_and_exit("$npcd_cfg is not readable",0);
	}
	# read npcd.cfg into %cfg
	process_npcd_cfg($npcd_cfg);
	
	check_config_var('perfdata_spool_dir', 'exists');
	count_spool_files(get_config_var('perfdata_spool_dir'));
	
	$command_line = check_command_definition('service_perfdata_file_processing_command');
	$command_line = check_command_definition('host_perfdata_file_processing_command');

	check_process_perfdata_pl($cfg{'perfdata_file_run_cmd'});
}

if($mode eq "npcdmod"){
	my $val;

	info("========== Checking npcdmod Mode Config  ============",4);

	compare_config_var('process_performance_data', '1');
	last_info         ("Needed config options are missing. http://docs.pnp4nagios.org",5,$last_check);
	
	# Options not allowed in sync mode
	check_config_var('service_perfdata_file', 'notexists');
	check_config_var('service_perfdata_file_template', 'notexists');
	check_config_var('service_perfdata_file_mode', 'notexists');
	check_config_var('service_perfdata_file_processing_interval', 'notexists');
	check_config_var('service_perfdata_file_processing_command', 'notexists');
	check_config_var('host_perfdata_file', 'notexists');
	check_config_var('host_perfdata_file_template', 'notexists');
	check_config_var('host_perfdata_file_mode', 'notexists');
	check_config_var('host_perfdata_file_processing_interval', 'notexists');
	check_config_var('host_perfdata_file_processing_command', 'notexists');
	last_info("Config options are not allowed in bulk mode with npcd",5,$last_check);

	# event_broker_option must have enabled bits 2 and 3 (0b01100)
	check_config_var  ('event_broker_options', 'exists');
	$val = get_config_var('event_broker_options') & 0x0c;
	if($val == 12){
		info("event_broker_option bits 2 and 3 enabled ($val)",0);
	}else{
		info_and_exit("event_broker_option bits 2 and/or 3 not enabled",2);
	}

	check_config_var('broker_module', 'exists', 'break');

	$val = get_config_var('broker_module');
	# extract npcd.cfg patch from broker_module definition 
	my $npcdmod_npcd_cfg;
	$val =~ /npcdmod\.o\s+config_file=(.*)$/;
	if($1){
		$npcdmod_npcd_cfg=$1;
		info("npcdmod.o config file is $npcdmod_npcd_cfg",0);
		if( -r $npcdmod_npcd_cfg){
			info("$npcdmod_npcd_cfg used by npcdmod.o is readable",0);
		}else{
			info_and_exit("$npcdmod_npcd_cfg used by npcdmod.o is not readable",2);
		}
	}else{
		info("broker_module definition looks suspect '$val'",2);
		info_and_exit("Can´t extract path to npcd.cfg from your broker_module definition",2);
	}		
	# extract npcd.cfg path from process list
	my $npcd_cfg = check_proc_npcd(get_config_var($product.'_user'));
	if( -r $npcd_cfg){
		info("$npcd_cfg is used by npcd and readable",0);
	}
	if($npcd_cfg eq $npcdmod_npcd_cfg){
		info("npcd and npcdmod.o are using the same config file ($npcd_cfg)",0);
	}else{
		info_and_exit("npcd and npcdmod.o are not using the same config file($npcd_cfg<=>$npcdmod_npcd_cfg)",2);
	}

	# read npcd.cfg into %cfg
	process_npcd_cfg($npcd_cfg);
	check_config_var('perfdata_spool_dir', 'exists');
	count_spool_files(get_config_var('perfdata_spool_dir'));

	info(ucfirst($product)." config looks good so far",4);
	info("========== Checking config values ============",4);

	# read npcd.cfg into %cfg
	process_npcd_cfg($npcd_cfg);
	check_process_perfdata_pl($cfg{'perfdata_file_run_cmd'});

}
	
info("========== Starting global checks ============",4);
check_config_var('status_file', 'exists', 'break');
process_status_file();
info("==== Starting rrdtool checks ====",4);
check_rrdtool();

info("==== Starting directory checks ====",4);
check_config_var('RRDPATH', 'exists', 'break');
check_perfdata_dir(get_config_var('RRDPATH'));

if($process_perf_data_stats{1} == 0){
	info("'process_perf_data 1' is not set for any of your hosts/services",2);
} 
if($process_perf_data_stats{'noperf'} > 0){
	info($process_perf_data_stats{'noperf'}." hosts/services are not providing performance data",1);
} 
if($process_perf_data_stats{'noperf_but_enabled'} > 0){
	info("'process_perf_data 1' is set for ".$process_perf_data_stats{'noperf_but_enabled'}." hosts/services which are not providing performance data!",1);
} 
if($process_perf_data_stats{0} > 0){
	info("'process_perf_data 0' is set for ".$process_perf_data_stats{0}." of your hosts/services",1);
} 
if($process_perf_data_stats{1} > 0){
	info("'process_perf_data 1' is set for ".$process_perf_data_stats{1}." of your hosts/services",0);
} 

if ( get_config_var('LOG_LEVEL') gt 0 ){
	info("Logging is enabled in process_perfdata.cfg. This will reduce the overall performance of PNP4Nagios",1)
}

info("==== System sizing ====",4);
print_sizing();

info("==== Check statistics ====",4);
print_stats();
exit;

#
# Helper Functions
#

sub config_var_exists {
	my $key = shift;
	if(exists $cfg{$key}){
		return 1;
	}else{
		return 0;
	}
}

sub get_config_var {
	my $key = shift;
	if(exists $cfg{$key}){
		return $cfg{$key};
	}else{
		return undef;
	}
}

sub count_spool_files {
	my $spool_dir = shift;
        my @all_files = glob "$spool_dir/*";
	if($#all_files >= 10){
		info("$#all_files files found in $spool_dir",2);
		info("Something went wrong here!",5);
	}elsif($#all_files >= 3){
		info("$#all_files files found in $spool_dir",1);
		info("Something went wrong here!",5);
	}else{
		info("$#all_files files found in $spool_dir",0);
	}
}
sub check_command_definition {
	my $option = shift;
        warn $option;
	my $key = get_config_var($option);
	my $val = $commands{$key};
	if(exists $commands{$key}){
		info("Command $key is defined",0);
		info("'$val'",0);
	}else{
		info_and_exit("Command $key is not defined",2);
	}
	if($mode eq "sync"){
                if ( $option eq "host_perfdata_command"){
			if( $val =~ m/process_perfdata.pl\s+-d\s+HOSTPERFDATA/ ){
				info ( "Command looks good",0 );
			}else{
				info_and_exit ( "Command looks suspect ($val)",2 );
			}
		}else{
			if( $val =~ m/process_perfdata.pl$/ or $val =~ m/process_perfdata.pl\s+-d\s+SERVICEPERFDATA/ ){
				info ( "Command looks good",0 );
			}else{
				info_and_exit ( "Command looks suspect ($val)",2 );
			}
		}
	}
	if($mode eq "bulk"){
		if( $val =~ m/process_perfdata.pl\s+--bulk=/){
			info ( "Command looks good",0 );
		}else{
			info_and_exit ( "Command looks suspect ($val)",2 );
		}
	}
	if($mode eq "bulk+npcd"){
		my $dump_file = get_config_var( $option =~m/(.*)_processing_command/ );
		my $perfdata_spool_dir = get_config_var( 'perfdata_spool_dir');
		#print "$dump_file\n";
		my $regex = qr/\/bin\/mv\s+$dump_file\s+$perfdata_spool_dir/;
		if( $val =~ m/$regex/){
			info ( "Command looks good",0 );
		}else{
			info ( "Regex = $regex", 4 );
			info_and_exit ( "Command looks suspect ($val)",2 );
		}
	}
	return $commands{$key};
	
}

#
# Max three parameters
# 
sub check_config_var {
	my $key   = shift;
	my $check = shift;
	my $break = shift||0;
	my $var = get_config_var($key);
	if($check eq "exists"){
		if(defined($var)){
			info("$key is defined",0);
			info("$key=$var",0);
			#$last_check = 0;
		}else{
			info("$key is not defined",2);
			$last_check++;
			exit if $break; 
		}
	}
	if($check eq "notexists"){
		if( ! defined($var)){
			#info("$key is not defined",0);
			#$last_check = 0;
		}else{
			info("$key is defined ($key=$var)",2);
			info("$key is not allowed in mode '$mode'",2);
			$last_check++;
			exit if $break; 
		}
	}
}

sub compare_config_var {
	my $key     = shift;
	my $compare = shift;
	my $break   = shift||0;
	my $var     = get_config_var($key);
	if( $var =~ /$compare/){
		info("$key is $var compared with '/$compare/'",0);
	}else{
		info("$key is $var compared with '/$compare/'",2);
		exit if $break;
	}
}

sub check_perfdata_file_template {
	$_ = shift;
	if ( not $_ ){
		return;
	}
		 
	if( /^DATATYPE::(HOST|SERVICE)PERFDATA/ ){
		info("PERFDATA template looks good",0);
	}else{
		info("PERFDATA template looks suspect",2);
	}
}

sub info {
	my $string = shift;
	my $state  = shift||0;
	my $break  = shift||0;

	$stats{$state}++;
	return if $state == 6 and not defined $debug;
	$statistics{$states[$state]}++;
	print color $colors[$state];
	printf("[%-4s]", $states[$state]);
	print color 'reset';
	printf("  %s\n", $string);
}

sub last_info {
	my $string = shift;
	my $state  = shift;
	my $break  = shift||0;
	return if $break == 0;
	info("$string ($break)", $state);
	exit if $break > 0;
}
sub info_and_exit {
	my $string = shift;
	my $state = shift;
	info($string, $state);
	exit $state;
}

sub print_stats {
	my $state = 0;
	$state = 1 if $stats{1} > 0;
	$state = 2 if $stats{2} > 0;
	info(sprintf("Warning: %d, Critical: %d",$stats{1}, $stats{2}),$state);
	info("Checks finished...", $state);
}

sub print_sizing {
	my $object_count = ($process_perf_data_stats{'hosts'} + $process_perf_data_stats{'services'});
	my $graph_count  = ($process_perf_data_stats{'hosts'} + $process_perf_data_stats{'services'});
	info("$object_count hosts/service objects defined",0);
	foreach my $limit ( sort {$a <=> $b} keys %sizing){
		if($graph_count >= $limit and $sizing{$limit} eq $mode){
			info("Use at least mode '".get_mode_by_size($graph_count)."' to reduce I/O",5);
			last;
		} 
	}
}

sub get_mode_by_size {
	my $graph_count = shift;
	foreach my $limit ( sort {$a <=> $b} keys %sizing){
		return $sizing{$limit} if $limit >= $graph_count;
	}
	return 'gearman';	
}

sub check_rrdtool {
	check_config_var('RRDTOOL', 'exists', 'break');
	my $rrdtool = get_config_var('RRDTOOL');
	if ( -x $rrdtool ){
		info("$rrdtool is executable",0);
	}else{
		info_and_exit("$rrdtool is not executable",2);
	}
	my @version = `$rrdtool`;
	chomp $version[0];
	info($version[0],0);
	check_config_var('USE_RRDs', 'exists', 'break');
	if(get_config_var('USE_RRDs')){
		unless ( eval "use RRDs;1" ) {
        		info("Perl RRDs modules are not loadable",1);
    		}else{
        		info("Perl RRDs modules are loadable",0);
		}
	}else{
		unless ( eval "use RRDs;1" ) {
        		info("Perl RRDs modules are neither loadable nor enabled (USE_RRDs = 0)",1);
    		}else{
        		info("RRDs modules are loadable but not enabled (USE_RRDs = 0)",1);
		}
	}	
}

sub check_proc_npcd {
	my $user = shift;
	my $out = `ps -u $user -o cmd | grep /npcd | grep -v grep`;
	my $rc = $?;
	chomp $out;
	info("Check process: 'ps -u $user -o cmd | grep /npcd | grep -v grep'", 6);
	info("Result: $out", 6);
	info("Returncode: $rc", 6);
	#extract npcd.cfg 
	$out =~ /-f\s?(\S+)/;
	my $npcd_cfg = $1;
	if($rc == 0){
		info("npcd daemon is running",0);
	}else{
		info("npcd daemon is not running",2);
		info_and_exit("A running npcd daemon is needed to process data.",4);
	}
	return $npcd_cfg;
}

# process nagios.cfg
sub process_nagios_cfg {
	info ("Reading $MainCfg", 4);
	open (NFILE, "$MainCfg") || info_and_exit("Failed to open '$MainCfg'. $! ", 2);
	while (<NFILE>) {
		process_main_cfg_line();
	}
	close (NFILE);
}

# process process_perfdata.cfg
sub process_perfdata_cfg {
	my $cfg_file = shift;
	if ( -r $cfg_file ){
		if ( process_cfg($cfg_file) ){
			$process_perfdata_cfg = 1;
		}else{
			$process_perfdata_cfg = 0;
		}	 
	}elsif(-e "$PNPCfg/process_perfdata.cfg-sample"){
		info ("$cfg_file does not exist.",1);
		info ("We will try to parse defaults from process_perfdata.pl later on", 1);
		info ("process_perfdata.cfg-sample exists in $PNPCfg", 5);
		info ("It is recommended to rename process_perfdata.cfg-sample to process_perfdata.cfg", 5);
		$process_perfdata_cfg = 0; # we have to parse process_perfdata.pl to get defaults
	}else{
		info ("$cfg_file does not exist.",1);
		info ("We will try to parse defaults from process_perfdata.pl later on", 1);
		info ("It is recommended to place $cfg_file in $PNPCfg", 5);
		info ("A sample file is installed by 'make install-config'", 5);
		$process_perfdata_cfg = 0; # we have to parse process_perfdata.pl to get defaults
	}
}

sub process_pnp4nagios_release {
	my $cfg_file = shift;
	if ( -r $cfg_file ){
		process_cfg($cfg_file);
	}
}

sub process_cfg {
	my $cfg_file = shift;
	if ( -r $cfg_file ){
		info ("Reading $cfg_file", 4);
		
		open (NFILE, "$cfg_file") || info_and_exit("Failed to open '$cfg_file'. $! ", 2);
		while (<NFILE>) {
			process_main_cfg_line();
		}
		close (NFILE);
		return 1;
	}
	return 0;
}

# process npcd.cfg
sub process_npcd_cfg {
	my $cfg_file = shift;
	if ( -r $cfg_file ){
		info ("Reading $cfg_file", 4);
	}else{
		info ("$cfg_file does not exist", 4);
		info ("this file is needed to get more information about your system", 5);
		info_and_exit("no further processing possible",2);
	}
		
	open (NFILE, "$cfg_file") || info_and_exit("Failed to open '$cfg_file'. $! ", 2);
	while (<NFILE>) {
		process_main_cfg_line();
	}
	close (NFILE);
}

# process main config line
sub process_main_cfg_line {
	chomp;
	return if (/^$/);
	return if (/^#/);
	s/#.*//;
	s/\s*$//;
	if (my ($par, $val) = /([^=]+)\s?=\s?(.*)/){
		$par = trim($par);
		$val = trim($val);;
		if ( (defined($par) && $par eq "") ) {
			info ("oddLine -> $_" ,4);
			return;;
		}
		# skip broker_module lines.
		return if (($par eq "broker_module") and ($val !~ /npcdmod.o/));
		info("'$par' -> '$val'",6);
		$cfg{"$par"} = $val;
	}
}

sub trim {
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}

# read object_file
sub process_objects_file {
	my ($file) = @_;
	my $cmd = "";
	my $line = "";
	info ("Reading $file", 4);
	open (CFILE, "$file") || info_and_exit("Failed to open '$file'. $! ", 2);
	while (<CFILE>) {
		s/#.*//;
		next if (/^$/);
		chomp;
		if (/command_name/) {
			($cmd) = /command_name\s*(.*)/;
			next;
		}
		next unless ( /command_line/);
		($line) = /command_line\s*(.*)/ ;
		$commands{"$cmd"} = "$line";
		next unless (/process_perfdata.pl/);
		my @cmd = split (/\s+/,$line);
	}
	close (CFILE);
}

sub process_status_file {
	my ($file) = get_config_var('status_file');
	my $line = "";
	my $perfdata_found = 0;
	my ($host_query,$service_query) = split(/;/,$object) if ($object);
	$host_query    = "" unless (defined $host_query);
	$service_query = "" unless (defined $service_query);
        info("host_query = $host_query",4);
        info("service_query = $service_query",4);
	my $hst  = "";
	my $srv  = "";
	my $perf = "";
	my $ppd  = "";
	info ("Reading $file", 4);
	open (CFILE, "$file") || info_and_exit("Failed to open '$file'. $! ", 2);
	while (<CFILE>) {
		s/#.*//;
		next if (/^$/);
		chomp;
		if(/\shost_name=(.+)/){
			$hst = $1;
			$srv = "";
		}
		if(/\sservice_description=(.+)/){
			$srv = $1;
		}
		if(/\sperformance_data=$/){
			$process_perf_data_stats{'noperf'}++;
			$perfdata_found = 0;
			$perf = "   $hst/$srv: [empty perf data]";
		}
		if(/\sperformance_data=(.+)$/){
			$perfdata_found = 1;
			$perf =  "   $hst/$srv: [$1]";
		}
		# count process_perf_data definitions
                if (/process_performance_data=(\d)$/){
                        $ppd=$1;
                        $process_perf_data_stats{$1}++ ;
                        if ( $perfdata_found == 0 && $1 == 1){
                                $process_perf_data_stats{'noperf_but_enabled'}++;
                        }
                        if ($host_query ne '') {
                            $perf = "" if ($hst !~ /$host_query/i);
                            if ($service_query ne '') {
                                $perf = "" if ($srv !~ /$service_query/i);
                            }else{
                                $perf = '';
                            }
                        }else{
                            $perf='';
                        }
                        if ($perf ne ""){
                             info ("$perf, ppd=$ppd", 4);
                        }
                }		
                if(/^hoststatus /){
			$process_perf_data_stats{'hosts'}++;
		}
		if(/^servicestatus /){
			$process_perf_data_stats{'services'}++;
		}
	}
	close (CFILE);
}

sub check_process_perfdata_pl {
	my $command_line = shift;
	my $path = '';
	if( $command_line =~ /\s?([^\s]*)\/process_perfdata.pl\s?/ ){
		$path = $1;
		if ($path =~ /(\$USER\d+\$)/) {
			if (exists $cfg{"$1"}) {
				my $val = $cfg{"$1"};
				$path =~ s/\$USER\d+\$/$val/;
			}
		}
		if( -x "$path/process_perfdata.pl" ){
			info("Script $path/process_perfdata.pl is executable",0);
		}else{
			info_and_exit("Script $path/process_perfdata.pl is not executable",2);
		}
		process_pp_pl ("$path/process_perfdata.pl") if $process_perfdata_cfg == 0;
	}else{
		info_and_exit("Can´t find path to process_perfdata.pl",2);
	}
}

sub check_perfdata_spool_dir {
	my $dir = shift;
	if( -d $dir ){
		info("Spool directory '$dir' exists",0);
	}else{
		info_and_exit("Spool directory $dir does not exist",2);
	}
	my @files = <$dir/*>;
	my $count = @files;
	if($count > 1){
		info("$count files in $dir", 1);
	}else{
		info("$dir is empty", 0);
	}		
}

# 
sub check_perfdata_dir {
	my $dir =  shift;
	if( -d $dir ){
		info("Perfdata directory '$dir' exists",0);
		find(\&check_perm, "$dir");
	}else{
		info_and_exit("Perfdata directory $dir does not exist",2);
	}
}

sub check_perm {
	-d ;
	my $f = "$File::Find::name";
	return unless (($f =~ /\/$/) or ($f =~ /rrd$|xml$/));
	check_usrgrp ($f);
}

sub check_usrgrp {
	my $file = shift;
	my $break = shift || 0;
	if ($uid) {
		my $fuid = (stat("$file"))[4];
		my $fname = getpwuid($fuid);
		info ("$file: owner is $fname", 2, $break) if ($fuid != $uid);
	}
	if ($gid) {
		my $fgid = (stat("$file"))[5];
		my $fgroup = getgrgid($fgid);
		info ("$file: group is $fgroup", 2, $break) if ($fgid != $gid);
	}
}

# read config inside process_perfdata.pl
sub process_pp_pl {
	my $cfg_file = shift;
	my $loop = 0;
	info ("Reading $cfg_file", 4);
	open (NFILE, "$cfg_file") || info_and_exit("Failed to open '$cfg_file'. $! ", 2);
	while (<NFILE>) {
		chomp;
		last if (/^\s*\);/);
		s/#.*//;
		s/\s*$//;
		s/^\s+//;
		next if (/^$/);
		#$loop++ if (/%conf/);
		#next unless ($loop);
		my ($par, $val) = /([^\s]+)\s+=>\s+([^\s]+)/;    # shortest string
		next unless ((defined $par) and (defined $val));
		$val =~ s/['",]//g;	
		$cfg{"$par"} = $val;
	}
	close (NFILE);
}

sub get_product {
	for my $product (@products){ 
		my $string = $product . "_user"; 
		if ( exists $cfg{$string} ){
			return $product;
		}
	}
	return 0;
}

sub in_array{
	my ($arr,$search_for) = @_;
	my %items = map {$_ => 1} @$arr;
	return (exists($items{$search_for}))?1:0;
} 

sub usage{
print <<EOF;

verify_pnp_config -m|--mode=[sync|bulk|bulk+npcd|npcdmod]
                  -c|--config=[location of nagios.cfg]
                  -p|--pnpcfg=[path to PNP config dir]
                  -o|--object="[host][;service]" (optional)

This script will check certain settings/entries of your PNP environ-
ment to assist you in finding problems when you are using PNP.
It may be used prior and during operation of PNP.

Output starts with a letter with the following meaning:
[INFO] informational message about settings, ...
[OK  ] ok message, will not affect the operation of PNP
[WARN] warning message, might effect the operation of PNP 
[CRIT] error message: PNP will not work without resolving the problem(s)
[HINT] hint: it might be worth reading the appropriate documentation
[DBG ] debugging message, hopefully showing the source of your problem

EOF
}

sub usage_no_config{
	info("-c | --config option not given",2);
	info_and_exit("please specify the path to your nagios or icinga.cfg",2);
}

sub usage_no_pnpcfg{
	info("-p | --pnpcfg option not given",2);
	info_and_exit("please specify the path to your PNP config dir",2);
}

sub usage_no_mode{
	info("-m | --mode option not given",2);
	info_and_exit("Valid options are [@modes]",2);
}
