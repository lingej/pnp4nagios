<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	'getimagesize_missing'    => 'Библиотеке Image необходима функция getimagesize(), недоступная в вашей инсталляции PHP.',
	'unsupported_method'      => 'Указанный драйвер не поддерживает операцию %s.',
	'file_not_found'          => 'Заданное изображение, %s, не найдено. Удостоверьтесь в наличии изображения функцией file_exists() перед его обработкой.',
	'type_not_allowed'        => 'Заданное изображение, %s, не является разрешённым типом изображений.',
	'invalid_width'           => 'Заданная ширина, %s, некорректна.',
	'invalid_height'          => 'Заданная высота, %s, некорректна.',
	'invalid_dimensions'      => 'Заданный размер для %s некорректен.',
	'invalid_master'          => 'Заданная основная сторона некорректна.',
	'invalid_flip'            => 'Направление разворота некорректно.',

	'directory_unwritable'    => 'Заданная директория, %s, недоступна для записи.',

	// ImageMagick specific messages
	'imagemagick' => array
	(
		'not_found' => 'Директория ImageMagick не содержит запрошенную программу, %s.',
	),

	// GD specific messages
	'gd' => array
	(
		'requires_v2' => 'Библиотеке Image необходимо расширение GD2. Подробности на http://php.net/gd_info .',
	),
);
