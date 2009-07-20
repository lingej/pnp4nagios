<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Framework Error',      'Bekijk de Kohana documentatie voor meer informatie over deze fout.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Pagina Niet Gevonden', 'De opgevraagde pagina werd niet gevonden. Mogelijk werd deze verplaatst of verwijderd.'),
	E_DATABASE_ERROR     => array( 1, 'Database Error',       'Er vond een database fout plaats bij het verwerken van de opgeroepen procedure. Bekijk het errorbericht hieronder voor meer informatie.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Recoverable Error',    'Er vond een fout plaats waardoor deze pagina niet geladen kon worden. Als dit probleem aanhoudt, contacteer dan a.u.b. de website beheerder.'),
	E_ERROR              => array( 1, 'Fatal Error',          ''),
	E_USER_ERROR         => array( 1, 'Fatal Error',          ''),
	E_PARSE              => array( 1, 'Syntax Error',         ''),
	E_WARNING            => array( 1, 'Warning Message',      ''),
	E_USER_WARNING       => array( 1, 'Warning Message',      ''),
	E_STRICT             => array( 2, 'Strict Mode Error',    ''),
	E_NOTICE             => array( 2, 'Runtime Message',      ''),
);