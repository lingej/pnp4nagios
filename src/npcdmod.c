/*****************************************************************************
 *
 * NPCDMOD.C
 *
 * Copyright (c) 2008-2010 Hendrik Baecker (http://www.pnp4nagios.org)
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
 *
 * Last Modified: 07-30-2010
 *
 *****************************************************************************/

/* include (minimum required) event broker header files */
#include "../include/nebmodules.h"
#include "../include/nebcallbacks.h"

/* include other event broker header files that we need for our work */
#include "../include/nebstructs.h"
#include "../include/broker.h"

/* include some Nagios stuff as well */
#include "../include/config.h"
#include "../include/common.h"
#include "../include/nagios.h"

/* include some pnp stuff */
#include "../include/pnp.h"
#include "../include/npcdmod.h"

/* specify event broker API version (required) */
NEB_API_VERSION(CURRENT_NEB_API_VERSION);

extern int process_performance_data;

FILE *fp = NULL;

void *npcdmod_module_handle = NULL;
char *perfdata_file = "/usr/local/nagios/var/perfdata";
char *perfdata_spool_filename = "perfdata";
char *spool_dir = NULL;
char *perfdata_file_processing_interval = "15";

void npcdmod_file_roller();
int npcdmod_handle_data(int, void *);

int npcdmod_process_config_var(char *arg);
int npcdmod_process_module_args(char *args);

/* this function gets called when the module is loaded by the event broker */
int nebmodule_init(int flags, char *args, nebmodule *handle) {
	char temp_buffer[1024];
	time_t current_time;
	//unsigned long interval;

	/* save our handle */
	npcdmod_module_handle = handle;

	/* set some info - this is completely optional, as Nagios doesn't do anything with this data */
	neb_set_module_info(npcdmod_module_handle, NEBMODULE_MODINFO_TITLE, "npcdmod");
	neb_set_module_info(npcdmod_module_handle, NEBMODULE_MODINFO_AUTHOR, "Hendrik Baecker");
	neb_set_module_info(npcdmod_module_handle, NEBMODULE_MODINFO_TITLE, "Copyright (c) 2008-2009 Hendrik Baecker");
	neb_set_module_info(npcdmod_module_handle, NEBMODULE_MODINFO_VERSION, "0.0.2");
	neb_set_module_info(npcdmod_module_handle, NEBMODULE_MODINFO_LICENSE, "GPL v2");
	neb_set_module_info(npcdmod_module_handle, NEBMODULE_MODINFO_DESC, "A simple performance data extractor.");

	/* log module info to the Nagios log file */
	write_to_all_logs("npcdmod: Copyright (c) 2008-2009 Hendrik Baecker (andurin@process-zero.de) - http://www.pnp4nagios.org", NSLOG_INFO_MESSAGE);

	if (process_performance_data == FALSE) {
		write_to_all_logs("npcdmod: I can not work with disabled performance data in nagios.cfg.", NSLOG_INFO_MESSAGE);
		write_to_all_logs("npcdmod: Please enable it with 'process_performance_data=1' in nagios.cfg", NSLOG_INFO_MESSAGE);
		return -1;
	}

	/* process arguments */
	if (npcdmod_process_module_args(args) == ERROR) {
		write_to_all_logs("npcdmod: An error occurred while attempting to process module arguments.", NSLOG_INFO_MESSAGE);
		return -1;
	}

	/* de-initialize if there is no perfdata file nor spool dir */
	if (spool_dir == NULL || perfdata_file == NULL) {
		write_to_all_logs("npcdmod: An error occurred process your config file. Check your perfdata_file or perfdata_spool_dir.", NSLOG_INFO_MESSAGE);
		return -1;
	}

	/* Log some health data */
	snprintf(temp_buffer, sizeof(temp_buffer) - 1, "npcdmod: spool_dir = '%s'.", spool_dir);
	temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
	write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);

	snprintf(temp_buffer, sizeof(temp_buffer) - 1, "npcdmod: perfdata file '%s'.", perfdata_file);
	temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
	write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);

	/* open perfdata_file to write perfdata in it */
	if ((fp = fopen(perfdata_file, "a")) == NULL) {
		snprintf(temp_buffer, sizeof(temp_buffer) - 1,
				"npcdmod: Could not open file. %s", strerror(errno));
		temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
		write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);
		return -1;
	}

	/* log a message to the Nagios log file that we're ready */
	snprintf(temp_buffer, sizeof(temp_buffer) - 1,
			"npcdmod: Ready to run to have some fun!\n");
	temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
	write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);

	/* register for a 15 seconds file move event */
	time(&current_time);
	//interval = 15;
	schedule_new_event(EVENT_USER_FUNCTION,TRUE, current_time + atoi(perfdata_file_processing_interval), TRUE,
	atoi(perfdata_file_processing_interval), NULL, TRUE, (void *) npcdmod_file_roller, "", 0);

	/* register to be notified of certain events... */
	neb_register_callback(NEBCALLBACK_HOST_CHECK_DATA, npcdmod_module_handle,
			0, npcdmod_handle_data);
	neb_register_callback(NEBCALLBACK_SERVICE_CHECK_DATA,
	npcdmod_module_handle, 0, npcdmod_handle_data);
	return 0;
}

/* this function gets called when the module is unloaded by the event broker */
int nebmodule_deinit(int flags, int reason) {
	char temp_buffer[1024];

	/* deregister for all events we previously registered for... */
	neb_deregister_callback(NEBCALLBACK_HOST_CHECK_DATA,npcdmod_handle_data);
	neb_deregister_callback(NEBCALLBACK_SERVICE_CHECK_DATA,npcdmod_handle_data);

	/* log a message to the Nagios log file */
	snprintf(temp_buffer, sizeof(temp_buffer) - 1,
			"npcdmod: If you don't like me, I will go out! Bye.\n");
	temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
	write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);

	return 0;
}

/* gets called every X seconds by an event in the scheduling queue */
void npcdmod_file_roller() {
	char temp_buffer[1024];
	char spool_file[1024];
	int result = 0;
	time_t current_time;

	time(&current_time);

	sprintf(spool_file, "%s/%s.%d", spool_dir, perfdata_spool_filename, (int)current_time);
	spool_file[sizeof(spool_file) - 1] = '\x0';

	/* close actual file */
	fclose(fp);

	/* move the original file */
	result = my_rename(perfdata_file, spool_file);

	/* open a new file */
	if ((fp = fopen(perfdata_file, "a")) == NULL) {
		snprintf(temp_buffer, sizeof(temp_buffer) - 1,
				"npcdmod: Could not reopen file. %s", strerror(errno));
		temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
		write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);
	}

	return;
}

/* handle data from Nagios daemon */
int npcdmod_handle_data(int event_type, void *data) {
	nebstruct_host_check_data *hostchkdata = NULL;
	nebstruct_service_check_data *srvchkdata = NULL;

	host *host=NULL;
	service *service=NULL;

	char temp_buffer[1024];
	char perfdatafile_template[PERFDATA_BUFFER];
    int written;


	/* what type of event/data do we have? */
	switch (event_type) {

	case NEBCALLBACK_HOST_CHECK_DATA:
		/* an aggregated status data dump just started or ended... */
		if ((hostchkdata = (nebstruct_host_check_data *) data)) {

			host = find_host(hostchkdata->host_name);

                        if(host->process_performance_data == 0) {
                            break;
                        }

			/* Do some Debuglog */
			/*
			snprintf(temp_buffer, sizeof(temp_buffer) - 1,  "npcdmod: DEBUG >>> %s\n",
			host->host_check_command);

			temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
			write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);
			 */

			if (hostchkdata->type == NEBTYPE_HOSTCHECK_PROCESSED
				&& hostchkdata->perf_data != NULL) {
				written = snprintf(perfdatafile_template, PERFDATA_BUFFER,
					"DATATYPE::HOSTPERFDATA\t"
					"TIMET::%d\t"
					"HOSTNAME::%s\t"
					"HOSTPERFDATA::%s\t"
					"HOSTCHECKCOMMAND::%s!%s\t"
					"HOSTSTATE::%d\t"
					"HOSTSTATETYPE::%d\n", (int)hostchkdata->timestamp.tv_sec,
						hostchkdata->host_name, hostchkdata->perf_data,
						hostchkdata->command_name, hostchkdata->command_args,
						hostchkdata->state, hostchkdata->state_type);

                if (written >= PERFDATA_BUFFER) {
                    snprintf(temp_buffer, sizeof(temp_buffer) - 1,
                        "npcdmod: Buffer size of %d in npcdmod.h is too small, ignoring data for %s\n", PERFDATA_BUFFER, hostchkdata->host_name);
                    temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
                    write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);
                } else {
                    fputs(perfdatafile_template, fp);
                }
			}
		}
		break;

	case NEBCALLBACK_SERVICE_CHECK_DATA:
		/* an aggregated status data dump just started or ended... */
		if ((srvchkdata = (nebstruct_service_check_data *) data)) {

			if (srvchkdata->type == NEBTYPE_SERVICECHECK_PROCESSED
					&& srvchkdata->perf_data != NULL) {

				/* find the nagios service object for this service */
				service = find_service(srvchkdata->host_name, srvchkdata->service_description);

                                if(service->process_performance_data == 0) {
                                    break;
                                }

				/* Do some Debuglog */
				/*
				snprintf(temp_buffer, sizeof(temp_buffer) - 1,  "npcdmod: DEBUG >>> %s\n",
						service->service_check_command);

				temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
				write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);
				*/

				written = snprintf(perfdatafile_template, PERFDATA_BUFFER,
					"DATATYPE::SERVICEPERFDATA\t"
					"TIMET::%d\t"
					"HOSTNAME::%s\t"
					"SERVICEDESC::%s\t"
					"SERVICEPERFDATA::%s\t"
					"SERVICECHECKCOMMAND::%s\t"
					"SERVICESTATE::%d\t"
					"SERVICESTATETYPE::%d\n", (int)srvchkdata->timestamp.tv_sec,
						srvchkdata->host_name, srvchkdata->service_description,
						srvchkdata->perf_data, service->service_check_command,
						srvchkdata->state, srvchkdata->state_type);

                if (written >= PERFDATA_BUFFER) {
                    snprintf(temp_buffer, sizeof(temp_buffer) - 1,
                        "npcdmod: Buffer size of %d in npcdmod.h is too small, ignoring data for %s / %s\n", PERFDATA_BUFFER, srvchkdata->host_name, srvchkdata->service_description);
                    temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
                    write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);
                } else {
                    fputs(perfdatafile_template, fp);
                }
			}
		}
		break;

	default:
		break;
	}


	return 0;
}

/****************************************************************************/
/* CONFIG FUNCTIONS                                                         */
/****************************************************************************/

/* process arguments that were passed to the module at startup */
int npcdmod_process_module_args(char *args) {
	char *ptr = NULL;
	char **arglist = NULL;
	char **newarglist = NULL;
	int argcount = 0;
	int memblocks = 64;
	int arg = 0;

	if (args == NULL)
		return OK;

	/* get all the var/val argument pairs */

	/* allocate some memory */
	if ((arglist = (char **) malloc(memblocks * sizeof(char **))) == NULL)
		return ERROR;

	/* process all args */
	ptr = strtok(args, ",");
	while (ptr) {

		/* save the argument */
		arglist[argcount++] = strdup(ptr);

		/* allocate more memory if needed */
		if (!(argcount % memblocks)) {
			if ((newarglist = (char **) realloc(arglist, (argcount + memblocks)
					* sizeof(char **))) == NULL) {
				for (arg = 0; arg < argcount; arg++)
					free(arglist[argcount]);
				free(arglist);
				return ERROR;
			} else
				arglist = newarglist;
		}

		ptr = strtok(NULL, ",");
	}

	/* terminate the arg list */
	arglist[argcount] = '\x0';

	/* process each argument */
	for (arg = 0; arg < argcount; arg++) {
		if (npcdmod_process_config_var(arglist[arg]) == ERROR) {
			for (arg = 0; arg < argcount; arg++)
				free(arglist[arg]);
			free(arglist);
			return ERROR;
		}
	}

	/* free allocated memory */
	for (arg = 0; arg < argcount; arg++)
		free(arglist[arg]);
	free(arglist);

	return OK;
}

/* process all config vars in a file */
int npcdmod_process_config_file(char *filename) {
	pnp_mmapfile *thefile = NULL;
	char *buf = NULL;
	char temp_buffer[1024];
	int result = OK;

	/* open the file */
	if ((thefile = pnp_mmap_fopen(filename)) == NULL) {
		snprintf(temp_buffer, sizeof(temp_buffer) - 1,
				"npcdmod ERROR: failed to open %s\n", filename);
		temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
		write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);
		return ERROR;

	} else {
		snprintf(temp_buffer, sizeof(temp_buffer) - 1,
				"npcdmod: %s initialized\n", filename);
		temp_buffer[sizeof(temp_buffer) - 1] = '\x0';
		write_to_all_logs(temp_buffer, NSLOG_INFO_MESSAGE);
	}

	/* process each line of the file */
	while ((buf = pnp_mmap_fgets(thefile))) {

		/* skip comments */
		if (buf[0] == '#') {
			free(buf);
			continue;
		}

		/* skip blank lines */
		if (!strcmp(buf, "")) {
			free(buf);
			continue;
		}

		/* skip new lines */
		if (!strcmp(buf, "\n")) {
			free(buf);
			continue;
		}
		/* process the variable */
		result = npcdmod_process_config_var(buf);

		/* free memory */
		free(buf);

		if (result != OK)
			break;
	}

	/* close the file */
	pnp_mmap_fclose(thefile);

	return result;
}

/* process a single module config variable */
int npcdmod_process_config_var(char *arg) {
	char *var = NULL;
	char *val = NULL;

	/* split var/val */
	var = strtok(arg, "=");
	val = strtok(NULL, "\n");

	/* skip incomplete var/val pairs */
	if (var == NULL || val == NULL)
		return OK;

	/* strip var/val */
	strip(var);
	strip(val);

	/* process the variable... */
	if (!strcmp(var, "config_file"))
		npcdmod_process_config_file(val);

	else if (!strcmp(var, "perfdata_spool_dir"))
		spool_dir = strdup(val);

	else if (!strcmp(var, "perfdata_file"))
		perfdata_file = strdup(val);

	else if (!strcmp(var, "perfdata_spool_filename"))
		perfdata_spool_filename = strdup(val);

	else if (!strcmp(var, "perfdata_file_processing_interval"))
		perfdata_file_processing_interval = strdup(val);

	else if (!strcmp(var, "user"))
		;
	else if (!strcmp(var, "group"))
		;
	else if (!strcmp(var, "log_type"))
		;
	else if (!strcmp(var, "log_file"))
		;
	else if (!strcmp(var, "max_logfile_size"))
		;
	else if (!strcmp(var, "log_level"))
		;
	else if (!strcmp(var, "perfdata_file_run_cmd"))
		;
	else if (!strcmp(var, "perfdata_file_run_cmd_args"))
		;
	else if (!strcmp(var, "identify_npcd"))
		;
	else if (!strcmp(var, "npcd_max_threads"))
		;
	else if (!strcmp(var, "sleep_time"))
		;
	else if (!strcmp(var, "load_threshold"))
		;
	else if (!strcmp(var, "pid_file"))
		;
	else
		return ERROR;

	return OK;
}

/**************************************************************/
/****** MMAP()'ED FILE FUNCTIONS ******************************/
/**************************************************************/

/* open a file read-only via mmap() */
pnp_mmapfile *pnp_mmap_fopen(char *filename) {
	pnp_mmapfile *new_mmapfile;
	int fd;
	void *mmap_buf;
	struct stat statbuf;
	int mode = O_RDONLY;

	/* allocate memory */
	if ((new_mmapfile = (pnp_mmapfile *) malloc(sizeof(pnp_mmapfile))) == NULL)
		return NULL;

	/* open the file */
	if ((fd = open(filename, mode)) == -1) {
		free(new_mmapfile);
		return NULL;
	}

	/* get file info */
	if ((fstat(fd, &statbuf)) == -1) {
		close(fd);
		free(new_mmapfile);
		return NULL;
	}

	/* mmap() the file */
	if ((mmap_buf = (void *) mmap(0, statbuf.st_size, PROT_READ, MAP_PRIVATE,
			fd, 0)) == MAP_FAILED) {
		close(fd);
		free(new_mmapfile);
		return NULL;
	}

	/* populate struct info for later use */
	/*new_mmapfile->path=strdup(filename);*/
	new_mmapfile->path = NULL;
	new_mmapfile->fd = fd;
	new_mmapfile->file_size = (unsigned long) (statbuf.st_size);
	new_mmapfile->current_position = 0L;
	new_mmapfile->current_line = 0L;
	new_mmapfile->mmap_buf = mmap_buf;

	return new_mmapfile;
}

/* close a file originally opened via mmap() */
int pnp_mmap_fclose(pnp_mmapfile *temp_mmapfile) {

	if (temp_mmapfile == NULL)
		return ERROR;

	/* un-mmap() the file */
	munmap(temp_mmapfile->mmap_buf, temp_mmapfile->file_size);

	/* close the file */
	close(temp_mmapfile->fd);

	/* free memory */
	if (temp_mmapfile->path != NULL)
		free(temp_mmapfile->path);
	free(temp_mmapfile);

	return OK;
}

/* gets one line of input from an mmap()'ed file */
char *pnp_mmap_fgets(pnp_mmapfile *temp_mmapfile) {
	char *buf = NULL;
	unsigned long x = 0L;
	int len = 0;

	if (temp_mmapfile == NULL)
		return NULL;

	/* we've reached the end of the file */
	if (temp_mmapfile->current_position >= temp_mmapfile->file_size)
		return NULL;

	/* find the end of the string (or buffer) */
	for (x = temp_mmapfile->current_position; x < temp_mmapfile->file_size; x++) {
		if (*((char *) (temp_mmapfile->mmap_buf) + x) == '\n') {
			x++;
			break;
		}
	}

	/* calculate length of line we just read */
	len = (int) (x - temp_mmapfile->current_position);

	/* allocate memory for the new line */
	if ((buf = (char *) malloc(len + 1)) == NULL) {
		write_to_all_logs("could not allocate a new buf", NSLOG_INFO_MESSAGE);
		return NULL;
	}

	/* copy string to newly allocated memory and terminate the string */
	memcpy(buf, ((char *) (temp_mmapfile->mmap_buf)
			+ temp_mmapfile->current_position), len);
	buf[len] = '\x0';

	/* update the current position */
	temp_mmapfile->current_position = x;

	/* increment the current line */
	temp_mmapfile->current_line++;

	return buf;
}

