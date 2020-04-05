<?php
/**
 * Fonctions utiles au plugin Emogrifier
 *
 * @plugin	   Emogrifier
 * @copyright  2013
 * @author	   Vertige ASBL
 * @licence	   GNU/GPL
 * @package	   SPIP\Emogrifier\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined('_EMOGRIFIER_CSS')) {
	define('_EMOGRIFIER_CSS', 'css/newsletter.css');
}

function filtre_emogrifier($html, $fichier_css = _EMOGRIFIER_CSS) {

  include_spip('lib/emogrifier/Emogrifier');

  $fichiers = explode(',', $fichier_css);

  foreach ($fichiers as $fichier) {
		if (find_in_path($fichier . '.html')) {
			$_css = produire_fond_statique($fichier, array('format' => 'css'));
			$css .= file_get_contents(find_in_path(supprimer_timestamp($_css)));
		} elseif ( find_in_path(trim($fichier)) ){
			$css .= file_get_contents(find_in_path(trim($fichier)));
		}
  }

  // Pouvoir dire à DOMDocument.loadHTML de râler en silence sur le html mal formé
  if (!_EMOGRIFIER_LIBXML_ERROR) {
	  libxml_use_internal_errors(true);
  }

  $htmldoc = new \Pelago\Emogrifier($html, $css);
  if (_EMOGRIFIER_DISABLE_STYLE_BLOCKS_PARSING) {
		$htmldoc->disableStyleBlocksParsing();
  }
  return $htmldoc->emogrify();
}
