// Copyright (C) 2006-2009 Hendrik Baecker <andurin@process-zero.de>
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

extern int prepare_vars();

extern int thread_counter;
extern int max_threads;
extern int daemon_mode;
extern int loglevel;
extern int use_syslog;
int sighup_detected = FALSE;

extern char** command;
extern char *command_args;
extern char *user;
extern char *group;
extern char *directory;
extern char *macro_x;
extern char *config_file;
extern char buffer[MAX_LOGMESSAGE_SIZE];

extern void process_configfile(char *);
extern int check_needed_config_options(void);

typedef void (*sighandler_t)(int);

// This function has special copyright!!!
/* Copyright (c) 1999-2006 Ethan Galstad (nagios@nagios.org) */
/* strip newline, carriage return, and tab characters from beginning and end of a string */
void pnp_strip(char *buffer) {
	register int x;
	register int y;
	register int z;

	if (buffer == NULL || buffer[0] == '\x0')
		return;

	/* strip end of string */
	y = (int) strlen(buffer);
	for (x = y - 1; x >= 0; x--) {
		if (buffer[x] == ' ' || buffer[x] == '\n' || buffer[x] == '\r'
				|| buffer[x] == '\t' || buffer[x] == 13)
			buffer[x] = '\x0';
		else
			break;
	}

	/* strip beginning of string (by shifting) */
	y = (int) strlen(buffer);
	for (x = 0; x < y; x++) {
		if (buffer[x] == ' ' || buffer[x] == '\n' || buffer[x] == '\r'
				|| buffer[x] == '\t' || buffer[x] == 13)
			continue;
		else
			break;
	}
	if (x > 0) {
		for (z = x; z < y; z++)
			buffer[z - x] = buffer[z];
		buffer[y - x] = '\x0';
	}

	return;
}

/******************************************************************/
/*********************** SECURITY FUNCTIONS ***********************/
/******************************************************************/
/* drops privileges */
int drop_privileges(char *user, char *group) {
	uid_t uid = -1;
	gid_t gid = -1;
	struct group *grp = NULL;
	struct passwd *pw = NULL;
	int result = OK;

	/* only drop privileges if we're running as root, so we don't interfere with being debugged while running as some random user */
	if (getuid() != 0)
		return OK;

	/* set effective group ID */
	if (group != NULL) {

		/* see if this is a group name */
		if (strspn(group, "0123456789") < strlen(group)) {
			grp = (struct group *) getgrnam(group);
			if (grp != NULL) {
				gid = (gid_t) (grp->gr_gid);
			} else {
				printf("Warning: Could not get group entry for '%s'\n", group);
			}
		}

		/* else we were passed the GID */
		else
			gid = (gid_t) atoi(group);

		/* set effective group ID if other than current EGID */
		if (gid != getegid()) {

			if (setgid(gid) == -1) {
				printf("Warning: Could not set effective GID=%d\n", (int) gid);
				result = ERROR;
			}
		}
	}

	/* set effective user ID */
	if (user != NULL) {

		/* see if this is a user name */
		if (strspn(user, "0123456789") < strlen(user)) {
			pw = (struct passwd *) getpwnam(user);
			if (pw != NULL)
				uid = (uid_t) (pw->pw_uid);
			else {
				printf("Warning: Could not get passwd entry for '%s'\n", user);
			}
		}

		/* else we were passed the UID */
		else
			uid = (uid_t) atoi(user);

#ifdef HAVE_INITGROUPS

		if(uid!=geteuid()) {

			/* initialize supplementary groups */
			if(initgroups(user,gid)==-1) {
				if(errno==EPERM) {
					printf("Warning: Unable to change supplementary groups using initgroups() -- I hope you know what you're doing\n");
				}
				else {
					printf("Warning: Possibly root user failed dropping privileges with initgroups()\n");
					return ERROR;
				}
			}
		}
#endif
		if (setuid(uid) == -1) {
			printf("Warning: Could not set effective UID=%d\n", (int) uid);
			result = ERROR;
		}
	}
	return result;
}

/*******************
 *
 * Signal functions
 *
 *******************/

sighandler_t handle_signal(int sig_nr, sighandler_t signalhandler) {
	struct sigaction new_sig, old_sig;

	new_sig.sa_handler = signalhandler;
	sigemptyset(&new_sig.sa_mask);
	new_sig.sa_flags = SA_RESTART;
	if (sigaction(sig_nr, &new_sig, &old_sig) < 0)
		return SIG_ERR;
	return old_sig.sa_handler;
}

void check_sig(int signr) {
	char buffer[MAX_LOGMESSAGE_SIZE];

	switch (signr) {
	case SIGINT:
		LOG(0, "Caught SIGINT - Good bye\n");
		exit(EXIT_SUCCESS);
		break;

	case SIGTERM:
		LOG(0, "Caught Termination Signal - Astalavista... baby\n");
		exit(EXIT_SUCCESS);
		break;

	case SIGHUP:
		LOG(0, "Caught SIGHUP - reloading configuration\n");
		sighup_detected = TRUE;

		process_configfile(config_file);
		if (check_needed_config_options() != 0) {
			LOG(0, "There is an error in the config! Exiting...\n");
			exit(EXIT_FAILURE);
		}
		prepare_vars();
		LOG(0, "Configuration reload succesfull.\n");

		break;

	default:
		snprintf(buffer, sizeof(buffer - 1),
				"Caught the Signal '%d' but don't care about this.\n", signr);
		LOG(2, buffer);
		break;
	}
}

/* This won't compile on Solaris and HP UX */

#ifdef HAVE_STRUCT_DIRENT_D_TYPE
int is_file(const struct dirent *d) {
	if (d->d_type == DT_REG)
		return 1;

	//free(d);
	return 0;
}
#endif

