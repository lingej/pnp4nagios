<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Class errors
	'invalid_rule'  => 'Ongeldige validatieregel gebruikt: %s',

	// Algemene errors
	'unknown_error' => 'Onbekende validatiefout bij het valideren van het %s veld.',
	'required'      => 'Het %s veld is verplicht.',
	'min_length'    => 'Het %s veld moet minstens %s karakters lang zijn.',
	'max_length'    => 'Het %s veld mag maximum %s karakters lang zijn.',
	'exact_length'  => 'Het %s veld moet exact %s karakters lang zijn.',
	'in_array'      => 'Het %s veld moet geselecteerd worden uit de gegeven opties.',
	'matches'       => 'Het %s veld moet overeenkomen met het %s veld.',
	'valid_url'     => 'Het %s veld moet een geldige URL zijn.',
	'valid_email'   => 'Het %s veld moet een geldig e-mailadres zijn.',
	'valid_ip'      => 'Het %s veld moet een geldig IP-adres zijn.',
	'valid_type'    => 'Het %s veld mag alleen maar %s karakters bevatten.',
	'range'         => 'Het %s veld moet tussen bepaalde waardes liggen.',
	'regex'         => 'Het %s veld valideert niet als geldige invoer.',
	'depends_on'    => 'Het %s veld is afhankelijk van het %s veld.',

	// Upload errors
	'user_aborted'  => 'Het uploaden van het %s bestand werd afgebroken.',
	'invalid_type'  => 'Het bestandstype van het %s bestand is niet toegestaan.',
	'max_size'      => 'Het %s bestand dat je wilde uploaden is te groot. De maximum toegelaten grootte is %s.',
	'max_width'     => 'Het %s upgeloade bestand is te groot: maximum toegelaten breedte is %spx.',
	'max_height'    => 'Het %s upgeloade bestand is te groot: maximum toegelaten hoogte is %spx.',
	'min_width'     => 'Het %s upgeloade bestand is te klein: minimum toegelaten breedte is %spx.',
	'min_height'    => 'Het %s upgeloade bestand is te klein: minimum toegelaten breedte is %spx.',

	// Field types
	'alpha'         => 'alfabetisch',
	'alpha_numeric' => 'alfanumeriek',
	'alpha_dash'    => 'alfabetisch, streepje, en underscore',
	'digit'         => 'cijfers',
	'numeric'       => 'getal',
);