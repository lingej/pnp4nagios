<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	E_KOHANA             => array( 1, 'Ошибка фреймворка',      'Информация об этой ошибке доступна в документации Kohana.'),
	E_PAGE_NOT_FOUND     => array( 1, 'Page Not Found',         'Запрошенная страница не найдена. Возможно, она была перемещена, удалена, или архивирована.'),
	E_DATABASE_ERROR     => array( 1, 'Database Error',         'При обработке запроса произошла ошибка в базе данных. Пожалуйста, уточните причину ошибки ниже'),
	E_RECOVERABLE_ERROR  => array( 1, 'Recoverable Error',      'Обнаружена ошибка, препятствующая загрузке этой страницы. Если это повторится, пожалуйста, уведомите администрацию сайта.'),
	E_ERROR              => array( 1, 'Фатальная ошибка',       ''),
	E_USER_ERROR         => array( 1, 'Фатальная ошибка',       ''),
	E_PARSE              => array( 1, 'Синтаксическая ошибка',  ''),
	E_WARNING            => array( 1, 'Предупреждение',         ''),
	E_USER_WARNING       => array( 1, 'Предупреждение',         ''),
	E_STRICT             => array( 2, 'Стилистическая ошибка',  ''),
	E_NOTICE             => array( 2, 'Уведомление',            ''),
);
