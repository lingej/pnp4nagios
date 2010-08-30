#!/usr/bin/perl
#
#
#
#
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
        PeerAddr => "localhost",
        PeerPort => "4730",
    ) or die "cannot connect to daytime port at localhost";

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
        #print Dumper $queue{$opt_queue};
        $state_id = $np->check_threshold(check => $queue{$opt_queue}{'waiting'}, warning => $opt_warn_jobs, critical => $opt_crit_jobs);
        #if($queue{$opt_queue}{'waiting'} >= $opt_crit_jobs){
        if($state_id == 2){
            $state_txt = "CRITICAL";
            $state_id  = 2;
            $state_info .= sprintf("[Jobs waiting %s (C=>%s)] ",$queue{$opt_queue}{'worker'}, $opt_crit_jobs); 
 
        #}elsif($queue{$opt_queue}{'waiting'} >= $opt_warn_jobs){
        }elsif($state_id == 1){
            $state_txt = "WARNING";
            $state_id  = 1; 
            $state_info .= sprintf("[Jobs waiting %s (W=>%s)] ",$queue{$opt_queue}{'waiting'}, $opt_warn_jobs); 

        }else{
            $state_txt = "OK";
            $state_id  = 0; 
        }
        $state_id = $np->check_threshold(check => $queue{$opt_queue}{'worker'}, warning => $opt_warn_worker, critical => $opt_crit_worker);
        #if($queue{$opt_queue}{'worker'} < $opt_crit_worker){
        if($state_id == 2){
            $state_txt = "CRITICAL";
            $state_id  = 2;
            $state_info .= sprintf("[Worker %s (C=>%s)] ",$queue{$opt_queue}{'worker'}, $opt_crit_worker); 
 
        #}elsif($queue{$opt_queue}{'worker'} < $opt_warn_worker){
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

-Q | --queue name of queue to check

=head1 EXAMPLE

./check_grearman.pl --host=localhost --port=4730 --queue=pnp4nagios 

=head1 AUTHOR

Joerg Linge, <joerg.linge@pnp4nagios.org>

=cut


