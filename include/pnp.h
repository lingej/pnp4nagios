/* #include "../include/global.h" */

/*************************************
 *      General Header Files
 *************************************/

#ifdef HAVE_STDIO_H
#include <stdio.h>
#endif

#ifdef HAVE_ERRNO_H
#include <errno.h>
#endif

#ifdef HAVE_UNISTD_H
#include <unistd.h>
#endif

#ifdef HAVE_SYSLOG_H
#include <syslog.h>
#endif

#ifdef HAVE_SYS_TYPES_H
#include <sys/types.h>
#endif

#ifdef HAVE_SYS_WAIT_H
#include <sys/wait.h>
#endif

#ifdef HAVE_SYS_STAT_H
#include <sys/stat.h>
#endif

#ifdef HAVE_SYS_TIME_H
#include <sys/time.h>
#endif

#ifdef HAVE_SIGNAL_H
#include <signal.h>
#endif

#ifdef HAVE_STDLIB_H
#include <stdlib.h>
#endif

#ifdef HAVE_DIRENT_H
#include <dirent.h>
#endif

#ifdef HAVE_STRING_H
#include <string.h>
#endif

#ifdef HAVE_TIME_H
#include <time.h>
#endif

#ifdef HAVE_PTHREAD_H
#include <pthread.h>
#endif

#ifdef HAVE_GETOPT_H
#include <getopt.h>
#endif

#ifdef HAVE_GRP_H
#include <grp.h>
#endif

#ifdef HAVE_PWD_H
#include <pwd.h>
#endif

#ifdef HAVE_FCNTL_H
#include <fcntl.h>
#endif

#ifdef HAVE_LIMITS_H
#include <limits.h>
#endif

#ifdef HAVE_SYS_MMAN_H
#include <sys/mman.h>
#endif

/*************************************
 Default defines
 **************************************/
extern int do_log(char*);


#define LOG(level, msg) (loglevel >= level || loglevel == -1) ? (do_log(msg)) : (0)

#define TRUE    1
#define FALSE   0

#define OK      0
#define ERROR   -2

#define MAX_FILENAME_LENGTH             256
#define MAX_VARIABLE_LENGTH             256
#define MAX_VALUE_LENGTH                256
#define MAX_COMMANDLINE_LENGTH			512

#define MAX_BUFFER_SIZE                 1024
#define MAX_LOGMESSAGE_SIZE              768

#define CONFIG_OPT_COUNT                15

#define CONFIG_OPT_LOGTYPE				0
#define CONFIG_OPT_LOGFILE              1
#define CONFIG_OPT_LOGFILESIZE          2
#define CONFIG_OPT_LOGLEVEL             3
#define CONFIG_OPT_SCANDIR              4
#define CONFIG_OPT_RUNCMD				5
#define CONFIG_OPT_RUNCMD_ARG			6
#define CONFIG_OPT_MAXTHREADS			7
#define CONFIG_OPT_USER					8
#define CONFIG_OPT_GROUP				9
#define CONFIG_OPT_PIDFILE				10
#define CONFIG_OPT_USELOAD				11
#define CONFIG_OPT_LOAD					12
#define CONFIG_OPT_SLEEPTIME			13
#define CONFIG_OPT_IDENTMYSELF			14

