/*****************************************************************************
 *
 * NPCDMOD.H
 *
 * Copyright (c) 2008 Hendrik Baecker (http://www.pnp4nagios.org)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 *  published by the Free Software Foundation;
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
 * Last Modified: 11-18-2008
 *
 *****************************************************************************/

#define PERFDATA_BUFFER 65536 


/* MMAPFILE structure - used for reading files via mmap() */
typedef struct pnp_mmapfile_struct {
	char *path;
	int mode;
	int fd;
	unsigned long file_size;
	unsigned long current_position;
	unsigned long current_line;
	void *mmap_buf;
} pnp_mmapfile;

pnp_mmapfile *pnp_mmap_fopen(char *); /* open a file read-only via mmap() */
int pnp_mmap_fclose(pnp_mmapfile *);
char *pnp_mmap_fgets(pnp_mmapfile *);
char *pnp_mmap_fgets_multiline(pnp_mmapfile *);
extern void pnp_strip(char *);
