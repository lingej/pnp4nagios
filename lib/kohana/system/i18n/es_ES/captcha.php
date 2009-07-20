<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'file_not_found'	=> 'El archivo especificado, %s, no ha sido encontrado. Por favor, verifica que el fichero existe utilizando file_exists() antes de intentar utilizarlo.',
	'requires_GD2'		=> 'La libreria Captcha requiere GD2 con soporte FreeType. Lea lo siguiente http://php.net/gd_info para ampliar la informacion.',
	
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
		array('¿Odias el spam? (si o no)', 'si'),
		array('¿Eres un robot? (si o no)', 'no'),
		array('El fuego es... (caliente o frio)', 'caliente'),
		array('La estación que viene despues del otoño es...', 'invierno'),
		array('¿Qué día de la semana es hoy?', strftime('%A')),
		array('¿En qué mes del año estamos?', strftime('%B')),
	),
);
