<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// On active le calcul si var_mode=css
if (_request('var_mode') == 'css') {
	define('_VAR_MODE','calcul');
}

// Activer les LINE_COMMENTS si besoin
// define('_SCSS_LINE_COMMENTS', false);
