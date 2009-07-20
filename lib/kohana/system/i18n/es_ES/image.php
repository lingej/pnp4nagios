<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'getimagesize_missing'	  => 'La librería &8220;Image&8221; requiere la función PHP getimagesize, que no parece estar disponible en tu instalación.',
	'unsupported_method'	  => 'El driver que has elegido en la configuración no soporta el tipo de transformación %s.',
	'file_not_found'		  => 'La imagen especificada, %s no se ha encontrado. Por favor, verifica que existe utilizando file_exists() antes de manipularla.',
	'type_not_allowed'		  => 'El tipo de imagen especificado, %s, no es un tipo de imagen permitido.', 
	'invalid_width'			  => 'El ancho que has especificado, %s, no es valido.',
	'invalid_height'		  => 'El alto que has especificado, %s, no es valido.',
	'invalid_dimensions'	  => 'Las dimensiones que has especificado para %s no son validas.',
	'invalid_master'		  => 'The master dim specified is not valid.',
	'invalid_flip'			  => 'La dirección de rotación especificada no es valida.',
	'directory_unwritable'	  => 'El directorio especificado, %s, no tiene permisos de escritura.',

	// ImageMagick specific messages
	'imagemagick' => array
	(
		'not_found' => 'El directorio de ImageMagick especificado, no contiene el programa requerido, %s.', 
	),

	// GraphicsMagick specific messages
	'graphicsmagick' => array
	(
		'not_found' => 'El directorio de GraphicsMagick especificado, no contiene el programa requerido, %s.',
	),

	// GD specific messages
	'gd' => array
	(
		'requires_v2' => 'La librería &8220;Image&8221; requiere GD2. Por favor, lee http://php.net/gd_info para más información.',
	),
);
