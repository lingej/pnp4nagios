#!/usr/bin/perl 
#
# write perfdata files into gearman queues
#

use warnings;
use strict;
use POSIX;
use Gearman::Client;

unless (defined $ARGV[0]) {
    print "No filename given\n";
    print "$0 <file_to_process>\n";
    exit;
}

my @job_servers=('127.0.0.1:4730');
my $file = $ARGV[0];

my $client = Gearman::Client->new;
$client->job_servers(@job_servers);

open(FH, "<", $file);
while(<FH>){
    my $data = $_; 
    $client->dispatch_background("pp", $data);
}

unlink $file;

