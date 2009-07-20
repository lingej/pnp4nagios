<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Errori della classe
	'invalid_rule'  => 'Regola di validazione usata non valida: %s',
	'i18n_array'    => 'Il parametro i18n %s deve essere un array per essere utilizzato con la regola in_lang',
	'not_callable'  => 'La callback %s usata per la Validation non puo essere richiamata',

	// Errori generici
	'unknown_error' => 'Errore sconosciuto durante la validazione del campo %s.',
	'required'      => 'Il campo %s è obbligatorio.',
	'min_length'    => 'Il campo %s deve essere lungo almeno %d caratteri.',
	'max_length'    => 'Il campo %s non deve superare i %d caratteri.',
	'exact_length'  => 'Il campo %s deve contenere esattamente %d caratteri.',
	'in_array'      => 'Il campo %s deve essere selezionato dalla lista delle opzioni.',
	'matches'       => 'Il campo %s deve coincidere con il campo %s.',
	'valid_url'     => 'Il campo %s deve contenere un URL valido.',
	'valid_email'   => 'Il campo %s deve contenere un indirizzo email valido.',
	'valid_ip'      => 'Il campo %s deve contenere un indirizzo IP valido.',
	'valid_type'    => 'Il campo %s deve contenere solo i caratteri %s.',
	'range'         => 'Il campo %s deve trovarsi negli intervalli specificati.',
	'regex'         => 'Il campo %s non coincide con i dati accettati.',
	'depends_on'    => 'Il campo %s dipende dal campo %s.',

	// Errori di upload
	'user_aborted'  => 'Il caricamento del file %s è stato interrotto.',
	'invalid_type'  => 'Il file %s non è un tipo di file permesso.',
	'max_size'      => 'Il file %s inviato è troppo grande. La dimensone massima consentita è %s.',
	'max_width'     => 'Il file %s inviato è troppo grande. La larghezza massima consentita è %spx.',
	'max_height'    => 'Il file %s inviato è troppo grande. L\'altezza massima consentita è %spx.',
	'min_width'     => 'Il file %s inviato è troppo piccolo. La larghezza minima consentita è %spx.',
	'min_height'    => 'Il file %s inviato è troppo piccolo. L\'altezza minima consentita è %spx.',

	// Tipi di valore dei campi
	'alpha'         => 'alfabetico',
	'alpha_numeric' => 'alfanumerico',
	'alpha_dash'    => 'alfabetico, trattino e sottolineato',
	'digit'         => 'cifra',
	'numeric'       => 'numerico',
);
