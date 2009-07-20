<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'file_not_found' => 'Het opgegeven bestand, %s, werd niet gevonden. Controleer met file_exists() of bestanden bestaan, voordat je ze gebruikt.',
	'requires_GD2'   => 'De Captcha library vereist GD2 met FreeType ondersteuning. Zie http://php.net/gd_info voor meer informatie.',

	// Words of varying length for the Captcha_Word_Driver to pick from
	// Note: use only alphanumeric characters
	'words' => array
	(
		'cd', 'tv', 'ok', 'pc', 'nu', 'ik',
		'zon', 'kar', 'kat', 'bed', 'tof', 'hoi',
		'puin', 'hoop', 'mens', 'roof', 'auto', 'haar',
		'water', 'beter', 'aarde', 'appel', 'mango', 'liter',
		'ananas', 'bakker', 'wekker', 'kroket', 'zingen', 'dansen',
		'fietsen', 'zwemmen', 'kolonel', 'potlood', 'kookpot', 'voetbal',
		'barbecue', 'computer', 'generaal', 'koelkast', 'fietsers', 'spelling',
		'appelmoes', 'president', 'kangoeroe', 'frankrijk', 'luxemburg', 'apartheid',
	),

	// Riddles for the Captcha_Riddle_Driver to pick from
	// Note: use only alphanumeric characters
	'riddles' => array
	(
		array('Haat jij spam? (ja of nee)', 'ja'),
		array('Ben jij een robot? (ja of nee)', 'nee'),
		array('Vuur is... (heet of koud)', 'heet'),
		array('Het seizoen na herfst is...', 'winter'),
		array('Welke dag van de week is het vandaag?', strftime('%A')),
		array('In welke maand van het jaar zijn we?', strftime('%B')),
	),
);
