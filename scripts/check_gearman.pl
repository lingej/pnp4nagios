#!/usr/bin/perl
# nagios: -epn
## check_gearman.pl - PNP4Nagios.
## Copyright (c) 2006-2010 Joerg Linge (http://www.pnp4nagios.org)
##
## This program is free software; you can redistribute it and/or
## modify it under the terms of the GNU General Public License
## as published by the Free Software Foundation; either version 2
## of the License, or (at your option) any later version.
##
## This program is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.
##
## You should have received a copy of the GNU General Public License
## along with this program; if not, write to the Free Software
## Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

use warnings;
use strict;
use IO::Socket;
use Getopt::Long;
use Pod::Usage;
use Data::Dumper;
use Nagios::Plugin;

my $host = "localhost";
my $port = "4730";
my $EOL = "\015\012";

my($opt_verbose, $opt_help, $opt_debug);
my $opt_host = "localhost";
my $opt_port = 4730;
my $opt_queue;
my $opt_mode;
my $opt_warn_jobs = '50';
my $opt_crit_jobs = '150';
my $opt_warn_worker = '2:10';
my $opt_crit_worker = '1:50';

my $np = Nagios::Plugin->new();

Getopt::Long::Configure('no_ignore_case');
GetOptions (
    "h|help" => \$opt_help,
    "v|verbose" => \$opt_verbose,
    "d|debug" => \$opt_debug,
    "H|host=s" => \$opt_host,
    "P|port=i" => \$opt_port,
    "Q|queue=s" => \$opt_queue,
    "M|mode=s" => \$opt_mode,
    "warn_jobs=s" => \$opt_warn_jobs,
    "crit_jobs=s" => \$opt_crit_jobs,
    "warn_worker=s" => \$opt_warn_worker,
    "crit_worker=s" => \$opt_crit_worker,
);

if(defined $opt_help) {
    pod2usage( { -verbose => 1 } );
    exit 3;
}

unless( defined($opt_mode)){
    print "Missing Mode Option [ -M | --mode ]\n\n";
    pod2usage( { -verbose => 1 } );
    exit 3;
}

my $remote = IO::Socket::INET->new(
        Proto => "tcp",
        PeerAddr => $opt_host,
        PeerPort => $opt_port,
    ); # or die "CRITICAL: connect to gearmand on $opt_host:$opt_port failed";

unless ( defined($remote) ){
    print "CRITICAL: connect to gearmand on $opt_host:$opt_port failed\n";
    exit 2;
}

$remote->autoflush(1);

my %queue = ();
my $gearmand_version;

print $remote "status" . $EOL;
while ( my $line = <$remote> ){
    last if $line eq ".\n";
    my ($f, $w, $r, $c) = split(" ", $line);
    $queue{$f} = { 'waiting' => $w, 'running' => $r,'worker' => $c };
    printf("Queue: '%s'\n\twaiting: %s running: %s, workers: %s\n",$f,$w,$r,$c) if defined $opt_verbose;
}

print $remote "version" . $EOL;
while ( $gearmand_version = <$remote> ){
    chomp $gearmand_version;
    last;
}


#print Dumper %queue;
if($opt_mode =~ /queue/i){

    unless( defined($opt_queue)){
        print "Missing queue name [ -Q | --queue ]\n\n";
        pod2usage( { -verbose => 1 } );
        exit 3;
    }

    if(defined($queue{$opt_queue})){
        my $state_txt = "OK";
        my $state_id = 0;
        my $state_info = "";
        my $perfdata = sprintf("waiting=%s;%s;%s;0; worker=%s;%s;%s;0;",
                                $queue{$opt_queue}{'waiting'},
                                $opt_warn_jobs,
                                $opt_crit_jobs,
                                $queue{$opt_queue}{'worker'},
                                $opt_warn_worker,
                                $opt_crit_worker,
                        );
        $state_id = $np->check_threshold(check => $queue{$opt_queue}{'waiting'}, warning => $opt_warn_jobs, critical => $opt_crit_jobs);
        if($state_id == 2){
            $state_txt = "CRITICAL";
            $state_id  = 2;
            $state_info .= sprintf("[Jobs waiting %s (C=>%s)] ",$queue{$opt_queue}{'worker'}, $opt_crit_jobs); 
 
        }elsif($state_id == 1){
            $state_txt = "WARNING";
            $state_id  = 1; 
            $state_info .= sprintf("[Jobs waiting %s (W=>%s)] ",$queue{$opt_queue}{'waiting'}, $opt_warn_jobs); 

        }else{
            $state_txt = "OK";
            $state_id  = 0; 
        }
        $state_id = $np->check_threshold(check => $queue{$opt_queue}{'worker'}, warning => $opt_warn_worker, critical => $opt_crit_worker);
        if($state_id == 2){
            $state_txt = "CRITICAL";
            $state_id  = 2;
            $state_info .= sprintf("[Worker %s (C=>%s)] ",$queue{$opt_queue}{'worker'}, $opt_crit_worker); 
 
        }elsif($state_id == 1 ){
            $state_txt = "WARNING";
            $state_id  = 1; 
            $state_info .= sprintf("[Worker %s (W=>%s)] ",$queue{$opt_queue}{'worker'}, $opt_warn_worker); 

        }else{
            $state_txt = "OK";
            $state_id  = 0; 
        }
        printf( "%s: %s gearmand running version '%s' - Queue: '%s' - Jobs waiting: %s - Worker connected: %s | %s\n",
                $state_txt,
                $state_info,
                $gearmand_version,
                $opt_queue,
                $queue{$opt_queue}{'waiting'},
                $queue{$opt_queue}{'worker'},
                $perfdata,
        );
        exit $state_id;
    }else{
        print "UNKNOWN: Queue $opt_queue not found\n";
        exit 3;
    }
}

if($opt_mode =~ /orphaned_jobs/i){
    my $state_id = 0;
    my $state_txt = "OK";
    my $state_info = "";
    my $count = 0;
    foreach my $d ( keys %queue ) {
        if($queue{$d}{'waiting'} > 0 && $queue{$d}{'worker'} == 0){
            $state_info .= " [$d]";
            $count++;
        }
    }
    my $perfdata = "queues=$count";
    if($count == 0){
        $state_id = 0;
        $state_txt = "OK: No orphaned queues found";
    }
    if($count == 1){
        $state_id = 1;
        $state_txt = sprintf("WARNING: One orphaned queue found.%s", $state_info);
    }
    if($count > 1){
        $state_id = 1;
        $state_txt = sprintf("WARNING: %d orphaned queues found.%s", $count, $state_info);
    }

    printf ( "%s | %s\n", $state_txt, $perfdata);
    exit $state_id;
}

print "UNKNOWN: Mode '$opt_mode' not known\n";
exit 3;


__END__

=pod

=head1 NAME

check_greaman.pl

=head1 OPTIONS 

./check_gearman.pl [ -v ]
[ -h ]
[ -H | --host ]
[ -P | --port ]
[ -Q | --queue ]

=head1 DESCRIPTION

Check Gearman Server

=head1 ARGUMENTS

script has the following arguments

-h | --help Display the help and exit

-v | --verbose Enable verbose mode

-H | --host=<hostname> connect to this gearman server.

-P | --port Gearman tcp port. Defaults to 4730

-M | --mode Type of check to perform. 
    
    '--mode=queue --queue=perfdata' count the number of waiting jobs for a given queue
    '--mode=orphaned_jobs' report back queues containing jobs but without workers connected

-Q | --queue name of queue to check

=head1 EXAMPLE

./check_grearman.pl --host=localhost --port=4730 --queue=pnp4nagios 

=head1 AUTHOR

Joerg Linge, <joerg.linge@pnp4nagios.org>

=cut


