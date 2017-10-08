<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Par défaut, les erreur de html seront affichée
if (!defined('_EMOGRIFIER_LIBXML_ERROR')) {
	define('_EMOGRIFIER_LIBXML_ERROR', true);
}
// Par défaut on desactive la prise en compte des balises style contenu dans le html
// elle sont utilisés pour des besoins spécifique a des navigateurs ou peripheriques
// et ne doivent pas êtres inlinées ou supprimées du html généré
if (!defined('_EMOGRIFIER_DISABLE_STYLE_BLOCKS_PARSING')) {
	define('_EMOGRIFIER_DISABLE_STYLE_BLOCKS_PARSING', true);
}
