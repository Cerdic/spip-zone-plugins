<?php
/**
 * Utilisations de pipelines par encarts
 *
 * @plugin     encarts
 * @copyright  2013-2016
 * @author     Cyril
 * @licence    GNU/GPL
 * @package    SPIP\Encarts\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Les encarts sont par défaut dans le texte de l'article
 * mais il peuvent être aussi en marge de ce texte
 */
if (!defined('_TYPES_ENCARTS')) {
	define('_TYPES_ENCARTS', 'encart|marge');
}
