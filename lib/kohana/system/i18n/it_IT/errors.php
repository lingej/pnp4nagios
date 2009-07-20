<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Errore del Framework', 'Controlla la documentazione di Kohana per informazioni circa il seguente errore.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Pagina non trovata',   'La pagina richiesta non è stata trovata. Potrebbe essere stata spostata, rimossa o archiviata.'),
	E_DATABASE_ERROR     => array( 1, 'Errore del Database',  'Si è verificato un errore durante l\'esecuzione della procedura richiesta. Per maggiori informazioni fare riferimento al messaggio sottostante.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Errore Recuperabile',  'Un errore ha impedito il caricamento della pagina. Se l\'errore persiste contattare l\'amministratore del sito.'),
	E_ERROR              => array( 1, 'Errore Fatale',        ''),
	E_USER_ERROR         => array( 1, 'Errore Fatale',        ''),
	E_PARSE              => array( 1, 'Errore di Sintassi',   ''),
	E_WARNING            => array( 1, 'Avviso',               ''),
	E_USER_WARNING       => array( 1, 'Avviso',               ''),
	E_STRICT             => array( 2, 'Strict Mode Error',    ''),
	E_NOTICE             => array( 2, 'Runtime Message',      ''),
);