// Copyright (C) 2007-2009 Hendrik Baecker <andurin@process-zero.de>
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

#include <stdio.h>
#include <sys/time.h>
#include "../include/config.h"
#include "../include/pnp.h"

int write_log(char *);

extern char* log_file;
extern int use_syslog;
extern int loglevel;
extern int max_logfile_size;

int do_log(char *message) {
	if (use_syslog) {
		syslog(LOG_NOTICE, "%s", message);
		return OK;
	} else if (use_syslog == FALSE) {
		if (write_log(message)==0)
			return OK;
		else
			return ERROR;
	}
	return OK;
}

int write_log(char *message) {
	int fd;
	long filelen;
	struct timeval tv;
	char temp_buffer[2048];

	time_t curtime;

	gettimeofday(&tv, NULL);
	curtime = tv.tv_sec;

	strftime(temp_buffer, sizeof(temp_buffer) - 1, "[%m-%d-%Y %T] NPCD: ",
			localtime(&curtime));
	strcat(temp_buffer, message);

	/* open / create logfile */
	if ((fd = open(log_file, O_WRONLY | O_CREAT | O_APPEND, S_IRUSR | S_IWUSR
			| S_IRGRP | S_IROTH)) == -1) {
		printf("Cannot open log file %s\n", log_file);
		return 0;
	}
	/* write log message */
	if (write(fd, temp_buffer, strlen(temp_buffer)) != strlen(temp_buffer)) {
		perror("NPCD: Error writing to log file");
		close(fd);
		return 0;
	}

	/* rotate logfile if size > max_logfile_size */
	if ((filelen = lseek(fd, 0L, SEEK_END)) > max_logfile_size) {
		/* LOG(0, "Begin Logrotation!\n"); */
		char buffer[PATH_MAX];
		close(fd);

		/* delete .old log file */
		strncpy(buffer, log_file, sizeof(buffer) - 1);
		strncat(buffer, ".old", sizeof(buffer) - 1);
		unlink(buffer);

		/* rename log_file to log_file.old */
		if (rename(log_file, buffer) != 0) {
			snprintf(buffer, sizeof(buffer) - 1, "Error rename() logfile - %s",
					strerror(errno));
			LOG(0, buffer);
			//perror("Error renaming logfile\n");
			return 0;
		}
		LOG(0, "Logfile rotated!\n");
	} else {
		close(fd);
	}
	return filelen;
}
