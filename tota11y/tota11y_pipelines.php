<?php
/**
 * Utilisations de pipelines par tota11y
 *
 * @plugin     tota11y
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\tota11y\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajouter tota11y dans l'espace public seulement pour webmestre
 *
 * @pipeline affichage_final
 * @note
 *     Le pipeline affichage_final est executé à chaque hit sur toute la page
 *
 * @param string $page
 *     Contenu de la page à envoyer au navigateur
 * @return string
 *     Contenu de la page à envoyer au navigateur
**/
function tota11y_affichage_final($page) {
	if(!function_exists('autoriser'))
		include_spip('inc/autoriser');

	if (autoriser('webmestre')) {
		$pos_head = strpos($page, '</head>');
		if ($pos_head === false) {
			return $page;
		}

		$incJS = '<script type="text/javascript" src="'.find_in_path("javascript/tota11y.min.js").'"></script>';
		
		// js avant la premiere css, ou sinon avant la fin du head
		$pos_link = strpos($page, '<link ');
			if (!$pos_link) {
				$pos_link = $pos_head;
		}
		$page = substr_replace($page, $incJS, $pos_link, 0);
	}

	return $page;
}
?>