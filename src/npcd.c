/* Copyright (C) 2007-2009 Hendrik Baecker <andurin@process-zero.de>
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License version 2 as
* published by the Free Software Foundation;
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

#include "../include/config.h"
#include "../include/pnp.h"

typedef void (*sighandler_t)(int);

void *processfile(void *);
static void *exit_handler_mem(void *);
extern int process_arguments(int, char **);
extern void process_configfile(char *);
extern void check_sig(int);
extern int prepare_vars(void);
extern int drop_privileges(char *, char *);
extern sighandler_t handle_signal(int, sighandler_t);
extern int is_file(const struct dirent *d);
extern int check_needed_config_options();

double getload(int);

static int thread_counter = 0;
int max_threads = 5;
int daemon_mode = FALSE;
int use_syslog = TRUE;
int loglevel = 0;
int max_logfile_size = 10485760; /* default 10Mbyte */
int use_load_threshold = FALSE;
int we_should_stop = FALSE;
int sleeptime = 15;
int identmyself = TRUE;
double load_threshold = 10.0;

extern int sighup_detected;

char *command, *command_args, *user, *group, *pidfile;
char *macro_x[CONFIG_OPT_COUNT];
char *log_file, *log_type;
char *config_file = NULL;

const char *directory = NULL;
const char progname[5] = "npcd";

static void start_daemon(const char *log_name, int facility) {
	int i;
	pid_t pid;

	/* Kill parent after for to get a waise */
	if ((pid = fork()) != 0)
		exit(EXIT_SUCCESS);

	/* Get this waise to sessionleader */
	if (setsid() < 0) {
		printf("%s could not get sessionleader\n", log_name);
		exit(EXIT_FAILURE);
	}

	/* Ignore SIGHUP */
	handle_signal(SIGHUP, SIG_IGN);

	/* terminate child */
	if ((pid = fork()) != 0)
		exit(EXIT_SUCCESS);

	/* for core dump handling and better unmounting behavior */
	/* Its return should not be ignored.
	 * npcd.c:79: warning: ignoring return value of ‘chdir’ */
	if (chdir("/") != 0)
		exit(EXIT_FAILURE);

	/* change umask to defined value - be independet from parent umask */
	umask(002);

	/* close all possible file handles */
	for (i = sysconf(_SC_OPEN_MAX); i > 0; i--)
		close(i);

	/* to hear the daemon you are calling... use syslog */
	if (use_syslog == TRUE && log_name != NULL)
		openlog(log_name, LOG_PID | LOG_CONS | LOG_NDELAY, facility);

        /* close existing stdin, stdout, stderr */
        close(0);
        close(1);
        close(2);

        /* re-open stdin, stdout, stderr with known values */
        open("/dev/null",O_RDONLY);
        open("/dev/null",O_WRONLY);
        open("/dev/null",O_WRONLY);

}

int main(int argc, char **argv) {

	int i = 0;
	int filecounter = 0, pthread_ret = 0;
	double load;
	char buffer[MAX_LOGMESSAGE_SIZE];

	FILE *fppid = NULL;

	struct dirent **namelist;

	load = 0.0;

	if (process_arguments(argc, argv) == EXIT_FAILURE)
		exit(EXIT_FAILURE);

	process_configfile(config_file);

	if (loglevel == -1) {
		printf("DEBUG: Config File = %s\n", config_file);
		printf("CONFIG_OPT_LOGTYPE = %s\n", macro_x[CONFIG_OPT_LOGTYPE]);
		printf("CONFIG_OPT_LOGFILE = %s\n", macro_x[CONFIG_OPT_LOGFILE]);
		printf("CONFIG_OPT_LOGFILESIZE = %s\n", macro_x[CONFIG_OPT_LOGFILESIZE]);
		printf("CONFIG_OPT_LOGLEVEL = %s\n", macro_x[CONFIG_OPT_LOGLEVEL]);
		printf("CONFIG_OPT_SCANDIR = %s\n", macro_x[CONFIG_OPT_SCANDIR]);
		printf("CONFIG_OPT_RUNCMD = %s\n", macro_x[CONFIG_OPT_RUNCMD]);
		printf("CONFIG_OPT_RUNCMD_ARG = %s\n", macro_x[CONFIG_OPT_RUNCMD_ARG]);
		printf("CONFIG_OPT_MAXTHREADS = %s\n", macro_x[CONFIG_OPT_MAXTHREADS]);
		printf("CONFIG_OPT_LOAD = %s\n", macro_x[CONFIG_OPT_LOAD]);
		printf("CONFIG_OPT_USER = %s\n", macro_x[CONFIG_OPT_USER]);
		printf("CONFIG_OPT_GROUP = %s\n", macro_x[CONFIG_OPT_GROUP]);
		printf("CONFIG_OPT_PIDFILE = %s\n", macro_x[CONFIG_OPT_PIDFILE]);
		printf("CONFIG_OPT_SLEEPTIME = %s\n", macro_x[CONFIG_OPT_SLEEPTIME]);
		printf("CONFIG_OPT_IDENTMYSELF = %s\n", macro_x[CONFIG_OPT_IDENTMYSELF]);
		printf("---------------------------\n");
		if (check_needed_config_options() != 0) {
			printf("There is an Error! Exiting...\n");
			exit(EXIT_FAILURE);
		}
	}

	if (prepare_vars() != 0)
		exit(EXIT_FAILURE);
	if (loglevel == -1)
		printf("DEBUG: load_threshold is %s - ('%f')\n",
				use_load_threshold ? "enabled" : "disabled", load_threshold);

	pthread_t th[max_threads];
	for (i=0;i<max_threads;i++){
		th[i] = (pthread_t) NULL;
	}
	i = 0;

	/* Nice point for another function to set
	 * the internal vars from macro_x[] */

	/* Start in Daemon Mode or in foreground? */
	if (daemon_mode == TRUE)
		start_daemon("NPCD", LOG_LOCAL0);

	else if (use_syslog)
		openlog("NPCD", LOG_PID | LOG_CONS | LOG_NDELAY, LOG_LOCAL0);

	/* Create PID File or exit on failure */
	if (daemon_mode == TRUE && sighup_detected == FALSE) {
		fppid = fopen(pidfile, "w");

		if (fppid == NULL) {
			printf("Could not open pidfile '%s': %s\n", pidfile,
					strerror(errno));
			exit(EXIT_FAILURE);
		} else {
			fprintf(fppid, "%d", getpid());
			fclose(fppid);
		}
	}

	/* Try to drop the privileges */
	if (drop_privileges(user, group) == EXIT_FAILURE)
		exit(EXIT_FAILURE);

	snprintf(buffer, sizeof(buffer) - 1,
			"%s Daemon (%s) started with PID=%d\n", progname, PACKAGE_VERSION,
			getpid());
	LOG(0, buffer);
	snprintf(buffer, sizeof(buffer) - 1,
			"Please have a look at '%s -V' to get license information\n",
			progname);
	LOG(0, buffer);

	//sigemptyset();
	handle_signal(SIGINT, check_sig);
	handle_signal(SIGHUP, check_sig);
	handle_signal(SIGTERM, check_sig);

	snprintf(buffer, sizeof(buffer) - 1,
			"HINT: load_threshold is %s - ('%f')\n",
			use_load_threshold ? "enabled" : "disabled", load_threshold);
	LOG(0, buffer);

	/* beginn main loop */
	while (1) {

		if (chdir(directory) != 0)
			exit(EXIT_FAILURE);

		/* is_file() filter may cause trouble on some systems
		 * like Solaris or HP-UX that don't have a d-type
		 * member in struct dirent
		 */
		/* #ifdef HAVE_STRUCT_DIRENT_D_TYPE
		 if ( ( filecounter = scandir( directory, &namelist, is_file, alphasort ) ) < 0 ) {
		 #else */
		if ((filecounter = scandir(directory, &namelist, 0, alphasort)) < 0) {
			/* #endif */
			snprintf(buffer, sizeof(buffer) - 1,
					"Error while get file list from spooldir (%s) - %s\n",
					directory, strerror(errno));
			LOG(0, buffer);
			snprintf(buffer, sizeof(buffer) - 1, "Exiting...\n");
			LOG(0, buffer);

			if (daemon_mode != TRUE)
				printf("Error while get file list from spooldir (%s) - %s\n",
						directory, strerror(errno));
			break;
		}

		snprintf(buffer, sizeof(buffer) - 1, "Found %d files in %s\n",
				filecounter, directory);
		LOG(2, buffer);

		for (i = 0, namelist; i < filecounter; i++) {

#ifdef HAVE_GETLOADAVG
			if (use_load_threshold == TRUE) {
				load = getload(1);
				snprintf(buffer, sizeof(buffer) - 1, "DEBUG: load %f/%f\n",
						load, load_threshold);
				LOG(2, buffer);
			}

			if (use_load_threshold && (load > load_threshold)) {

				snprintf(buffer, sizeof(buffer) - 1,
						"WARN: MAX load reached: load %f/%f at i=%d", load,
						load_threshold, i);
				LOG(0, buffer);

				if (i > 0)
					i--;
				sleep(sleeptime);
				continue;
			}
#endif

			snprintf(buffer, sizeof(buffer) - 1,
					"ThreadCounter %d/%d File is %s\n", thread_counter,
					max_threads, namelist[i]->d_name);
			LOG(2, buffer);

			struct stat attribute;

			if (stat(namelist[i]->d_name, &attribute) == -1) {
				LOG(0, "Error while getting file status");
				break;
			}

			if (strstr((namelist[i]->d_name), "-PID-") != NULL) {
				snprintf(
						buffer,
						sizeof(buffer) - 1,
						"File '%s' is an already in process PNP file. Leaving it untouched.\n",
						namelist[i]->d_name);

				LOG(1, buffer);
				continue;
			}

			if (S_ISREG(attribute.st_mode)) {
				snprintf(buffer, sizeof(buffer) - 1, "Regular File: %s\n",
						namelist[i]->d_name);
				LOG(2, buffer);

				/* only start new threads if the max_thread config option is not reached */
				if (thread_counter < max_threads && we_should_stop == FALSE) {

					if ((pthread_ret = pthread_create(&th[thread_counter],
							NULL, processfile, namelist[i]->d_name)) != 0) {
						snprintf(buffer, sizeof(buffer) - 1,
							"Could not create thread... exiting with error '%s'\n", strerror(errno));
						LOG(0, buffer);
						exit(EXIT_FAILURE);
					}

					snprintf(buffer, sizeof(buffer) - 1,
							"A thread was started on thread_counter = %d\n",
							thread_counter);
					LOG(2, buffer);

					thread_counter++;

				}

				else if (we_should_stop == TRUE)
					break;

				else {

					snprintf(
							buffer,
							sizeof(buffer) - 1,
							"WARN: MAX Thread reached: %s comes later with ThreadCounter: %d\n",
							namelist[i]->d_name, thread_counter);
					LOG(2, buffer);

					i--;

					for (thread_counter = thread_counter; thread_counter > 0; thread_counter--) {
						snprintf(buffer, sizeof(buffer) - 1,
								"DEBUG: Will wait for th['%d']\n",
								thread_counter - 1);
						LOG(2, buffer);
						pthread_join(th[thread_counter - 1], NULL);
					}
				}
			}
		}

		if (thread_counter > 0) {
			/* Wait for open threads before working on the next run */
			snprintf(buffer, sizeof(buffer) - 1,
					"Have to wait: Filecounter = %d - thread_counter = %d\n",
					filecounter - 2, thread_counter);
			LOG(2, buffer);

			for (thread_counter = thread_counter; thread_counter > 0; thread_counter--)
				pthread_join(th[thread_counter - 1], NULL);
		}

		if (we_should_stop == TRUE)
			break;

		for (i = 0, namelist; i < filecounter; i++) {
			free(namelist[i]);
		}

		free(namelist);

		snprintf(buffer, sizeof(buffer) - 1,
				"No more files to process... waiting for %d seconds\n",
				sleeptime);
		LOG(1, buffer);

		sleep(sleeptime);

	}

	snprintf(buffer, sizeof(buffer) - 1, "Daemon ended. PID was '%d'\n",
			getpid());
	LOG(0, buffer);

	if (use_syslog)
		closelog();
	return EXIT_SUCCESS;
}

/* **************************************************************
 *
 * Function to parse and check the commandline arguments
 *
 * *************************************************************/

int process_arguments(int argc, char **argv) {
	int c;
	int error = FALSE;
	int display_license = FALSE;
	int display_help = FALSE;

#ifdef HAVE_GETOPT_H
	int option_index = 0;
	static struct option long_options[] = { { "help", no_argument, 0, 'h' }, {
			"version", no_argument, 0, 'V' },
			{ "license", no_argument, 0, 'V' },
			{ "daemon", no_argument, 0, 'd' }, { "config", required_argument,
					0, 'f' }, { 0, 0, 0, 0 } };
#endif

	/* make sure we have the correct number of command line arguments */
	if (argc < 2)
		error = TRUE;

	while (1) {

#ifdef HAVE_GETOPT_H
		c = getopt_long(argc, argv, "+hVdf:", long_options, &option_index);
#else
		c = getopt(argc, argv, "+hVdf:");
#endif

		if (c == -1 || c == EOF)
			break;

		switch (c) {

		case '?': /* usage */
		case 'h':
			display_help = TRUE;
			break;

		case 'V': /* version */
			printf("%s %s - $Revision: 637 $\n\n", progname, PACKAGE_VERSION);
			display_license = TRUE;
			break;

		case 'd': /* run in daemon mode */
			daemon_mode = TRUE;
			break;

		case 'f': /* config file */
			if (optarg != NULL)
				config_file = optarg;
			break;

		default:
			break;
		}

	}

	if (display_license == TRUE) {

		printf(
				"This program is free software; you can redistribute it and/or modify\n");
		printf(
				"it under the terms of the GNU General Public License version 2 as\n");
		printf("published by the Free Software Foundation.\n\n");
		printf(
				"This program is distributed in the hope that it will be useful,\n");
		printf(
				"but WITHOUT ANY WARRANTY; without even the implied warranty of\n");
		printf(
				"MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n");
		printf("GNU General Public License for more details.\n\n");
		printf(
				"You should have received a copy of the GNU General Public License\n");
		printf("along with this program; if not, write to the Free Software\n");
		printf(
				"Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA\n\n");

		exit(EXIT_SUCCESS);
	}

	/* if there are no command line options (or if we encountered an error), print usage */
	if (error == TRUE || display_help == TRUE) {
		printf("\nUsage: %s -f <configfile> [-d] \n", argv[0]);
		printf("\n");
		printf("Options:\n");
		printf("\n");
		printf("  -d | --daemon \n");
		printf("\t\tRun as daemon in background\n");
		printf("\n");
		printf("  -f | --config \n");
		printf("\t\tPath to config file\n");
		printf("\n");
		printf(
				"Visit the Website at http://sourceforge.net/projects/pnp4nagios/ for bug fixes, \n");
		printf("new releases, online documentation, FAQs, Mailinglists.\n");
		printf("\n");

		exit(EXIT_FAILURE);
	}
	return EXIT_SUCCESS;
}

/********************************************************************
 *								    *
 * processfile - this is the function for each thread		    *
 *								    *
 ********************************************************************/

void * processfile(void *filename) {

	char *file = (char *) filename;
	char command_line[MAX_COMMANDLINE_LENGTH];
	char buffer[MAX_LOGMESSAGE_SIZE];
	int result;
	FILE *proc;

	/* npcd.c:493: warning: ‘result’ may be used uninitialized in this function */
	result = 0;

	snprintf(command_line, sizeof(command_line), "%s %s %s %s/%s", command,
			identmyself ? "-n" : "\b", command_args, directory, file);

	pthread_cleanup_push((void *) &exit_handler_mem, file);

		snprintf(buffer, sizeof(buffer) - 1,
				"Processing file %s with ID %ld - going to exec %s\n", file,
				pthread_self(), command_line);
		LOG(2, buffer);

		snprintf(buffer, sizeof(buffer) - 1, "Processing file '%s'\n", file);
		LOG(1, buffer);

		if ((proc = popen(command_line, "r")) != NULL)
			result = pclose(proc);

		result >>= 8;

		if (result != 0) {
			snprintf(buffer, sizeof(buffer) - 1,
					"ERROR: Executed command exits with return code '%d'\n",
					result);
			LOG(0, buffer);

			snprintf(buffer, sizeof(buffer) - 1,
					"ERROR: Command line was '%s'\n", command_line);
			LOG(0, buffer);

			we_should_stop = FALSE;
		}

		if (loglevel == -1)
			sleep(2);

		pthread_cleanup_pop(1);
	pthread_exit((void *) pthread_self());
}

/* ******************************************************************
 *                                                                  *
 * processfile - this is the function for each thread               *
 *                                                                  *
 * ******************************************************************/

static void *exit_handler_mem(void * arg) {
	// syslog( LOG_NOTICE, "Will now clean up thread %ld\n",pthread_self());
	//if (thread_counter > 0)
	//thread_counter--;
	return 0;
}

#ifdef HAVE_GETLOADAVG
double getload(int which_sample) {

	double loadavg[3];
	char buffer[MAX_LOGMESSAGE_SIZE];

	if (which_sample == 1) {
		which_sample = 0;
	} else if (which_sample == 5) {
		which_sample = 1;
	} else if (which_sample == 15) {
		which_sample = 2;
	} else {
		snprintf(buffer, sizeof(buffer) - 1,
				"Invalid load sample %d - allowed is 1,5,15\n", which_sample);
		LOG(0, buffer);

		return -1;
	}
	getloadavg(loadavg, 3);
	return loadavg[which_sample];
}
#endif
