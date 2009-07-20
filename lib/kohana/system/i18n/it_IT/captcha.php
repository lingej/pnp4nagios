<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'file_not_found' => 'Il file specificato, %s, non è stato trovato. Verificarne l\'esistenza con <tt>file_exists</tt> prima di usarlo.',
	'requires_GD2'	 => 'La libreria Captcha richiede GD2 con supporto FreeType. Leggere http://php.net/gd_info per maggiori informazioni.',

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
		array('Detesti lo spam? (si o no)', 'si'),
		array('Sei un robot? (si o no)', 'no'),
		array('Il fuoco è... (caldo o freddo)', 'caldo'),
		array('La stagione che viene dopo l\'autunno è...', 'inverno'),
		array('Che giorno della settimana è oggi?', strftime('%A')),
		array('In quale mese dell\'anno siamo?', strftime('%B')),
	),
);
