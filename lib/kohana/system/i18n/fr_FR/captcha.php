<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array 
( 
    'file_not_found' => 'Le fichier spécifié, %s, est introuvable. Merci de vérifier que les fichiers existent, grâce à la fontion file_exists, avant de les utiliser.',
    'requires_GD2'   => 'La librairie Captcha requiert GD2 avec le support FreeType installé. Voir http://php.net/gd_info pour de plus amples renseignements, merci.',
    
	// Words of varying length for the Captcha_Word_Driver to pick from
	// Note: use only alphanumeric characters
	'words' => array
	(
		'cd', 'tv', 'le', 'il', 'ou', 'an',
		'moi', 'age', 'coq', 'ici', 'bob', 'eau',
		'cake', 'agir', 'bain', 'dodo', 'elle', 'faux',
		'hello', 'monde', 'terre', 'adore', 'baton', 'chats',
		'absent', 'cendre', 'banane', 'cirque', 'violet', 'disque',
		'abricot', 'billets', 'cendres', 'frisson', 'nations', 'respect',
		'accepter', 'batterie', 'collines', 'desserts', 'feuilles', 'sandwich',
		'acheteurs', 'tellement', 'renverser', 'histoires', 'dimanches', 'cinquante',
	),

	// Riddles for the Captcha_Riddle_Driver to pick from
	// Note: use only alphanumeric characters
	'riddles' => array
	(
		array('Détestez-vous le spam? (oui ou non)', 'oui'),
		array('Etes vous un robot? (oui ou non)', 'non'),
		array('Le feu est... (chaud ou froid)', 'chaud'),
		array('La saison après l\'automne est l\'...', 'hiver'),
		array('Quel jour est-il?', strftime('%A')),
		array('Quel est le mois en cours?', strftime('%B')),
	)    
);
