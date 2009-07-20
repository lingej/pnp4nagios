<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'there_can_be_only_one' => 'Er kan maar één instantie van Kohana zijn per pagina oproep.',
	'uncaught_exception'    => 'Uncaught %s: %s in bestand %s op lijn %s',
	'invalid_method'        => 'Ongeldige method %s opgeroepen in %s.',
	'invalid_property'      => 'De %s property bestaat niet in de %s class.',
	'log_dir_unwritable'    => 'De log directory is niet schrijfbaar: %s',
	'resource_not_found'    => 'De opgevraagde %s, %s, kon niet gevonden worden.',
	'invalid_filetype'      => 'Het opgevraagde bestandstype, .%s, wordt niet toegestaan door het view configuratiebestand.',
	'view_set_filename'     => 'Je moet de view bestandsnaam opgeven voordat je render aanroept.',
	'no_default_route'      => 'Zet een default route in config/routes.php.',
	'no_controller'         => 'Kohana kon geen controller aanduiden om deze pagina te verwerken: %s',
	'page_not_found'        => 'De opgevraagde pagina, %s, kon niet gevonden worden.',
	'stats_footer'          => 'Geladen in {execution_time} seconden, met een geheugengebruik van {memory_usage}. Aangedreven door Kohana v{kohana_version}.',
	'error_file_line'       => '<tt>%s <strong>[%s]:</strong></tt>',
	'stack_trace'           => 'Stack Trace',
	'generic_error'         => 'Oproep kon niet afgewerkt worden',
	'errors_disabled'       => 'Ga naar de <a href="%s">homepage</a> of <a href="%s">probeer opnieuw</a>.',

	// Drivers
	'driver_implements'     => 'De %s driver voor de %s library moet de %s interface implementeren.',
	'driver_not_found'      => 'De %s driver voor de %s library werd niet gevonden.',

	// Resource names
	'config'                => 'configuratiebestand',
	'controller'            => 'controller',
	'helper'                => 'helper',
	'library'               => 'library',
	'driver'                => 'driver',
	'model'                 => 'model',
	'view'                  => 'view',
);