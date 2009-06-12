#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

AC_PREREQ(2.61)
AC_INIT(pnp, 0.4.14, pnp4nagios-devel@lists.sourceforge.net)
AC_CONFIG_SRCDIR(src/)
AC_CONFIG_HEADERS(include/config.h:include/config.h.in)
AC_PREFIX_DEFAULT(/usr/local/nagios)

AC_DEFINE([DEFAULT_NAGIOS_USER], [nagios], [Default Nagios User])
AC_DEFINE([DEFAULT_NAGIOS_GROUP], [nagios], [Default Nagios Group])

PKG_NAME=pnp
PKG_VERSION="0.4.14"
PKG_HOME_URL="http://www.pnp4nagios.org/pnp/start"
PKG_REL_DATE="05-02-2009"
AC_SUBST(PKG_NAME)
AC_SUBST(PKG_VERSION)
AC_SUBST(PKG_HOME_URL)
AC_SUBST(PKG_REL_DATE)

dnl Figure out how to invoke "install" and what install options to use.

AC_PROG_INSTALL
AC_SUBST(INSTALL)

#dnl What OS are we running?
AC_CANONICAL_HOST

dnl Checks for programs.
AC_PROG_CC
AC_PROG_MAKE_SET
AC_PATH_PROG([STRIP],[strip],[true])



AC_PATH_PROG(CP,cp)

# Checks for libraries.

# Checks for header files.
AC_HEADER_STDC
AC_HEADER_DIRENT
AC_HEADER_SYS_WAIT
#AC_CHECK_HEADERS(netinet/in.h string.h sys/socket.h unistd.h stdio.h stdlib.h getopt.h signal.h)
AC_CHECK_HEADERS(dirent.h stdio.h errno.h unistd.h syslog.h signal.h stdlib.h dirent.h string.h pthread.h getopt.h grp.h pwd.h sys/mman.h sys/types.h sys/wait.h sys/stat.h sys/socket.h sys/loadavg.h netinet/in.h fcntl.h limits.h)

# Checks for typedefs, structures, and compiler characteristics.
AC_TYPE_PID_T
AC_TYPE_SIGNAL
AC_TYPE_UID_T

# Checks for structure members
#AC_CHECK_MEMBER([struct dirent.d_type], [AC_MSG_RESULT([We successfully have a `dirent_d_type'!])],
#		[AC_MSG_ERROR([We need `dirent.d_type'!])],
#		[#include <dirent.h>])

AC_STRUCT_DIRENT_D_TYPE

# Checks for library functions.
AC_FUNC_FORK
AC_CHECK_FUNCS([bzero socket alarm strerror strspn getloadavg])

AC_FUNC_MALLOC
AC_FUNC_STAT

AC_C_CONST


AC_ARG_WITH(nagios_user,[  --with-nagios-user=<user> sets user name to run nagios],nagios_user=$withval,nagios_user=nagios)
AC_ARG_WITH(nagios_group,[  --with-nagios-group=<grp> sets group name to run nagios],nagios_grp=$withval,nagios_grp=nagios)
AC_SUBST(nagios_user)
AC_SUBST(nagios_grp)
AC_DEFINE_UNQUOTED(DEFAULT_NAGIOS_USER,"$nagios_user")
AC_DEFINE_UNQUOTED(DEFAULT_NAGIOS_GROUP,"$nagios_grp")
INSTALL_OPTS="-o $nagios_user -g $nagios_grp"
AC_SUBST(INSTALL_OPTS)

# Checks for programs.
AC_PATH_PROG(PERL,perl)

AC_ARG_WITH(onlypnpsender,[  --with-onlypnpsender use this if you know that you don't have rrdtools],ONLYPNPSENDER=yes,ONLYPNPSENDER=no)

PERL_LIB_PATH=no
AC_ARG_WITH(perl_lib_path,[  --with-perl_lib_path=<path_to_perl_libs> sets path to rrdtool RRDs perl modules.],PERL_LIB_PATH=$withval,PERL_LIB_PATH=no)
AC_SUBST(PERL_LIB_PATH)

RRDTOOL=no
AC_ARG_WITH(rrdtool,[  --with-rrdtool=<path_to_rrdtool> sets path to rrdtool],RRDTOOL=$withval,RRDTOOL=no)
if test RRDTOOL=no; then
        AC_PATH_PROG(RRDTOOL,rrdtool)
fi

if test "x$ONLYPNPSENDER" = "xno" ;then
    AC_MSG_CHECKING(rrdtool path $RRDTOOL)
    if [ test -d $RRDTOOL ];then
        AC_MSG_RESULT(no)
        AC_MSG_ERROR([$RRDTOOL is a directory! PNP needs the Path to the rrdtool binary!])
    fi
    AC_MSG_RESULT(yes)
    AC_MSG_CHECKING(for executable Bit on $RRDTOOL)
    if [ ! test -x $RRDTOOL] ;then
        AC_MSG_RESULT(no)
        AC_MSG_ERROR([$RRDTOOL is not executable!])
    fi
    AC_MSG_RESULT(yes)
else
	AC_MSG_WARN([Hope you know that you come along without rrdtools...])
fi

AC_SUBST(RRDTOOL)


AC_ARG_WITH(perfdata-logfile,[  --with-perfdata-logfile=<perfdata_logfile> Tell me where I should store the 'process_perfdata.pl' Logfile],
            PERFDATA_LOG=$withval,
	    PERFDATA_LOG=$localstatedir/perfdata.log
	   )
AC_SUBST(PERFDATA_LOG)


AC_ARG_WITH(perfdata-dir,[  --with-perfdata-dir=<path_to_perfdata> Tell me where I should store the RRD Database Files],
	    PERFDATA_DIR=$withval,
	    PERFDATA_DIR=$datarootdir/perfdata
	   )
AC_SUBST(PERFDATA_DIR)

AC_ARG_WITH(perfdata-spool-dir,[  --with-perfdata-spool-dir=<path_to_perfdata_spool_dir> Tell me where I should store perfdata files for bulk mode with npcd],
	    PERFDATA_SPOOL_DIR=$withval,
	    PERFDATA_SPOOL_DIR=$localstatedir/spool/perfdata/
	   )
AC_SUBST(PERFDATA_SPOOL_DIR)

AC_ARG_WITH(debug,[  --with-debug Enable debuging for process_perfdata.pl],
	    DEBUG="2",
	    DEBUG="0"
	   )
AC_SUBST(DEBUG)

										
#AC_CHECK_FILE($HTML_DIR,[PNP_UPDATE="true"],[PNP_UPDATE="false"])

dnl Check for location of init scripts
init_dir=/etc/rc.d/init.d
if test -d /etc/rc.d/init.d; then
    init_dir="/etc/rc.d/init.d"
elif test -d /usr/local/etc/rc.d; then
    init_dir="/usr/local/etc/rc.d"
elif test -d /etc/rc.d; then
    init_dir="/etc/rc.d"
elif test -d /etc/init.d; then
    init_dir="/etc/init.d"
elif test -d /sbin/init.d; then
    init_dir="/sbin/init.d"
fi

dnl User can override init script location
AC_ARG_WITH(init_dir,[  --with-init-dir=<path> sets directory to place init script into],init_dir=$withval) 
AC_SUBST(init_dir)

pnpsender_name=pnpsender
AC_SUBST(pnpsender_name)

npcd_name=npcd
AC_SUBST(npcd_name)

pp_pl_name=process_perfdata.pl
AC_SUBST(pp_pl_name)

dnl - Modified version from www.erlang.org
dnl - Some 12/15/05 mods made after reading http://xaxxon.slackworks.com/phuku/dl.html
AC_MSG_CHECKING(for linker flags for loadable modules)
case $host_os in
	solaris2*|sysv4*)
		MOD_LDFLAGS="-G"
	;;
	aix4*|aix5*)
		#MOD_LDFLAGS="-G -bnoentry -bexpall"
		MOD_LDFLAGS="-shared -Wl,-G -Wl,-bM:SRE -Wl,-bnoentry -Wl,-bexpall"
	;;
	freebsd2*)
		# Non-ELF GNU linker
		MOD_LDFLAGS="-Bshareable"
	;;
	darwin*)
		# Mach-O linker, a shared lib and a loadable
		# object file is not the same thing.
		MOD_LDFLAGS="-bundle -flat_namespace -undefined suppress"
		MOD_CFLAGS="$MOD_CFLAGS -fno-common"
	;;
	linux* | k*bsd*-gnu*)
		# assume GNU linker and ELF
		MOD_LDFLAGS="-shared"
		MOD_CFLAGS="-fPIC"
	;;
	*)
		# assume GNU linker and ELF
		MOD_LDFLAGS="-shared"
	;;
esac
AC_MSG_RESULT([$MOD_LDFLAGS])
AC_SUBST(MOD_CFLAGS)
AC_SUBST(MOD_LDFLAGS)

#
# Checking for Perl Modules
#
AC_MSG_CHECKING(for Perl Module Time::HiRes)
$PERL -MTime::HiRes -e exit >/dev/null 2>&1
if test $? -ne 0; then
    AC_MSG_RESULT(no)
    AC_MSG_ERROR(Perl Module Time::HiRes not available)
else
    AC_MSG_RESULT(yes)
fi

AC_MSG_CHECKING(for Perl Module Getopt::Long)
$PERL -MGetopt::Long -e exit >/dev/null 2>&1
if test $? -ne 0; then
    AC_MSG_RESULT(no)
    AC_MSG_ERROR(Perl Module Getopt::Long not available)
else
    AC_MSG_RESULT(yes)
fi

RRDS=0
AC_MSG_CHECKING(for optional Perl Module RRDs)
$PERL -MRRDs -e exit >/dev/null 2>&1
if test $? -ne 0; then
    AC_MSG_RESULT(no)
    AC_MSG_WARN(Perl Module RRDs not available)
    RRDS=0
else
    AC_MSG_RESULT(yes)
    RRDS=1
fi
AC_SUBST(RRDS)

AC_CONFIG_FILES( subst )
AC_OUTPUT()

$PERL subst summary
$PERL subst Makefile
$PERL subst share/Makefile
$PERL subst scripts/Makefile
$PERL subst src/Makefile
$PERL subst sample-config/Makefile
$PERL subst man/Makefile
$PERL subst scripts/process_perfdata.pl 
$PERL subst scripts/net2pnp.pl 
$PERL subst scripts/rc.npcd 
$PERL subst scripts/check_pnp_rrds.pl 
$PERL subst share/pnp/index.php 
$PERL subst share/pnp/zoom.php 
$PERL subst share/pnp/ajax.php 
$PERL subst sample-config/pnp/config.php 
$PERL subst sample-config/xinetd.pnp.sample 
$PERL subst sample-config/misccommands.sample 
$PERL subst sample-config/pnp/npcd.cfg-sample 
$PERL subst sample-config/pnp/process_perfdata.cfg-sample
$PERL subst sample-config/pnp/pnp4nagios_release
$PERL subst contrib/verify_pnp_config 

$PERL summary
