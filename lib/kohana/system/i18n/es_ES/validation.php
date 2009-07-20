<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Class errors
	'invalid_rule'  => 'La regla de validación usada es invalida: %s',
	'i18n_array'    => 'La clave %s i18n debe de ser un array para ser utilizado en la regla in_lang',
	'not_callable'  => 'La llamada de retorno %s utilizada para la validación no puede ser llamada',

	// General errors
	'unknown_error' => 'Error de validación desconocido al comprobar el campo %s.',
	'required'      => 'El campo %s es obligatorio.',
	'min_length'    => 'El campo %s debe tener un mínimo de %d caracteres.',
	'max_length'    => 'El campo %s debe tener un máximo de %d caracteres.',
	'exact_length'  => 'El campo %s debe tener exactamente %d caracteres.',
	'in_array'      => 'El campo %s debe ser seleccionado de las opciones listadas.',
	'matches'       => 'El campo %s debe conincidir con el campo %s.',
	'valid_url'     => 'El campo %s debe contener una url valida, empezando con %s://.',
	'valid_email'   => 'El campo %s debe contener una dirección de email valida.',
	'valid_ip'      => 'El campo %s debe contener una dirección IP valida.',
	'valid_type'    => 'El campo %s debe contener unicamente %s.',
	'range'         => 'El campo %s debe estar entre los rangos especificados.',
	'regex'         => 'El campo %s no coincide con los datos aceptados.',
	'depends_on'    => 'El campo %s depende del campo %s.',

	// Upload errors
	'user_aborted'  => 'El envio del archivo %s fue abortado antes de completarse.',
	'invalid_type'  => 'El archivo %s no es un tipo de archivo permitido.',
	'max_size'      => 'El archivo %s que estas enviando es muy grande. El tamaño máximo es de %s.',
	'max_width'     => 'El archivo %s debe tener como ancho máximo %s, y tiene %spx.',
	'max_height'    => 'El archivo %s debe tener como alto máximo %s, y tiene %spx.',
	'min_width'     => 'El archivo %s que estas enviando es muy pequeño. El ancho mínimo permitido es de %spx.',
	'min_height'    => 'El archivo %s que estas enviando es muy pequeño. El alto mínimo permitido es de %spx.',

	// Field types                                                                                                                                                     
	'alpha'         => 'caracteres del alfabeto',
	'alpha_numeric' => 'caracteres del alfabeto y numericos',
	'alpha_dash'    => 'caracteres del alfabeto, guiones y subrayado',
	'digit'         => 'digitos',
	'numeric'       => 'caracteres numéricos',
);