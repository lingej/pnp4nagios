<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'there_can_be_only_one' => 'Ci può essere una sola istanza di Kohana per ogni pagina richiesta.',
	'uncaught_exception'    => 'Uncaught %s: %s in %s, linea %s',
	'invalid_method'        => 'Metodo non valido <tt>%s</tt> chiamato in <tt>%s</tt>.',
	'invalid_property'      => 'La proprietà %s non esiste nella classe %s.',
	'log_dir_unwritable'    => 'Il parametro di configurazione log.directory non punta ad una cartella con permesso di scrittura.',
	'resource_not_found'    => 'Il %s richiesto, <tt>%s</tt>, non è stato trovato.',
	'invalid_filetype'      => 'Il tipo di file richiesto, <tt>.%s</tt>, non è presente nel file di configurazione.',
	'view_set_filename'     => 'Bisogna definire il nome di una vista prima di chiamare il metodo render',
	'no_default_route'      => 'Non è stato definito il default route in <tt>config/routes.php</tt>.',
	'no_controller'         => 'Kohana non è in grado di determinare a quale controller inoltrare la richiesta: %s',
	'page_not_found'        => 'La pagina richiesta, <tt>%s</tt>, non è stata trovata.',
	'stats_footer'          => 'Caricato in {execution_time} secondi, usando {memory_usage} di memoria. Generato da Kohana v{kohana_version}.',
	'error_file_line'       => 'Errore in <strong>%s</strong> linea: <strong>%s</strong>.',
	'stack_trace'           => 'Tracciato',
	'generic_error'         => 'Impossibile completare la richiesta',
	'errors_disabled'       => 'Puoi andare alla <a href="%s">pagina iniziale</a> o <a href="%s">ritentare</a>.', 

	// Drivers
	'driver_implements'     => 'Il driver %s per la libreria %s non implementa l\'interfaccia %s',
	'driver_not_found'      => 'Il driver %s per la libreria %s non è stato trovato',
	
	// Resource names
	'config'                => 'file di configurazione',
	'controller'            => 'controller',
	'helper'                => 'helper',
	'library'               => 'libreria',
	'driver'                => 'driver',
	'model'                 => 'modello',
	'view'                  => 'vista',
);
