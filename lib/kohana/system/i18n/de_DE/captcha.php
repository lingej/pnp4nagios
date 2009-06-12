<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'file_not_found'        => 'Die eingestellte Datei %s konnte nicht gefunden werden. Kontrollieren Sie bitte, bevor Sie Dateien benutzen, ob diese existieren. Sie können dafür die Funktion file_exists() benutzen.',
	'requires_GD2'			=> 'Die Captcha-Bibliothek erfordert GD2 mit FreeType-Unterstützung. Sehen Sie sich die Seite http://php.net/gd_info an, um weitere Informationen zu erhalten.',

	// Words of varying length for the Captcha_Word_Driver to pick from
	// Note: use only alphanumeric characters
	'words' => array
	(
		'cd', 'tv', 'it', 'to', 'be', 'or',
		'sun', 'car', 'dog', 'bed', 'kid', 'egg',
		'bike', 'tree', 'bath', 'roof', 'road', 'hair',
		'hello', 'world', 'earth', 'beard', 'chess', 'water',
		'barber', 'bakery', 'banana', 'market', 'purple', 'writer',
		'america', 'release', 'playing', 'working', 'foreign', 'general',
		'aircraft', 'computer', 'laughter', 'alphabet', 'kangaroo', 'spelling',
		'architect', 'president', 'cockroach', 'encounter', 'terrorism', 'cylinders',
	),

	// Riddles for the Captcha_Riddle_Driver to pick from
	// Note: use only alphanumeric characters
	'riddles' => array
	(
		array('Hasst du Spam? (ja oder nein)', 'ja'),
		array('Bist du ein Roboter? (ja oder nein)', 'nein'),
		array('Feuer ist ... (heiß or kalt)', 'heiß'),
		array('Die Jahreszeit, die nach Herbst kommt ist ...', 'Winter'),
		array('Welcher Wochentag ist heute?', strftime('%A')),
		array('In welchem Monat befinden wir uns gerade?', strftime('%B')),
	),
);
