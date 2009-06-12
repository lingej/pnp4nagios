<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Class errors
	'invalid_rule'  => 'Ungültige Validierungsregel benutzt: %s',
	'i18n_array'    => 'Der i18n-Schlüssel %s muss ein Array sein, um diesen in der in_array-Regel benutzen zu können',
	'not_callable'  => 'Die Callback-Funktion %s, die zur Validierung benutzt wird, ist nicht aufrufbar',

	// General errors
	'unknown_error' => 'Unbekannter Fehler bei der Validierungsregel von dem Feld %s aufgetreten.',
	'required'      => 'Das Feld %s ist erforderlich.',
	'min_length'    => 'Das Feld %s muss mindestens %d Zeichen lang sein.',
	'max_length'    => 'Das Feld %s darf höchstens %d Zeichen lang sein.',
	'exact_length'  => 'Das Feld %s muss genau %d Zeichen enthalten.',
	'in_array'      => 'Das Feld %s muss ausgewählt werden.',
	'matches'       => 'Das Feld %s muss mit dem Feld %s übereinstimmen.',
	'valid_url'     => 'Das Feld %s muss eine gültige URL beinhalten.',
	'valid_email'   => 'Das Feld %s muss eine gültige E-Mailadresse beinhalten.',
	'valid_ip'      => 'Das Feld %s muss eine gültige IP-Adresse beinhalten.',
	'valid_type'    => 'Das Feld %s darf nur %s beinhalten.',
	'range'         => 'Das Feld %s muss zwischen festgelegten Bereichen sein.',
	'regex'         => 'Das Feld %s entspricht nicht einer akzeptierten Eingabe.',
	'depends_on'    => 'Das Feld %s hängt vom Feld %s ab.',

	// Upload errors
	'user_aborted'  => 'Das Hochladen der Datei %s wurde abgebrochen.',
	'invalid_type'  => 'Die Datei %s entspricht nicht den erlaubten Dateitypen.',
	'max_size'      => 'Die Datei %s ist zu groß. Die maximale Größe beträgt %s.',
	'max_width'     => 'Die Datei %s ist zu groß. Die maximal erlaubte Breite betägt %spx.',
	'max_height'    => 'Die Datei %s ist zu groß. Die maximal erlaubte Höhe betägt %spx.',
	'min_width'     => 'Die Datei %s ist zu klein. Die minimal erlaubte Breite betägt %spx.',
	'min_height'    => 'Die Datei %s ist zu klein. Die minimal erlaubte Höhe betägt %spx.',

	// Field types
	'alpha'         => 'alphabetische Zeichen',
	'alpha_numeric' => 'alphabetische und numerische Zeichen',
	'alpha_dash'    => 'alphabetische Zeichen, Trennstriche und Unterstriche',
	'digit'         => 'Zahlen',
	'numeric'       => 'Nummern',
);
