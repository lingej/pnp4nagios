<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'there_can_be_only_one' => 'Pro Seitenaufruf kann es nur eine Instanz von Kohana geben',
	'uncaught_exception'    => 'Unerwarteter Fehler vom Typ %s: %s in %s in Zeile %s',
	'invalid_method'        => 'Ungültige Methode %s aufgerufen in %s',
	'invalid_property'      => '%s ist keine Eigenschaft der Klasse %s.',
	'log_dir_unwritable'    => 'Das Log-Verzeichnis ist nicht beschreibbar: %s',
	'resource_not_found'    => '%s %s konnte nicht gefunden werden',
	'invalid_filetype'      => 'Die Dateiendung .%s ist in Ihrer View-Konfiguration nicht vorhanden',
	'view_set_filename'     => 'Sie müssen den Dateinamen der Ansicht festlegen, bevor render aufgerufen wird',
	'no_default_route'      => 'Erstellen Sie bitte eine Standardroute config/routes.php',
	'no_controller'         => 'Kohana gelang es nicht einen Controller zu finden, um diesen Aufruf zu verarbeiten: %s',
	'page_not_found'        => 'Die Seite %s konnte nicht gefunden werden.',
	'stats_footer'          => 'Seite geladen in {execution_time} Sekunden bei {memory_usage} Speichernutzung. Generiert von Kohana v{kohana_version}.',
	'error_file_line'       => '<tt>%s <strong>[%s]:</strong></tt>',
	'stack_trace'           => 'Stack Trace',
	'generic_error'         => 'Die Abfrage konnte nicht abgeschlossen werden',
	'errors_disabled'       => 'Sie können zur <a href="%s">Startseite</a> zurück kehren oder es <a href="%s">erneut versuchen</a>.',

	// Drivers
	'driver_implements'     => 'Der Treiber %s für die Bibliothek %s muss das Interface %s implementieren',
	'driver_not_found'      => 'Der Treiber %s für die Bibliothek %s konnte nicht gefunden werden',

	// Resource names
	'config'                => 'Die Konfigurationsdatei',
	'controller'            => 'Der Controller',
	'helper'                => 'Der Helfer',
	'library'               => 'Die Bibliothek',
	'driver'                => 'Der Treiber',
	'model'                 => 'Das Modell',
	'view'                  => 'Die Ansicht',
);