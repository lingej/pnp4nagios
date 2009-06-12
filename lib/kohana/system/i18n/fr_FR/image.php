<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
    'directory_unwritable'    => 'Le répertoire %s spécifié n\'est pas accessible en écriture.',
	'getimagesize_missing'    => 'La librairie d\'image requiert la function PHP getimagesize. Celle-ci n\'est pas disponible dans votre installation.',
	'unsupported_method'      => 'Le pilote configuré ne supporte pas la transformation d\'image %s.',
	'file_not_found'          => 'L\'image spécifié %s n\'a pas été trouvée. Merci de vérifier que l\'image existe bien avec la fonction file_exists avant sa manipulation.',
	'type_not_allowed'        => 'L\'image spécifié %s n\'est pas d\'un type autorisé.',
	'invalid_width'           => 'La largeur que vous avez spécifiée, %s, est invalide.',
	'invalid_height'          => 'La hauteur que vous avez spécifiée, %s, est invalide.',
	'invalid_dimensions'      => 'Les dimensions spécifiées pour %s ne sont pas valides.',
	'invalid_master'          => 'La dimension principale (master dim) n\'est pas valide.',
	'invalid_flip'            => 'La direction de rotation spécifiée n\'est pas valide.',

	// ImageMagick specific messages
	'imagemagick' => array
	(
		'not_found' => 'Le répertoire ImageMagick spécifié ne contient pas le programme requis %s.',
	),
	
	// GraphicsMagick specific messages
	'graphicsmagick' => array
	(
		'not_found' => 'Le répertoire GraphicsMagick spécifié ne contient pas le programme requis %s.',
	),
	
	// GD specific messages
	'gd' => array
	(
		'requires_v2' => 'La librairie d\'image requiert GD2. Veuillez consulter http://php.net/gd_info pour de plus amples informations.',
	),
);