/*****************************************************************************
 *
 * modpnpsender.c - NEB Module for sending PNP performance data
 *
 * Copyright (c) 2007-2009 Hendrik Baecker (http://www.process-zero.de)
 *
 * Last Modified: $LastChangedDate: 2009-01-07 19:53:58 +0100 (Wed, 07 Jan 2009) $
 * by: 		  $Author: hendrikb $
 *
 *
 * Description:
 *
 * Will follow soon....
 *
 *
 * Instructions:
 *
 * Compile with the following command:
 *
 *     gcc -shared -o modpnpsender.o modpnpsender.c
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

#include <sys/types.h>
#include <sys/socket.h>
#include <errno.h>
#include <time.h>

#define OPEN 	1
#define CLOSE	0

#define BUFFER 1024

/* specify event broker API version (required) */
NEB_API_VERSION(CURRENT_NEB_API_VERSION);

#define DEFAULT_SERVERPORT 5661

void *modpnpsender_module_handle=NULL;

int modpnpsender_handle_data(int,void *);
int send_data(nebstruct_service_check_data *);
int open_sock();

char *serverip="127.0.0.1";
char *port, *saveptr1;
int serverport=DEFAULT_SERVERPORT;
int socket_status=CLOSE;

/* this function gets called when the module is loaded by the event broker */
int nebmodule_init(int flags, char *args, nebmodule *handle){
	char temp_buffer[1024];
	time_t current_time;
	unsigned long interval;

	/* save our handle */
	modpnpsender_module_handle=handle;

	/* log module info to the Nagios log file */
	write_to_all_logs("modpnpsender: Copyright (c)2007 Hendrik Baecker (andurin@process-zero.de)",NSLOG_INFO_MESSAGE);

	/* log a message to the Nagios log file */
	snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: PNPSender module starting the engines!\n");
	temp_buffer[sizeof(temp_buffer)-1]='\x0';
	write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);

	if(args == NULL) {
	        write_to_all_logs("modpnpsender: WARNING assuming '127.0.0.1' as destination IP Address)",NSLOG_INFO_MESSAGE);
	}
	else {
		if((serverip = strtok_r(args, " ", &saveptr1))!=NULL){
			serverip = serverip;
		}

		if((port = strtok_r(NULL, " ", &saveptr1))!=NULL){
			serverport=atoi(port);
		}
	}


	snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: Arguments are %s - %d",serverip,serverport);
	temp_buffer[sizeof(temp_buffer)-1]='\x0';
	write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);


	/* register to be notified of certain events... */
	neb_register_callback(NEBCALLBACK_SERVICE_CHECK_DATA,modpnpsender_module_handle,0,modpnpsender_handle_data);

	return 0;
        }


/* this function gets called when the module is unloaded by the event broker */
int nebmodule_deinit(int flags, int reason){
	char temp_buffer[1024];

	/* deregister for all events we previously registered for... */
	neb_deregister_callback(NEBCALLBACK_SERVICE_CHECK_DATA,modpnpsender_handle_data);

	/* log a message to the Nagios log file */
	snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: Exiting - Thanks for for the flight!\n");
	temp_buffer[sizeof(temp_buffer)-1]='\x0';
	write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);

	return 0;
        }


/* handle data from Nagios daemon */
int modpnpsender_handle_data(int event_type, void *data){
	nebstruct_service_check_data *scdata=NULL;
	char temp_buffer[1024];

	/* what type of event/data do we have? */
	switch(event_type){

	case NEBCALLBACK_SERVICE_CHECK_DATA:

		/* a service check event occurs */
                if((scdata=(nebstruct_service_check_data *)data)!=NULL){

			if (scdata->type==NEBTYPE_SERVICECHECK_INITIATE) {

				/* Check if this service check has performance data */

				if (scdata->perf_data != NULL) {
					snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: Processing PNP for %s / %s with perfdata %s",scdata->host_name,scdata->service_description,scdata->perf_data);
					temp_buffer[sizeof(temp_buffer)-1]='\x0';
					write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);
					if(send_data(scdata)!=0) {
					        write_to_all_logs("modpnpsender: An error occured while sending data!",NSLOG_INFO_MESSAGE);
					}
					else {
					        write_to_all_logs("modpnpsender: Message sent - finish for now.",NSLOG_INFO_MESSAGE);
						return 0;
					}
				}
        	        }
		}
	        break;


	default:
		break;
	        }

	return 0;
        }

/*********************************************
 *
 * Sending Data
 *
 * *******************************************/

int send_data(nebstruct_service_check_data *data){
	char temp_buffer[1024];
	char *message;
	int socket;
	int message_length = 1024;
	char cmd[1024];
	time_t t;

	if (socket_status == CLOSE) {
		socket = open_sock();
		if (socket == -1) {
			write_to_all_logs("modpnpsender: Arg! Socket is -1",NSLOG_INFO_MESSAGE);
			return (-1);
			}
		}

	if (!(message = (char *) malloc(message_length))) {
		snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: cannot allocate memory for message. Aborting...\n");
                temp_buffer[sizeof(temp_buffer)-1]='\x0';
                write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);
		return (-1);
	}

	bzero((void *)message, message_length);

	strcat(message,"<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n");
	strcat(message,"<NAGIOS>\n<NAGIOS_DATATYPE>serviceperfdata</NAGIOS_DATATYPE>\n");

	strcat(message,"<NAGIOS_HOSTNAME>");
	strcat(message,data->host_name);
	strcat(message,"</NAGIOS_HOSTNAME>\n");

	strcat(message,"<NAGIOS_SERVICEDESC>");
	strcat(message,data->service_description);
	strcat(message,"</NAGIOS_SERVICEDESC>\n");

	strcat(message,"<NAGIOS_SERVICEPERFDATA>");
	strcat(message,data->perf_data);
	strcat(message,"</NAGIOS_SERVICEPERFDATA>\n");

	strcat(message,"<NAGIOS_SERVICECHECKCOMMAND>");
        if (data->command_name != NULL) {
		strcat(message,data->command_name);
		}
        strcat(message,"</NAGIOS_SERVICECHECKCOMMAND>\n");

        strcat(message,"<NAGIOS_SERVICECHECKCOMMAND_ARGS>");
        if (data->command_args != NULL) strcat(message,data->command_args);
        strcat(message,"</NAGIOS_SERVICECHECKCOMMAND>\n");

        strcat(message,"<NAGIOS_SERVICECHECKCOMMAND_LINE>");
        if (data->command_line != NULL) strcat(message,data->command_line);
        strcat(message,"</NAGIOS_SERVICECHECKCOMMAND>\n");

        strcat(message,"<NAGIOS_SERVICEOUTPUT>");
        if (data->output != NULL) strcat(message,data->output);
        strcat(message,"</NAGIOS_SERVICEOUTPUT>\n");


	time(&t);
	snprintf(temp_buffer, sizeof(temp_buffer)-1,"%ld",t);
	temp_buffer[sizeof(temp_buffer)-1]='\x0';

 	strcat(message,"<NAGIOS_TIMET>");
	strcat(message, temp_buffer);
	strcat(message,"</NAGIOS_TIMET>\n");
	bzero((void *)temp_buffer,sizeof(temp_buffer)-1);

	strcat(message,"</NAGIOS>\n");

	if(send(socket, message, strlen(message), 0) == -1) {
		snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: error while sending message to server. - %s\n",strerror(errno));
                temp_buffer[sizeof(temp_buffer)-1]='\x0';
                write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);
	}

	close (socket);
	socket_status = CLOSE;

	snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: Sending perfdata for %s / %s with perfdata %s",data->host_name,data->service_description,data->perf_data);
        temp_buffer[sizeof(temp_buffer)-1]='\x0';
        write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);

	return 0;
}

int open_sock(){
	int tosocket; /* the socket descriptor*/
	char temp_buffer[BUFFER];

	/* description of struct sockaddr_in is mentioned in netinet/in.h */
	struct sockaddr_in toaddr; /* store address of server here */

	/* create tcp socket */
	tosocket = socket(PF_INET,SOCK_STREAM,0);
	if(tosocket == -1){
		snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: Unable to create socket. Aborting...\n");
                temp_buffer[sizeof(temp_buffer)-1]='\x0';
                write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);
		return(-1);
		}
	/* else {
                snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: Socket is OK....\n");
                temp_buffer[sizeof(temp_buffer)-1]='\x0';
                write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);
		} */

	/* define server address */
	toaddr.sin_family = PF_INET;
	toaddr.sin_addr.s_addr =  inet_addr(serverip);
	toaddr.sin_port = htons(serverport);

	/* connect to server */
	if(connect(tosocket, (struct sockaddr *)&toaddr, sizeof(toaddr)) == -1){

		snprintf(temp_buffer,sizeof(temp_buffer)-1,"modpnpsender: Unable to connect to server '%s' on port %d. Aborting...\n", serverip, serverport);
		temp_buffer[sizeof(temp_buffer)-1]='\x0';
		write_to_all_logs(temp_buffer,NSLOG_INFO_MESSAGE);
		// write_to_all_logs("modpnpsender: Connect failed... closing the socket...",NSLOG_INFO_MESSAGE);
		close (tosocket);
		// write_to_all_logs("modpnpsender: Socket closed...",NSLOG_INFO_MESSAGE);
		return(-1);
		}
	else {
		socket_status=OPEN;
		return tosocket;
		}
	return (-1);
}
