<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Utiliser la version SASS
if (!defined('_FOUNDATION_SASS')) {
	define('_FOUNDATION_SASS', false);
}

// Animation par défaut pour les reveal modal
if (!defined('_REVEAL_ANIMATION_IN')) {
	define('_REVEAL_ANIMATION_IN', '');
}

if (!defined('_REVEAL_ANIMATION_OUT')) {
	define('_REVEAL_ANIMATION_OUT', '');
}