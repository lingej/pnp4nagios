<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Class errors
	'invalid_rule'  => 'La règle de validation %s utilisée est invalide',
    'i18n_array'    => 'La clé i18n %s doit être un tableau pour pouvoir être utilisée avec la règle in_lang',	
    'not_callable'  => 'La callback %s utilisé pour la Validation n\'est pas appellable',

	// General errors
	'unknown_error' => 'Erreur de validation inconnue lors de la validation du champ %s.',
	'required'      => 'Le champ %s est requis.',
	'min_length'    => 'Le champ %s doit contenir au minimum %d caractères.',
	'max_length'    => 'Le champ %s ne peut contenir plus de %d caractères.',
	'exact_length'  => 'Le champ %s doit contenir exactement %d caractères.',
	'in_array'      => 'Le champ %s doit être sélectionné dans parmi les options listées.',
	'matches'       => 'Le champ %s doit correspondre au champ %s.',
	'valid_url'     => 'Le champ %s doit contenir une URL valide.',
	'valid_email'   => 'Le champ %s doit contenir une adresse email valide.',
	'valid_ip'      => 'Le champ %s doit contenir une adresse IP valide.',
	'valid_type'    => 'Le champ %s doit contenir uniquement %s caractères',
	'range'         => 'Le champ %s doit être situé dans la plage de valeurs spécifiée.',
	'regex'         => 'Le champ %s ne correspond pas aux valeurs acceptées.',
	'depends_on'    => 'Le champ %s est dépendant du champ %s.',

	// Upload errors
	'user_aborted'  => 'L\'envoi du fichier %s sur le serveur a échoué.',
	'invalid_type'  => 'Le type du fichier %s n\'est pas autorisé.',
	'max_size'      => 'La taille du fichier %s que vous tentez d\'envoyer est trop volumineuse. La taille maximale autorisée est de %s',
	'max_width'     => 'La largeur de l\'image %s que vous envoyez est trop grande. La largeur maximale autorisée est de %spx',
	'max_height'    => 'La hauteur de l\'image %s que vous envoyez est trop grande. La hauteur maximale autorisée est de %spx',
	'min_width'     => 'La largeur de l\'image %s que vous envoyez n\'est pas assez grande. La largeur minimale demandée est de %spx',
	'min_height'    => 'La hauteur de l\'image %s que vous envoyez n\'est pas assez grande. La hauteur minimale demandée est de %spx',

	// Field types
	'alpha'         => 'alphabétiques',
	'alpha_numeric' => 'alphabétiques et numériques',
	'alpha_dash'    => 'alphabétiques, tirets haut ou tirets bas (underscore)',
	'digit'         => 'digitaux',
	'numeric'       => 'numériques',
);