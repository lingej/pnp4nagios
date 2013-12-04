#!/usr/bin/perl

use strict;
use warnings;
use Getopt::Long;
use File::Basename;
use POSIX qw(strftime);

my $version       = 0.1;
my $verbose       = 0;
my $show_help     = 0;
my $show_machine  = 0;
my $show_service  = 0;
my $type          = "mysql";
my $db_name       = "ndoutils";
my $user          = "root";
my $password      = "root";
my $host          = "localhost";
my $export_as_pnp = 0;

my $service_table = "nagios_servicechecks";
my $object_table  = "nagios_objects";
my $start_time = 0;
my $end_time = 0;
my $filter_machine = 0;
my $filter_service = 0;

sub usage {
  print "Usage :
  -h  --help            Display this message.
      --version         Display version then exit.
  -v  --verbose         Verbose run.
  -u  --user <NDOUSER>  Log on to database with <NDOUSER> (default $user).
  -p  --pass <PASSWD>   Use <PASSWD> to logon (default $password).
  -t  --type <DBTYPE>   Change database type (default $type).
      --host <DBHOST>   Use <DBHOST> (default $host).
      --dbname <DB>     Use <DB> for ndo database name (default $db_name).
      --list-machine    Display machine definition in ndo database.
      --list-service    Show services defined.
      --export-as-pnp   Export ndo content as a bulk file used by process_perfdata.pl.
      
";
}

GetOptions(
    "version"       => \$version,
    "v"             => \$verbose,
    "verbose"       => \$verbose,
    "h"             => \$show_help,
    "help"          => \$show_help,
    "u=s"           => \$user,
    "user=s"        => \$user,
    "username=s"    => \$user,
    "p=s"           => \$password,
    "pass=s"        => \$password,
    "password=s"    => \$password,
    "type=s"        => \$type,
    "dbname=s"      => \$db_name,
    "host=s"        => \$host,
    "list-machine"  => \$show_machine,
    "list-service"  => \$show_service,
    "export-as-pnp" => \$export_as_pnp,
    "machines=s"    => \$filter_machine,
    "services=s"    => \$filter_service,
);

if($show_help) {
  usage ; exit();
}

use DBI;
my $dbh = 0;

sub connect_db {
  $dbh = DBI->connect("DBI:$type:$db_name;host=$host", $user, $password) || die "Could not connect to database: $DBI::errstr";
}

my $request = 0;

sub show_machine {
  $request = "SELECT name1 FROM $object_table WHERE objecttype_id = 1";
  my $sth = $dbh->prepare($request);
  $sth->execute();
  while(my @result = $sth->fetchrow_array()) {
    print $result[0]."\n";
  }
}

sub show_service {
  $request = "SELECT name1, name2 FROM $object_table WHERE objecttype_id = 2";
  print STDERR "Hostname                       | Service\n";
  print STDERR "-------------------------------+-------------------\n";
  my $sth = $dbh->prepare($request);
  $sth->execute();
  while(my @result = $sth->fetchrow_array()) {
    printf("%-30s | %s\n", $result[0], $result[1]);
  }
}

sub export_as_pnp {

  my $start_time_str;

  my $fh;
  if(open($fh,'time-nagios.txt')){
    $start_time_str = <$fh>;
  }
  close $fh;

  if($start_time_str) {
    $start_time = strftime "%Y-%m-%d %H:%M:%S", localtime(time);
  }else{
    $start_time = strftime "%Y-%m-%d %H:%M:%S", localtime(time-60);
    $start_time_str = $start_time;
  }

  open($fh,'>','time-nagios.txt');
  print $fh $start_time;
  close $fh;

  my %status = (0, "OK", "1", "WARNING", 2, "CRITICAL", 3, "UNKNOWN");
  my %state_type = (0, "SOFT", 1 , "HARD");
  my $request_filter = "1";
  if($start_time_str) {
    $request_filter .= " AND $service_table.start_time > '$start_time_str' ";
  }
  if($end_time) {
    $request_filter .= " AND $service_table.start_time <= '$end_time' ";
  }
  if($filter_machine) {
    my @machines = split(/\s*,\s*/, $filter_machine);
    $request_filter .= " AND $object_table.name1 IN ('".join("','", @machines)."') ";
  }
  if($filter_service) {
    my @services = split(/\s*,\s*/, $filter_service);
    $request_filter .= " AND $object_table.name2 IN ('".join("','", @services)."') ";
  }
  $request = "SELECT
  UNIX_TIMESTAMP(start_time), name1, name2, perfdata, command_line, command_args, output, state, state_type
FROM
  $service_table, $object_table
WHERE
  $service_table.service_object_id = $object_table.object_id AND length(perfdata) > 0 AND $request_filter
ORDER BY
  $service_table.start_time
";
  print "$request\n" if($verbose);
  my $sth = $dbh->prepare($request);
  $sth->execute();
  while(my @r = $sth->fetchrow_array()) {
    basename($r[4]) =~ /^(\w+).*/;
    my $cmd = "$1";
    $cmd .= ($r[5] ? "!".$r[5] : "");
    print "DATATYPE::SERVICEPERFDATA\tTIMET::".$r[0]."\tHOSTNAME::".$r[1]."\tSERVICEDESC::".$r[2]."\tSERVICEPERFDATA::".$r[3].
          "\tSERVICECHECKCOMMAND::$cmd\tHOSTSTATE::OK\tHOSTSTATETYPE::HARD\tSERVICESTATE::".$status{$r[7]}."\tSERVICESTATETYPE::".$state_type{$r[8]}."\n";
  }
}

my $format = "%s";
if($show_machine) {
  connect_db();
  show_machine();
} elsif($show_service) {
  connect_db();
  show_service();
} elsif($export_as_pnp) {
  connect_db();
  export_as_pnp();
} else {
  print "Nothing to do\n";
  exit;
}
