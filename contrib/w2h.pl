#!@PERL@
## @PKG_NAME@–@PKG_VERSION@
## Copyright (c) 2010 http://www.pnp4nagios.org ( Wolfgang Nieder)
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

use strict;

sub usage {
print <<EOD;

------------------------------------------------------------------

   w2h.pl creates documentation in HTML format using the Wiki docs

   usage: w2h.pl <language>
   <language> is one of:
      de - german documentation
      en - english documentation
      es - spanish documentation

   folder structure
   +
   +-+- docs
     +--- de_DE  HTML documents
     +--- en_US  HTML documents
     +--- es_ES  HTML documents

------------------------------------------------------------------
EOD
}

if ((! @ARGV) or ($ARGV[0] =~ /-h/)) {
	usage();
	exit 1;
}
	
my %lang = ();
my $base = 'http://docs.pnp4nagios.org';
my $tag = "pnp-0.6";
my @pg = ();
my $out = 0;
my $res = 0;

$lang{de} = 'de_DE';
$lang{en} = 'en_US';
$lang{es} = 'es_ES';
# $lang{fr} = 'fr_FR';

mkdir ("docs") unless (-d "docs");
chdir ("docs") || die "shit";
mkdir ("_media") unless (-d "_media");

foreach my $lng (keys %lang) {
	next unless ($lng eq lc($ARGV[0]));
	@pg = ();
	mkdir ($lang{$lng}) unless (-d $lang{$lng});
	chdir ($lang{$lng}) || return;
	my $lng2 = ($lng eq 'en' ? "" : $lng);
	my $iFile = "start";
#	next if (-f $iFile);
	my $url = "$base/$lng2/$tag/$iFile";
	$res = `wget -nv $url`;
	conv ($iFile,$lng);
	for my $page (0..$#pg) {
		$pg[$page] =~ s/\?.*//;
		if ($pg[$page] =~ /(.*)\//) {
			`mkdir -p $1`;
		}
		unless (-f $pg[$page]) {
			$url = "$base/$lng2/$tag/$pg[$page]";
			$res = `wget -nv $url`;
		}
		conv ($pg[$page],$lng); # unless (-f "$pg[$page].html");
	}
	for my $page (0..$#pg) {
#		unlink $pg[$page];
	}
	chdir ("..");
}
chdir ("..");
exit;

sub conv {
	my $iFile = shift;
	my $lng = shift;
	my $oFile = "$iFile.html";

	return unless (-f $iFile);
	open (IFILE, "$iFile") || die "Error opening $iFile, RC=$!";
	open (OFILE, ">$oFile") || die "Error creating $oFile, RC=$!";
	print OFILE "<html>\n   <body>\n";

	while (<IFILE>) {
		if (/right_page/) {	# skip header stuff
			$out = 1;
			next;
		}
		$out = 0 if (/stylefoot/);	# finished
		next unless ($out);	
		next if (/Read more.../);	# line in doc_complete	

		if (/a href=".*\/$tag\/(.*?)(#.*)?"/) {	# might be a link	
			my $ad = $1;
			($ad) =~ s/\?.*//;
			next if ($ad =~ /=/);
			next if ($ad =~ /png/);
			push @pg,$ad;
		}
		# convert language specific characters to HTML entities
		s/ä/\&auml;/g;	
		s/ö/\&ouml;/g;
		s/ü/\&uuml;/g;
		s/Ä/\&Auml;/g;
		s/Ö/\&Ouml;/g;
		s/Ü/\&Uuml;/g;
		s/ß/\&szlig;/g;
		s/á/\&aacute;/g;
		s/é/\&eacute;/g;
		s/í/\&iacute;/g;
		s/ó/\&oacute;/g;
		s/ú/\&uacute;/g;
		s/ñ/\&ntilde;/g;
		s/¿/\&iquest;/g;
		s/–/--/g;
		s/—/-/g;
		s/“/"/g;
		s/”/"/g;
		s/„/"/g;
		s/…/&hellip;/g;

		# convert links 
		s#(/_media)#..$1#g;
		if ((/href="/) and (! /http:/)) {
			if (/href="(.*?)"/) {
				s#/$lng/$tag/(.*?)(["|\#])#$1.html$2#g;
			}
			if (/img src="(.+?)(\?.*?)"/) {
				my $pic = $1;
				my $img = $1;
				($img) =~ s/.*\///;
				chdir "../_media/";
				$res = `wget $base/$pic` unless (-f $img);
				chdir "../$lng";
				$_ = "<a href=\"../_media/$img\"><img src=\"../_media/$img\" /></a>";
			}
		}

		print OFILE "$_";
	}

	close (IFILE);
	print OFILE "   <body>\n<html>\n";
	close (OFILE);	
}
