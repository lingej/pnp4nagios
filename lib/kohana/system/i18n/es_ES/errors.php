<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Error del Framework', 'Revisa la documentación de Kohana para información sobre el siguiente error.'),
	E_PAGE_NOT_FOUND     => array( 1, 'No se encuentra la página', 'No se encontró la página solicitada. Puede ser que haya sido movida, borrada o archivada.'),
	E_DATABASE_ERROR     => array( 1, 'Error de Base de Datos', 'Ocurrió un error en la base de datos mientras se ejecutaba el procedimiento requerido. Para más información, revisa el error que aparece más abajo.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Error Recuperable', 'Se detectó un error que evitó que esta página cargara. Si el problema persiste, contacta con el administrador de la web.'),
	E_ERROR              => array( 1, 'Error Fatal', ''),
	E_USER_ERROR         => array( 1, 'Error Fatal', ''),
	E_PARSE              => array( 1, 'Error de Syntaxis', ''),
	E_WARNING            => array( 1, 'Advertencia', ''),
	E_USER_WARNING       => array( 1, 'Advertencia', ''),
	E_STRICT             => array( 2, 'Strict Mode Error', ''),
	E_NOTICE             => array( 2, 'Runtime Message', ''),
);