<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Framework-Fehler',     'Lesen Sie bitte in der Kohana-Dokumentation, um mehr über den folgenden Fehler zu erfahren.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Seite Nicht Gefunden', 'Die aufgerufene Seite wurde nicht gefunden. Sie wurde entweder verschoben, gelöscht oder archiviert.'),
	E_DATABASE_ERROR     => array( 1, 'Datenbank-Fehler',     'Ein Datenbankfehler ist während des Aufrufs aufgetreten. Überprüfen Sie bitte den unten stehenden Fehler für mehr Informationen.'),
	E_RECOVERABLE_ERROR  => array( 1, 'Behebbarer Fehler',    'Es ist ein Fehler aufgetreten, der das Laden der Seite verhindert hat. Wenn der Fehler weiterhin besteht, kontaktieren Sie bitte den Administrator der Seite.'),
	E_ERROR              => array( 1, 'Fataler Fehler',       ''),
	E_USER_ERROR         => array( 1, 'Fataler Fehler',       ''),
	E_PARSE              => array( 1, 'Syntax-Fehler',        ''),
	E_WARNING            => array( 1, 'Warnung',              ''),
	E_USER_WARNING       => array( 1, 'Warnung',              ''),
	E_STRICT             => array( 2, 'Strict Mode Error',    ''),
	E_NOTICE             => array( 2, 'Laufzeitfehler',       ''),
);