<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Framework Error',   'Veuillez vous référer à la documentation de Kohana pour plus d\'informations sur l\'erreur suivante.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Page Not Found',    'La page demandée n\'a pas été trouvée. Elle a peut-être été déplacée, supprimée, ou archivée.'),
	E_DATABASE_ERROR     => array( 1, 'Database Error',    'Une erreur de base de données est survenue lors de l\'exécution de la procèdure demandée. Veuillez vous référer à l\'erreur renvoyée ci-dessous pour plus d\'informations.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Recoverable Error', 'Une erreur a empêché le chargement de la page. Si le problème persiste, veuillez contacter l\'administrateur du site.'),
	E_ERROR              => array( 1, 'Fatal Error',       ''),
	E_USER_ERROR         => array( 1, 'Fatal Error',       ''),
	E_PARSE              => array( 1, 'Syntax Error',      ''),
	E_WARNING            => array( 1, 'Warning Message',   ''),
	E_USER_WARNING       => array( 1, 'Warning Message',   ''),
	E_STRICT             => array( 2, 'Strict Mode Error', ''),
	E_NOTICE             => array( 2, 'Runtime Message',   ''),
);
