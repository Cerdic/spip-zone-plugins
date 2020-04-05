<?php
// fichier d'options par défaut du plugin xray
// xray/inc/xray_options_default.php

if (!defined('XRAY_PATTERN_STATS_SPECIALES')) {
	define ('XRAY_PATTERN_STATS_SPECIALES', '/\.(js|css)(\s|_|$)/ui');
	define ('XRAY_LABEL_STATS_SPECIALES', 'Javascript et css');
	define ('XRAY_LABEL_STATS_SPECIALES_EXCLUES', 'Sans les javascript et css');
}

if (!defined('XRAY_OBJET_SPECIAL')) {
	define ('XRAY_OBJET_SPECIAL', 'article');
	define ('XRAY_ID_OBJET_SPECIAL', 14533);
}

if (!defined('JOLI_DATE_FORMAT')) {
	define ('JOLI_DATE_FORMAT', 'd/m/Y H:i:s');
	date_default_timezone_set ('Europe/Paris');
}

