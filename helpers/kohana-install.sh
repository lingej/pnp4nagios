#!/bin/sh
#
# PNP4Nagios Helper Script
#
DIR=`dirname $0`
cd $DIR/../lib/kohana
for D in `find . -type d -printf "%P\n"`;do
	if [ "$D" != "" ];then
		echo -e "\t\$(INSTALL) -m 755 \$(INSTALL_OPTS) -d \$(DESTDIR)\$(LIBDIR)/kohana/$D"
	fi
done
for F in `find . -type f -printf "%P\n"`;do
	if [ "$F" != "" ];then
		echo -e "\t\$(INSTALL) -m 644 \$(INSTALL_OPTS) kohana/$F \$(DESTDIR)\$(LIBDIR)/kohana/$F"
	fi
done
