// Copyright (C) 2007-2009 Hendrik Baecker <andurin@process-zero.de>
// Inspired by Ethan Galstad <egalstad@nagios.org>
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License version 2 as
// published by the Free Software Foundation;
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

#include "../include/config.h"
#include "../include/pnp.h"

#include <ctype.h>

extern void pnp_strip(char *);

extern char *macro_x[CONFIG_OPT_COUNT];
extern char *log_file, *log_type;
extern char *command, *command_args, *user, *group, *directory, *pidfile;
extern int daemon_mode;
extern int sighup_detected;
extern int use_syslog;
extern int loglevel;
extern int max_logfile_size;
extern int max_threads;
extern int use_load_threshold;
extern int sleeptime;
extern int identmyself;
extern double load_threshold;

void process_configfile(char *config_file) {
	FILE *fh_config_file = NULL;
	char current_config_line[MAX_BUFFER_SIZE];
	char error_msg[MAX_BUFFER_SIZE];
	char variable[MAX_VARIABLE_LENGTH];
	char value[MAX_VALUE_LENGTH];
	// char *input = NULL;
	char *temp;
	extern int loglevel;

	int error = FALSE;
	// int warning = FALSE;
	int line_number = 0;

	fh_config_file = fopen(config_file, (const char*) "r");

	/* check if we can not open that file */
	if (fh_config_file == NULL) {
		snprintf(error_msg, MAX_BUFFER_SIZE,
				"ERROR - Could not open config file - %s", strerror(errno));
		error = TRUE;
	}

	/* ok - parsing the config file line by line */
	else {
		while (feof(fh_config_file) == 0) {
			line_number++;
			temp = fgets(current_config_line, 1024, fh_config_file);
			temp = NULL;
			if (current_config_line == NULL) {
				printf("Error reading config\n");
				exit(1);
			}

			if (current_config_line[0] == '#' || current_config_line[0] == '\n')
				continue;

			pnp_strip(current_config_line);

			temp = strtok(current_config_line, "=");
			pnp_strip(temp);

			/* if there is no variable name, return error */
			if (temp == NULL) {
				strcpy(error_msg, "No variable found - exiting");
				error = TRUE;
				break;
			}

			/* else the variable is good */
			strncpy(variable, temp, sizeof(variable));
			variable[sizeof(variable) - 1] = '\x0';

			/* get the value */
			temp = strtok(NULL, "\n");
			pnp_strip(temp);
			/* if no value exists, return error */
			if (temp == NULL) {
				strcpy(error_msg, "NULL value");
				error = TRUE;
				break;
			}

			/* else the value is good */
			strncpy(value, temp, sizeof(value));
			value[sizeof(value) - 1] = '\x0';
			pnp_strip(value);

			/* process the variable/value */

			/* log_file directive */
			if (!strcmp(variable, "log_file")) {
				if (strlen(value) > MAX_FILENAME_LENGTH - 1) {
					strcpy(error_msg, "Log file is too long");
					error = TRUE;
					break;
				}

				if (log_file != NULL)
					free(log_file);

				log_file = (char *) strdup(value);
				pnp_strip(log_file);

				/* save the macro */
				if (macro_x[CONFIG_OPT_LOGFILE] != NULL)
					free(macro_x[CONFIG_OPT_LOGFILE]);

				macro_x[CONFIG_OPT_LOGFILE] = (char *) strdup(log_file);

				if (macro_x[CONFIG_OPT_LOGFILE] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro logfile");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_LOGFILE]);
			}

			/* log_file_size directive */
			else if (!strcmp(variable, "max_logfile_size")) {
				if (strlen(value) == 1) {
					if (isdigit((int)value[strlen(value)-1]) == FALSE) {
						strcpy(error_msg, "log_file_size should be an integer");
						error = TRUE;
						break;
					}
				}

				/* save the macro */
				if (macro_x[CONFIG_OPT_LOGFILESIZE] != NULL)
					free(macro_x[CONFIG_OPT_LOGFILESIZE]);

				macro_x[CONFIG_OPT_LOGFILESIZE] = (char *) strdup(value);
				max_logfile_size = atoi(value);
				if (macro_x[CONFIG_OPT_LOGFILESIZE] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro logfilesize");
					error = TRUE;
					break;
				}

				pnp_strip(macro_x[CONFIG_OPT_LOGFILESIZE]);
			}

			/* log_level directive */
			else if (!strcmp(variable, "log_level")) {
				if (strlen(value) == 1) {
					if (isdigit((int)value[strlen(value)-1]) == FALSE) {
						strcpy(error_msg, "log_level should be an integer");
						error = TRUE;
						break;
					}
				}

				/* save the macro */
				if (macro_x[CONFIG_OPT_LOGLEVEL] != NULL)
					free(macro_x[CONFIG_OPT_LOGLEVEL]);

				macro_x[CONFIG_OPT_LOGLEVEL] = (char *) strdup(value);
				loglevel = atoi(value);
				if (macro_x[CONFIG_OPT_LOGLEVEL] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro loglevel");
					error = TRUE;
					break;
				}

				pnp_strip(macro_x[CONFIG_OPT_LOGLEVEL]);
			}

			/* log_type directive */
			else if (!strcmp(variable, "log_type")) {

				if (log_type != NULL)
					free(log_type);

				log_type = (char *) strdup(value);
				pnp_strip(log_type);

				/* save the macro */
				if (macro_x[CONFIG_OPT_LOGTYPE] != NULL)
					free(macro_x[CONFIG_OPT_LOGTYPE]);

				macro_x[CONFIG_OPT_LOGTYPE] = (char *) strdup(log_type);

				if (macro_x[CONFIG_OPT_LOGTYPE] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro log_type");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_LOGTYPE]);

				if (strcmp(macro_x[CONFIG_OPT_LOGTYPE], "syslog") == 0)
					use_syslog = TRUE;
				else if (strcmp(macro_x[CONFIG_OPT_LOGTYPE], "file") == 0)
					use_syslog = FALSE;
				else {
					strcpy(error_msg,
							"Please define \"syslog\" or \"file\" as log_type!");
					error = TRUE;
					break;
				}
			}

			else if (!strcmp(variable, "perfdata_spool_dir")) {
				if (strlen(value) > MAX_FILENAME_LENGTH - 1) {
					strcpy(error_msg, "Perfdata Spool Path is too long");
					error = TRUE;
					break;
				}

				/* save the macro */
				if (macro_x[CONFIG_OPT_SCANDIR] != NULL)
					free(macro_x[CONFIG_OPT_SCANDIR]);

				macro_x[CONFIG_OPT_SCANDIR] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_SCANDIR] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro Scandir");
					error = TRUE;
					break;
				}

				pnp_strip(macro_x[CONFIG_OPT_LOGFILE]);
			}

			else if (!strcmp(variable, "perfdata_file"))
				;

			else if (!strcmp(variable, "perfdata_spool_filename"))
				;

			else if (!strcmp(variable, "perfdata_file_processing_interval"))
				;

			else if (!strcmp(variable, "user")) {

				/* save the macro */
				if (macro_x[CONFIG_OPT_USER] != NULL)
					free(macro_x[CONFIG_OPT_USER]);

				macro_x[CONFIG_OPT_USER] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_USER] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro user");
					error = TRUE;
					break;
				}

				pnp_strip(macro_x[CONFIG_OPT_USER]);

			}

			else if (!strcmp(variable, "group")) {

				/* save the macro */
				if (macro_x[CONFIG_OPT_GROUP] != NULL)
					free(macro_x[CONFIG_OPT_GROUP]);

				macro_x[CONFIG_OPT_GROUP] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_GROUP] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro group");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_GROUP]);
			}

			else if (!strcmp(variable, "perfdata_file_run_cmd")) {

				/* save the macro */
				if (macro_x[CONFIG_OPT_RUNCMD] != NULL)
					free(macro_x[CONFIG_OPT_RUNCMD]);

				macro_x[CONFIG_OPT_RUNCMD] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_RUNCMD] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro runcmd");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_RUNCMD]);
			}

			else if (!strcmp(variable, "perfdata_file_run_cmd_args")) {

				/* save the macro */
				if (macro_x[CONFIG_OPT_RUNCMD_ARG] != NULL)
					free(macro_x[CONFIG_OPT_RUNCMD_ARG]);

				macro_x[CONFIG_OPT_RUNCMD_ARG] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_RUNCMD_ARG] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro runcmd_arg");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_RUNCMD_ARG]);
			}

			else if (!strcmp(variable, "npcd_max_threads")) {

				if (strlen(value) == 1) {
					if (isdigit((int)value[strlen(value)-1]) == FALSE) {
						strcpy(error_msg,
								"npcd_max_threads should be an integer");
						error = TRUE;
						break;
					}
				}
				/* save the macro */
				if (macro_x[CONFIG_OPT_MAXTHREADS] != NULL)
					free(macro_x[CONFIG_OPT_MAXTHREADS]);

				macro_x[CONFIG_OPT_MAXTHREADS] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_MAXTHREADS] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro MAXTHREADS");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_MAXTHREADS]);
			}

			else if (!strcmp(variable, "use_load_threshold")) {
				strcpy(error_msg,
						"The option 'use_load_threshold' is obsolete.");
				printf(
						"An Warning occured while reading your config on line %d. Message was: \"%s\"\n",
						line_number, error_msg);
			} else if (!strcmp(variable, "load_threshold")) {

				/* save the macro */
				if (macro_x[CONFIG_OPT_LOAD] != NULL)
					free(macro_x[CONFIG_OPT_LOAD]);

				macro_x[CONFIG_OPT_LOAD] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_LOAD] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro LOAD");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_LOAD]);
			}

			else if (!strcmp(variable, "pid_file")) {
				FILE *fppid;
				/* save the macro */
				if (macro_x[CONFIG_OPT_PIDFILE] != NULL)
					free(macro_x[CONFIG_OPT_PIDFILE]);

				macro_x[CONFIG_OPT_PIDFILE] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_PIDFILE] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro PIDFILE");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_PIDFILE]);

				if (daemon_mode == TRUE && sighup_detected == FALSE) {
					fppid = fopen(macro_x[CONFIG_OPT_PIDFILE], "w");
					if (fppid == NULL) {
						snprintf(error_msg, sizeof(error_msg),
								"Could not open pidfile '%s': %s",
								macro_x[CONFIG_OPT_PIDFILE], strerror(errno));
						error = TRUE;
					} else {
						fclose(fppid);
					}
				}
			}

			else if (!strcmp(variable, "sleep_time")) {

				if (strlen(value) == 1) {
					if (isdigit((int)value[strlen(value)-1]) == FALSE) {
						strcpy(error_msg, "sleep_time should be an integer");
						error = TRUE;
						break;
					}
				}

				/* save the macro */
				if (macro_x[CONFIG_OPT_SLEEPTIME] != NULL)
					free(macro_x[CONFIG_OPT_SLEEPTIME]);

				macro_x[CONFIG_OPT_SLEEPTIME] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_MAXTHREADS] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro SLEEPTIME");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_SLEEPTIME]);
			}

			else if (!strcmp(variable, "identify_npcd")) {

				if (strlen(value) == 1) {
					if (isdigit((int)value[strlen(value)-1]) == FALSE) {
						strcpy(error_msg, "identify_npcd should be an integer");
						error = TRUE;
						break;
					}
				}
				/* save the macro */
				if (macro_x[CONFIG_OPT_IDENTMYSELF] != NULL)
					free(macro_x[CONFIG_OPT_IDENTMYSELF]);

				macro_x[CONFIG_OPT_IDENTMYSELF] = (char *) strdup(value);

				if (macro_x[CONFIG_OPT_IDENTMYSELF] == NULL) {
					strcpy(error_msg,
							"Could not allocate memory for macro IDENTMYSELF");
					error = TRUE;
					break;
				}
				pnp_strip(macro_x[CONFIG_OPT_IDENTMYSELF]);
			}

			else {
				strcpy(error_msg,
						"There is a config directive that I don't know");
				error = TRUE;
				break;
			}
		}
	}

	if (fh_config_file != NULL)
		fclose(fh_config_file);

	if (error) {
		printf(
				"An Error occured while reading your config on line %d\nMessage was: \"%s\"\n",
				line_number, error_msg);
		exit(EXIT_FAILURE);
	}
}

/************************************
 *
 * check if we have all we need
 *
 * **********************************/
int check_needed_config_options() {
	/* Needed config options are:
	 * logtype
	 * 	if syslog: ignore logfile
	 * 	if file: logfile is needed
	 * command
	 *  if command args: command is needed
	 */
	char error_msg[MAX_BUFFER_SIZE];
	int error = FALSE;
	int warning = FALSE;

	if (macro_x[CONFIG_OPT_LOGTYPE] == NULL) {
		strcpy(error_msg, "You have to define a logtype.");
		error = TRUE;
	} else if ((strcmp(macro_x[CONFIG_OPT_LOGTYPE], "file") == 0)
			&& (macro_x[CONFIG_OPT_LOGFILE] == NULL)) {
		strcpy(error_msg,
				"You have to define a logfile if you wish to use a file for logging.");
		error = TRUE;
	} else if (macro_x[CONFIG_OPT_RUNCMD_ARG] != NULL
			&& macro_x[CONFIG_OPT_RUNCMD] == NULL) {
		strcpy(error_msg, "There should no argument to no command.");
		error = TRUE;
	} else if (macro_x[CONFIG_OPT_RUNCMD] == NULL) {
		strcpy(error_msg,
				"There is nothing I can do - please give me a 'perfdata_file_run_cmd'.");
		error = TRUE;
	} else if (macro_x[CONFIG_OPT_SCANDIR] == NULL) {
		strcpy(error_msg,
				"You should define a performance data spool directory.");
		warning = TRUE;
	}
	if (error) {
		printf("ERROR - %s\n", error_msg);
		return ERROR;
	} else if (warning) {
		printf("WARNING - %s\n", error_msg);
		return OK;
	} else
		return OK;
}

/* Prepare config file variables to program vars */
int prepare_vars() {
	if (macro_x[CONFIG_OPT_MAXTHREADS] != NULL) {
		max_threads = atoi(macro_x[CONFIG_OPT_MAXTHREADS]);
	} else
		max_threads = 5;

	if (macro_x[CONFIG_OPT_LOAD] != NULL) {
		load_threshold = strtod(macro_x[CONFIG_OPT_LOAD], NULL);
		if (load_threshold != 0.0)
			use_load_threshold = TRUE;
	} else {
		load_threshold = 0.0;
		use_load_threshold = FALSE;
	}

	if (macro_x[CONFIG_OPT_RUNCMD] != NULL) {
		command = macro_x[CONFIG_OPT_RUNCMD];
	}

	if (macro_x[CONFIG_OPT_RUNCMD_ARG] != NULL) {
		command_args = macro_x[CONFIG_OPT_RUNCMD_ARG];
	}

	if (macro_x[CONFIG_OPT_USER] != NULL) {
		user = macro_x[CONFIG_OPT_USER];
	} else
		user = "nagios";

	if (macro_x[CONFIG_OPT_GROUP] != NULL) {
		group = macro_x[CONFIG_OPT_GROUP];
	} else
		group = "nagios";

	if (macro_x[CONFIG_OPT_SCANDIR] != NULL) {
		directory = macro_x[CONFIG_OPT_SCANDIR];
	} else {
		directory = "/usr/local/nagios/var/spool/perfdata/";
		printf(
				"WARNING - Adapting a hardcoded default perfdata spooldir - '%s'\n",
				directory);
	}

	if (macro_x[CONFIG_OPT_PIDFILE] != NULL) {
		pidfile = macro_x[CONFIG_OPT_PIDFILE];
	} else
		pidfile = "/var/run/npcd.pid";

	if (macro_x[CONFIG_OPT_LOGLEVEL] != NULL) {
		loglevel = atoi(macro_x[CONFIG_OPT_LOGLEVEL]);
	} else
		loglevel = 0;

	if (macro_x[CONFIG_OPT_SLEEPTIME] != NULL) {
		sleeptime = atoi(macro_x[CONFIG_OPT_SLEEPTIME]);
	} else
		sleeptime = 15;

	if (macro_x[CONFIG_OPT_IDENTMYSELF] != NULL) {
		identmyself = atoi(macro_x[CONFIG_OPT_IDENTMYSELF]);
	} else
		identmyself = TRUE;

	return OK;
}
