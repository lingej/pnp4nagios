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
# TODO: 
# perfdata spool dir ermitteln aus der npcd.cfg ( bulk+npcd, bulk und npcdmod )
# permissions auf das spool dir
# process_perfdata.cfg auslesen
# wenn keine process_perfdata.cfg vorhanden, process_perfdata.pl parsen!!!
# perfdata dir prüfen auf permission und rrdfiles
#
use strict;
use warnings;
use Data::Dumper;
use Getopt::Long;
use File::Find;
use Switch;
use Term::ANSIColor;

# process command line parameters
use vars qw ( $help $debug $mode $MainCfg $last_check);
Getopt::Long::Configure('bundling');
GetOptions(
    "h|help"     => \$help,
    "d|debug"    => \$debug,
    "m|mode=s"   => \$mode,
    "c|config=s" => \$MainCfg,
);

my @modes    = ("bulk", "bulk+npcd", "sync", "npcdmod");
my @products = ("nagios", "icinga");
my @states   = ("OK", "WARN", "CRIT", "UNKN", "INFO");
my @colors   = ("bold green", "bold yellow", "bold red", "bold blue", "bold blue");
my %process_perf_data_stats = (0 => 0, 1 => 0);

if ( ! $mode ){
	usage();;
	usage_no_mode();
	exit;
}

if ( ! $MainCfg ){
	usage();
	usage_no_config();
	exit;
}

if( ! in_array(\@modes, $mode)){
	usage();
	info("$mode is not valid",2);
	info("Valid modes are [@modes]",2);
	exit;
}
my %statistics = (
	'OK' => 0,
	'WARN' => 0,
	'CRIT' => 0,
);
 
my %cfg;
my %commands;


#
# Begin
#

info("========== Starting Environment Checks ============",4);

#
# Read Main config file
# 
process_nagios_cfg();
#
# get the Product name
#
my $product = get_product();
if( $product eq 0 ){
	info("Can´t determine product while reading $MainCfg", 4);
	info_and_exit("$MainCfg does not look like a valid config file", 2);
}else{
	info("Running Product is '$product'", 0);
}

#
# Read objects cache file to get more informations 
# Needs a running product
#
check_config_var('object_cache_file', 'exists', 'break');

#
# Read objects.cache file
#
if( -r $cfg{'object_cache_file'} ){ 
	process_objects_file($cfg{'object_cache_file'});
}else{
	info_and_exit($cfg{'object_cache_file'}. " is not readable", 2);
}

#
# Start Main config checks
#

if(canfig_var_exists($product.'_user') ){
	my $user = get_config_var($product.'_user');
	my $uid  = getpwnam($user);
	info( "Effective User is '$user'", 0);
	if($uid){
		info("User $user exists with ID '$uid'", 0 );
	}else{
		info_and_exit("User $user does not exist", 2 );
	}
}else{
	info_and_exit("Option '".$product."_user' not found in $MainCfg", 2);
}

if(canfig_var_exists($product.'_group') ){
	my $group = get_config_var($product.'_group');
	my $gid  = getgrnam($group);
	info( "Effective Group is '$group'", 0);
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

	compare_config_var('process_performance_data',  '1', 'break');
	compare_config_var('enable_environment_macros', '1');

	check_config_var('service_perfdata_command', 'exists');
	check_command_definition(get_config_var('service_perfdata_command'));

	check_config_var('host_perfdata_command', 'exists');
	check_command_definition(get_config_var('host_perfdata_command'));

	# Options not allowed in sync mode
	check_config_var('service_perfdata_file', 'notexists','break');
	check_config_var('service_perfdata_file_template', 'notexists','break');
	check_config_var('service_perfdata_file_mode', 'notexists','break');
	check_config_var('service_perfdata_file_processing_interval', 'notexists','break');
	check_config_var('service_perfdata_file_processing_command', 'notexists','break');
	check_config_var('host_perfdata_file', 'notexists','break');
	check_config_var('host_perfdata_file_template', 'notexists','break');
	check_config_var('host_perfdata_file_mode', 'notexists','break');
	check_config_var('host_perfdata_file_processing_interval', 'notexists','break');
	check_config_var('host_perfdata_file_processing_command', 'notexists','break');
	check_config_var('broker_module', 'notexists', 'break');
}

if($mode eq "bulk"){

	info("========== Checking Bulk Mode Config  ============",4);
	
	compare_config_var('process_performance_data', '1', 'break');
	check_config_var('service_perfdata_file', 'exists','break');
	check_config_var('service_perfdata_file_template', 'exists','break');
	check_perfdata_file_template(get_config_var('service_perfdata_file_template'));
	check_config_var('service_perfdata_file_mode', 'exists','break');
	check_config_var('service_perfdata_file_processing_interval', 'exists','break');
	check_config_var('service_perfdata_file_processing_command', 'exists','break');
	check_command_definition(get_config_var('service_perfdata_file_processing_command'));

	check_config_var('host_perfdata_file', 'exists','break');
	check_config_var('host_perfdata_file_template', 'exists','break');
	check_perfdata_file_template(get_config_var('host_perfdata_file_template'));
	check_config_var('host_perfdata_file_mode', 'exists','break');
	check_config_var('host_perfdata_file_processing_interval', 'exists','break');
	check_config_var('host_perfdata_file_processing_command', 'exists','break');
	check_command_definition(get_config_var('host_perfdata_file_processing_command'));
	# Options not allowed in bulk mode
	check_config_var('service_perfdata_command', 'notexists');
	check_config_var('host_perfdata_command', 'notexists');
	check_config_var('broker_module', 'notexists', 'break');
}

if($mode eq "bulk+npcd"){

	info("========== Checking Bulk Mode + NPCD Config  ============",4);
	
	compare_config_var('process_performance_data', '1', 'break');
	check_config_var('service_perfdata_file', 'exists','break');
	check_config_var('service_perfdata_file_template', 'exists','break');
	check_perfdata_file_template(get_config_var('service_perfdata_file_template'));
	check_config_var('service_perfdata_file_mode', 'exists','break');
	check_config_var('service_perfdata_file_processing_interval', 'exists','break');
	check_config_var('service_perfdata_file_processing_command', 'exists','break');
	check_command_definition(get_config_var('service_perfdata_file_processing_command'));

	check_config_var('host_perfdata_file', 'exists','break');
	check_config_var('host_perfdata_file_template', 'exists','break');
	check_perfdata_file_template(get_config_var('host_perfdata_file_template'));
	check_config_var('host_perfdata_file_mode', 'exists','break');
	check_config_var('host_perfdata_file_processing_interval', 'exists','break');
	check_config_var('host_perfdata_file_processing_command', 'exists','break');
	check_command_definition(get_config_var('host_perfdata_file_processing_command'));
	# Options not allowed in bulk mode
	check_config_var('service_perfdata_command', 'notexists');
	check_config_var('host_perfdata_command', 'notexists');
	check_config_var('broker_module', 'notexists', 'break');

	my $npcd_cfg = check_proc_npcd(get_config_var($product.'_user'));
	
	if( -r $npcd_cfg){
		info("$npcd_cfg is used by npcd and readable",0);
	}
	# read npcd.cfg into %cfg
	process_npcd_cfg($npcd_cfg);
	#print Dumper \%cfg;
	if(-x $cfg{'perfdata_file_run_cmd'}){
		info("'".$cfg{'perfdata_file_run_cmd'}."' is executable",0);
	}else{	
		info_and_exit("'".$cfg{'perfdata_file_run_cmd'}."' is not executable",2);
	}
}

if($mode eq "npcdmod"){
	my $val;

	info("========== Checking npcdmod Mode Config  ============",4);

	compare_config_var('process_performance_data', '1', 'break');
	check_config_var('event_broker_options', 'exists');
	# Options not allowed in sync mode
	check_config_var('service_perfdata_file', 'notexists','break');
	check_config_var('service_perfdata_file_template', 'notexists','break');
	check_config_var('service_perfdata_file_mode', 'notexists','break');
	check_config_var('service_perfdata_file_processing_interval', 'notexists','break');
	check_config_var('service_perfdata_file_processing_command', 'notexists','break');
	check_config_var('host_perfdata_file', 'notexists','break');
	check_config_var('host_perfdata_file_template', 'notexists','break');
	check_config_var('host_perfdata_file_mode', 'notexists','break');
	check_config_var('host_perfdata_file_processing_interval', 'notexists','break');
	check_config_var('host_perfdata_file_processing_command', 'notexists','break');

	# event_broker_option must be -1 or >= 12
	$val = get_config_var('event_broker_options');
	if($val == -1 || $val >= 12 ){
		info("event_broker_option inside correct value range ($val)",0);
	}else{
		info_and_exit("event_broker_option not inside a valid range -1 || >= 12 ",2);
	}

	check_config_var('broker_module', 'exists', 'break');

	$val = get_config_var('broker_module');
	# extract npcd.cfg patch out of broker_module definition 
	$val =~ /npcdmod\.o\s+config_file=(.*)$/;
	my $npcdmod_npcd_cfg;
	if($1){
		$npcdmod_npcd_cfg=$1;
		info("npcdmod.o config file is $npcdmod_npcd_cfg",0);
		if( -r $npcdmod_npcd_cfg){
			info("$npcdmod_npcd_cfg used by npcdmod.o is readable",0);
		}
	}else{
		info("can´t extract path to npcd.cfg out of your broker_module definition",2);
	}		
	# extract npcd.cfg path out of process list
	my $npcd_cfg = check_proc_npcd(get_config_var($product.'_user'));
	if( -r $npcd_cfg){
		info("$npcd_cfg is used by npcd and readable",0);
	}
	if($npcd_cfg eq $npcdmod_npcd_cfg){
		info("npcd and npcdmod.o are using the same config file ($npcd_cfg)",0);
	}else{
		info("npcd and npcdmod.o are not using the same config file($npcd_cfg<=>$npcdmod_npcd_cfg)",1);
	}
	# read npcd.cfg into %cfg
	process_npcd_cfg($npcd_cfg);
	#print Dumper \%cfg;
	if(-x $cfg{'perfdata_file_run_cmd'}){
		info("'".$cfg{'perfdata_file_run_cmd'}."' is executable",0);
	}else{	
		info_and_exit("'".$cfg{'perfdata_file_run_cmd'}."' is not executable",2);
	}
}


#global tests
info("========== Starting global checks ============",4);
if($process_perf_data_stats{1} == 0){
	info("process_perf_data 1 is not set for all of your Hosts/Services",2);
} 
if($process_perf_data_stats{0} > 0){
	info("'process_perf_data 0' is set for ".$process_perf_data_stats{0}." of your Hosts/Services",1);
} 
if($process_perf_data_stats{1} > 0){
	info("'process_perf_data 1' is set for ".$process_perf_data_stats{1}." of your Hosts/Services",0);
} 

exit;

sub canfig_var_exists {
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
		return 0;
	}
}

sub check_command_definition {
	my $key = shift;
	my $var = $commands{$key};
	if(exists $commands{$key}){
		info("Command $key is defined ('$var')",0);
	}else{
		info_and_exit("Command $key is not defined",2);
	}
}

#
# Max 3 parameter
# 
sub check_config_var {
	my $key   = shift;
    my $check = shift;
	my $break = shift||0;
	my $var = get_config_var($key);
	if($check eq "exists"){
		if($var){
			info("$key is defined ($key=$var)",0);
			$last_check = 1;
		}else{
			info("$key is not defined in $MainCfg",2);
			$last_check = 0;
			exit if $break; 
		}
	}
	if($check eq "notexists"){
		if( ! $var){
			#info("$key is not defined",0);
			$last_check = 1;
		}else{
			info("$key is defined ($key=$var)",2);
			info("$key is not allowed in mode '$mode'",2);
			$last_check = 0;
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
	if( /^DATATYPE::(HOST|SERVICE)PERFDATA/ ){
		info("PERFDATA Template looks good",0);
	}else{
		info("PERFDATA looks suspect",2);
	}
}
sub info {
	my $string = shift;
	my $state  = shift;
	$statistics{$states[$state]}++;
	print color $colors[$state];
	printf("[%4s]  ", $states[$state]);
	print color 'reset';
	printf("%s\n", $string);
}

sub info_and_exit {
	my $string = shift;
	my $rc = shift;
	info($string, $rc);
	exit $rc;
}

sub check_proc_npcd {
	my $user = shift;
	my $out = `ps -u $user -o cmd | grep npcd | grep -v grep`;
	my $rc = $?;
	chomp $out;
	#extract npcd.cfg 
	$out =~ /-f\s(\S+)$/;
	my $npcd_cfg = $1;
	if($rc == 0){
		info("npcd daemon is running",0);
	}else{
		info("npcd daemon is not running ($rc)",2);
		info_and_exit("we need a running npcd to process data.",4);
	}
	return $npcd_cfg;
}
# process nagios.cfg
sub process_nagios_cfg {
    info ("Reading $MainCfg", 0);
    open (NFILE, "$MainCfg") || info_and_exit("Failed to open '$MainCfg'. $! ", 2);
    while (<NFILE>) {
        process_main_cfg_line();
    }
    close (NFILE);
}

# process npcd.cfg
sub process_npcd_cfg {
	my $cfg_file = shift;
	%cfg = ();
    info ("Reading $cfg_file", 0);
    open (NFILE, "$cfg_file") || info_and_exit("Failed to open '$cfg_file'. $! ", 2);
    while (<NFILE>) {
        process_main_cfg_line();
    }
    close (NFILE);
}

# process main config line
sub process_main_cfg_line {
    chomp;
    s/#.*//;
    s/\s*$//;
    return if (/^$/);
    my ($par, $val) = /^(.*?)\s?=\s?(.*)/;    # shortest string (broker module contains multiple equal signs)
    if (($par eq "") or ($val eq "")) {
        info ("oddLine", "$_");
        next;
    }
    return if (($par eq "broker_module") and ($val !~ /npcdmod.o/));

    $cfg{"$par"} = $val;
}

# read config file
sub process_objects_file {
    my ($file) = @_;
    my $cmd = "";
    my $line = "";
    info ("Reading $file", 0);
    open (CFILE, "$file") || info_and_exit("Failed to open '$file'. $! ", 2);
    while (<CFILE>) {
        s/#.*//;
        next if (/^$/);
        chomp;
		# count process_perf_data definitions
		if (/process_perf_data\s+(\d)$/){
			$process_perf_data_stats{$1}++;
		}
        next unless (/command_[name|line]/);
        if (/command_name/) {
            ($cmd) = /command_name\s*(.*)/;
            next;
        }
        ($line) = /command_line\s*(.*)/ ;
        $commands{"$cmd"} = "$line";
        next unless (/process_perfdata.pl/);
        my @cmd = split (/\s+/,$line);
    }
    close (CFILE);
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

verify_pnp_config -m|--mode=[sync|bulk|npcdmod] -c|--config=[path to nagios.cfg]

This script will check certain settings/entries of your PNP environ-
ment to assist you in finding problems when you are using PNP.
It may be used prior and during operation of PNP.

Output starts with a letter with the following meaning:
[IMFO] informational message about settings, ...
[OK  ] ok message, will not affect the operation of PNP
[WARN] warning message, might be effent the operation of PNP
[CRIT] error message: PNP will not work without resolving the problem(s)
[INFO] hint: it might be worth reading the appropriate documentation
[DBG ] debugging message, hopefully showing the source of your problem

EOF
}

sub usage_no_config{
	info_and_exit("-c | --config option not given",2);
}

sub usage_no_mode{
	info_and_exit("-m | --mode option not given",2);
}
