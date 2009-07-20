<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'getimagesize_missing'    => 'La libreria Image richiede la funzione PHP <tt>getimagesize</tt>, che non è disponibile nella tua intallazione.',
	'unsupported_method'      => 'Il driver impostato in configurazione non supporta il tipo di trasformazione %s.',
	'file_not_found'          => 'L\'immagine specificata, %s, non è stata trovata. Verificarne l\'esistenza con <tt>file_exists</tt> prima di manipolarla.',
	'type_not_allowed'        => 'Il tipo d\'immagine specificato, %s, non è permesso.', 
	'invalid_width'           => 'La larghezza specificata, %s, non è valida.',
	'invalid_height'          => 'L\'altezza specificata, %s, non è valida.',
	'invalid_dimensions'      => 'Le dimensioni specificate per %s non sono valide.',
	'invalid_master'          => 'Master dimension specificato non valido.',
	'invalid_flip'            => 'La direzione di rotazione specificata non è valida.',
	'directory_unwritable'    => 'La directory specificata, %s, non consente la scrittura.',

	// Messaggi specifici per ImageMagick
	'imagemagick' => array
	(
		'not_found'       => 'La cartella di ImageMagick specificata non contiene il programma richiesto, %s.', 
	),

	// Messaggi specifici per GraphicsMagick
	'graphicsmagick' => array
	(
		'not_found' => 'La cartella di GraphicsMagick specificata non contiene il programma richiesto, %s.',
	),

	// Messaggi specifici per GD
	'gd' => array
	(
		'requires_v2'     => 'La libreria Image richiede GD2. Leggere http://php.net/gd_info per maggiori informazioni.',
	),
);
