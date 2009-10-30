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
#include <sys/types.h>
#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <signal.h>

#ifdef HAVE_GETOPT_H
#include <getopt.h>
#endif

#include<unistd.h>
#include<sys/types.h>
#include<sys/socket.h>
#include<netinet/in.h>

#define BUFFER_SIZE 255
#define DEFAULT_SERVERPORT	5661 	/* Ein Port der auch von 'gewhnlichen' Benutzern verwendet werden darf */
#define MSG_SIZE	8192		/* Die maximale Anzahl an Zeichen, die 'msg' enhalten darf */

#define OPEN 	1
#define CLOSE	0

#define TRUE 	1
#define FALSE 	0

#define OK	0
#define ERROR	-1

struct Nagios_Env {
	char varname[128];
	char varvalue[128];
	struct Nagios_Env *next;
};

/* function prototypes */
void print_list(int, struct Nagios_Env *);
int parse_env(char *, struct Nagios_Env **);
int process_env(char *[]);
int open_sock();
int process_arguments(int, char **);

void timeout_sighandler(int);

/* variable declaration*/
pid_t fork(void);
int socket_status = CLOSE;
int serverport = DEFAULT_SERVERPORT;
int timeout = 10; /* Default time out to 10 seconds */
char *serverip, *datatype;
char msg[MSG_SIZE]; /* Buffer for sending the message to remote server */

int main(int argc, char *argv[], char *envp[]) {

	if (process_arguments(argc, argv) == ERROR)
		exit(ERROR);

	pid_t pid;
	int error_code = 0;
	switch (pid = fork()) {
	case -1:
		/* Here pid is -1, the fork failed */
		/* Some possible reasons are that you're */
		/* out of process slots or virtual memory */
		perror("The fork failed!");
		error_code = -1;
		break;

	case 0:
		/* pid of zero is the child */
		signal(SIGALRM, timeout_sighandler);
		alarm(timeout);
		process_env(envp);
		_exit(0);

	default:
		/* pid greater than zero is parent getting the child's pid */
		//printf("Child's pid is %d\n",pid);
		error_code = 0;
	}
	exit(error_code);
}

void timeout_sighandler(int sig) {

	/* force the child process to exit... */
	fprintf(stderr, "Caught SIGALRM - timeout\n");
	_exit(-1);
}

/***************************************************
 * processing command line arguments
 *
 * Gets number of args and pointer reference to argv
 ***************************************************/
int process_arguments(int argc, char **argv) {
	int c;
	int error = FALSE;
	int test_mode = FALSE;
	int display_license = FALSE;
	int display_help = FALSE;

#ifdef HAVE_GETOPT_H
	int option_index=0;
	static struct option long_options[]= {
		{	"help",no_argument,0,'h'},
		{	"version",no_argument,0,'V'},
		{	"license",no_argument,0,'V'},
		{	"test",no_argument,0,'t'},
		{	"host",required_argument,0,'H'},
		{	"port",required_argument,0,'p'},
		{	"datatype",required_argument,0,'d'},
		{	"timeout",required_argument,0,'t'},
		{	0,0,0,0}
	};
#endif

	/* make sure we have the correct number of command line arguments */
	if (argc < 2)
		error = TRUE;

	while (1) {

#ifdef HAVE_GETOPT_H
		c=getopt_long(argc,argv,"+hH:p:d:Vt:",long_options,&option_index);
#else
		c = getopt(argc, argv, "+hH:p:d:Vt:");
#endif

		if (c == -1 || c == EOF)
			break;

		switch (c) {

		case '?': /* usage */
		case 'h':
			display_help = TRUE;
			break;

		case 'V': /* version */
			display_license = TRUE;
			break;

		case 'H': /* host ip */
			// ToDo: Check if it there
			if (optarg != NULL) {
				serverip = optarg;
			} else
				error = TRUE;
			break;

		case 'p': /* port to use */
			// ToDo: Check for port
			if (optarg != NULL)
				serverport = atoi(optarg);
			break;

		case 'd': /* datatype like hostperfdata, serviceperfdata and so on */
			if (optarg != NULL)
				datatype = optarg;
			else
				error = TRUE;
			break;

		case 't': /* timeout */
			if (optarg != NULL)
				timeout = atoi(optarg);
			else
				error = TRUE;
			break;

		default:
			break;
		}

	}

	/* it makes no sense to do anything without a target host*/
	if (serverip == NULL)
		error = TRUE;

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
				"Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA.\n\n");

		exit(OK);
	}

	/* if there are no command line options (or if we encountered an error), print usage */
	if (error == TRUE || display_help == TRUE) {
		printf(
				"Usage: %s -H <serverip> [-p <serverport>] -d <datatype> -t <timeout>\n",
				argv[0]);
		printf("\n");
		printf("Options:\n");
		printf("\n");
		printf("  -H | --host     Host IP Address to send data to.\n");
		printf("\n");
		printf(
				"  -p | --port     Host Port to send data to. (Default: tcp/1500)\n");
		printf("\n");
		printf(
				"  -t | --timeout  Timeout value to kill process (Default: 10 seconds)\n");
		printf("\n");
		printf("  -d | --datatype Free string what this Data stands for\n");
		printf(
				"                  like \"serviceperfdata\", \"hostperfdata\", \"eventhandler\"\n");
		printf("\n\n");
		printf(
				"Visit the PNP website at http://www.pnp4nagios.org/pnp/ for bug fixes, new\n");
		printf(
				"releases, online documentation, FAQs, information on subscribing to\n");
		printf("the mailing lists.\n");
		printf("\n");

		exit(ERROR);
	}
	return OK;
}

int process_env(char *envp[]) {
	int count = 0;
	int socket;
	struct Nagios_Env *base = NULL;
	struct Nagios_Env *ptr = NULL;
	while (1) {
		if (socket_status == CLOSE) {
			socket = open_sock(serverip);
		}
		if (envp[count] == NULL) {
			break;
		}
		parse_env(envp[count], &base);
		count++;
	}
	print_list(socket, base);
	return (0);
}

void print_list(int socket, struct Nagios_Env *base) {
	struct Nagios_Env *ptr = NULL;
	char *message, dtype[BUFFER_SIZE];
	int message_length = sizeof(struct Nagios_Env) - sizeof(ptr->next);
	ptr = base;
	strcat(dtype,
			"<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n");
	strcat(dtype, "<NAGIOS>\n");
	strcat(dtype, "  <NAGIOS_DATATYPE>");
	strcat(dtype, datatype);
	strcat(dtype, "</NAGIOS_DATATYPE>\n");
	if (send(socket, dtype, strlen(dtype), 0) == -1) {
		fprintf(stderr, "error while sending request to server\n");
	}
	bzero((void *) dtype, BUFFER_SIZE);

	while (ptr != NULL) {
		if (!(message = (char *) malloc(sizeof(struct Nagios_Env)))) {
			fprintf(stderr, "Cannot allocate memory for this message\n");
			exit(ERROR);
		}
		strncat(message, "  <", message_length);
		strncat(message, ptr->varname, message_length);
		strncat(message, ">", message_length);
		strncat(message, ptr->varvalue, message_length);
		strncat(message, "</", message_length);
		strncat(message, ptr->varname, message_length);
		strncat(message, ">\n", message_length);
		message[message_length] = '\0';
		if (send(socket, message, strlen(message), 0) == -1) {
			fprintf(stderr, "error while sending request to server\n");
		}
		ptr = ptr->next;
	}
	strcat(dtype, "</NAGIOS>\n");
	if (send(socket, dtype, strlen(dtype), 0) == -1) {
		fprintf(stderr, "error while sending request to server\n");
	}
}

int parse_env(char *curr, struct Nagios_Env **base) {
	char tmpbuf[BUFFER_SIZE];
	char *var1;
	char *var2;
	struct Nagios_Env *ptr = NULL;
	ptr = *base;

	strncpy(tmpbuf, curr, BUFFER_SIZE);
	var1 = strtok(tmpbuf, "=");
	var2 = strtok(NULL, "");
	if (strncmp(var1, (char *) "NAGIOS_", 7) == 0) {
		if (var2 != NULL) {
			if (ptr == NULL) {
				/* create first list element */
				ptr = (struct Nagios_Env *) malloc(sizeof(struct Nagios_Env));
				if (ptr == NULL)
					return ERROR;
				*base = ptr;
			} else {
				/* create a new list element */
				/* first look for the end of list */
				while (ptr->next != NULL)
					ptr = ptr->next;

				/* neues Listenelement erzeugen */
				ptr->next = (struct Nagios_Env *) malloc(
						sizeof(struct Nagios_Env));
				if (ptr->next == NULL)
					return ERROR;
				ptr = ptr->next;
			}

			// Building dynamic list
			strcpy(ptr->varname, var1);
			strcpy(ptr->varvalue, var2);
			ptr->next = NULL;
		}
	}
	bzero((void *) tmpbuf, BUFFER_SIZE);
	return (0);
}

int open_sock() {
	int tosocket; /* the socket descriptor*/

	/* description of struct sockaddr_in is mentioned in netinet/in.h */
	struct sockaddr_in toaddr; /* store address of server here */

	/* create tcp socket */
	tosocket = socket(PF_INET, SOCK_STREAM, 0);
	if (tosocket == -1) {
		fprintf(stderr, "cannot open socket\n");
		exit(1);
	}

	/* define server address */
	toaddr.sin_family = PF_INET;
	toaddr.sin_addr.s_addr = inet_addr(serverip);
	toaddr.sin_port = htons(serverport);

	/* connect to server */
	if (connect(tosocket, (struct sockaddr *) &toaddr, sizeof(toaddr)) == -1) {
		fprintf(stderr,
				"Unable to connect to server '%s' on port %d. Exiting...\n",
				serverip, serverport);
		close(tosocket);
		exit(1);
	} else {
		socket_status = OPEN;
		return tosocket;
	}
	return -1;
}
