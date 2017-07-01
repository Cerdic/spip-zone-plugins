<?php
/**
 * Fonctions utiles au plugin Liens associés
 *
 * @plugin     Liens associés
 * @copyright  2017
 * @author     Rainer
 * @licence    GNU/GPL
 * @package    SPIP\Liens_associes\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Compile la balise #URL_LIEN_ASSOCIE, génerant l'url à utiliser.
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée du code compilé
 */
function balise_URL_LIEN_ASSOCIE ($p) {
	return calculer_balise_dynamique($p, 'URL_LIEN_ASSOCIE', array('lien_interne', 'objet_spip', 'id_objet_spip', 'url'));
}

/**
 * Exécution de la balise dynamique `#URL_LIEN_ASSOCIE`
 *
 * Retourne une url interne ou externe.
 *
 * @param string $lien_interne
 *     Si on, il s'agit d'un lien interne.
 * @param string $table
 *     La table del l'objet.
 * @param string $id_objet
 *     L'identifiant de l'objet
 * @param string $url
 *     L'url extgerne.
 * @return string
 *     URL du lien associé.
 **/
function  balise_URL_LIEN_ASSOCIE_dyn($lien_interne, $table, $id_objet, $url) {

	if ($lien_interne) {
		$url = generer_url_entite($id_objet, $table, '', '', TRUE);
	}

	return $url;
}