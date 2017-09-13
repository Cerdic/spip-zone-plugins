<?php
/**
 * Fonctions utiles au plugin Bonux des logos
 *
 * @plugin     Bonux des logo
 * @copyright  2017
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Bonux_logo\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * #CHERCHER_LOGO{objet, id_objet}
 * Retrouver le logo d'un objet sans avoir à lancer tout le système de boucle.
 *
 * @param object $p
 * @access public
 * @return object
 */
if (!function_exists('balise_CHERCHER_LOGO_dist')) {
	function balise_CHERCHER_LOGO_dist($p) {
		$objet = interprete_argument_balise(1, $p);
		$id_objet = interprete_argument_balise(2, $p);

		// Faire la conversion pour la fonction chercher_logo
		$objet = "id_table_objet($objet)";

		include_spip('inc/filtres');
		include_spip('public/quete');
		$p->code = "trouver_logo($objet, $id_objet)";
		$p->interdire_scripts = false;

		return $p;
	}
}

function trouver_logo($objet, $id_objet) {
	if (quete_logo($objet, 'on', $id_objet, '', true)) {
		return http_img_pack(_DIR_IMG.quete_logo($objet, 'on', $id_objet, '', true), '', 'class=\"spip_logos\"');
	} else {
		return '';
	}
}
