<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'there_can_be_only_one' => 'Il ne peut y avoir qu\'une instance de Kohana par page.',
	'uncaught_exception'    => 'Uncaught %s: %s dans le fichier %s à la ligne %s',
	'invalid_method'        => 'La méthode %s appelée dans %s est invalide.',
	'invalid_property'      => 'La propriété %s n\'existe pas dans la classe %s.',	
	'log_dir_unwritable'    => 'Le répertoire spécifié dans votre fichier de configuration pour le fichier de log ne pointe pas vers un répertoire accessible en écriture.',
	'resource_not_found'    => 'La ressource %s, %s, n\'a pas été trouvée.',
	'invalid_filetype'      => 'Le type de ficher demandé, .%s, n\'est pas autorisé dans le fichier de configuration des vues (view configuration file).',
	'view_set_filename'     => 'Vous devez renseigner le nom de la vue avant d\'appeller la méthode render',
	'no_default_route'      => 'Aucune route par défaut n\a été définie. Veuillez la spécifer dans le fichier config/routes.php.',
	'no_controller'         => 'Kohana n\'a pu déterminer aucun controlleur pour effectuer la requête: %s.',
	'page_not_found'        => 'La page demandée %s n\'a pu être trouvée.',
	'stats_footer'          => 'Chargé en {execution_time} secondes, {memory_usage} de mémoire utilisée. Généré par Kohana v{kohana_version}.',
	'error_file_line'       => '%s <strong>[%s]:</strong>',
	'stack_trace'           => 'Stack Trace',
	'generic_error'         => 'Impossible de terminer la requête.',
	'errors_disabled'       => 'Vous pouvez aller sur la <a href="%s">page d\'accueil</a> ou <a href="%s">essayer encore</a>.',

	// Drivers
	'driver_implements'     => 'Le driver %s de la librairie %s doit implémenter l\'interface %s.',
	'driver_not_found'      => 'Le driver %s de la librairie %s est introuvable.',

	// Resource names
    'config'                => 'fichier de configuration',	
	'controller'            => 'contrôleur',
	'helper'                => 'helper',
	'library'               => 'librairie',
	'driver'                => 'driver',
	'model'                 => 'modèle',
	'view'                  => 'vue',
);
