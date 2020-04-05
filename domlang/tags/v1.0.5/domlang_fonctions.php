<?php
/**
 * Fonctions utiles au plugin Domaines par secteur de langue
 *
 * @plugin     Domaines par secteur de langue
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Domlang\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Compile la balise `#URL_SITE_SPIP` qui retourne l'URL du site
 * telle que définie dans la configuration
 *
 * Peut transmettre une langue en premier paramètre
 * `#URL_SITE_SPIP{en}`
 *
 * @balise
 * @see balise_URL_SITE_SPIP_dist()
 * @note
 *     Surcharge afin de gérer des domaines par langue.
 *     En fonction de la langue du site, on ne revoie pas la même URL.
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
 */
function balise_URL_SITE_SPIP($p) {
	$lang = interprete_argument_balise(1, $p);
	$p->code = "domlang_url_langue($lang)";
	$p->code = "spip_htmlspecialchars(" . $p->code . ")";
	$p->interdire_scripts = false;
	return $p;
}



/**
 * Ajoute le formulaire de configuration des des domaines sur la page identité du site
 *
 * @pipeline affiche_milieu
 *
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
 */
function domlang_affiche_milieu($flux) {
	if ($flux["args"]["exec"] == "configurer_identite") {
		$flux["data"] .= recuperer_fond('prive/squelettes/inclure/configurer', array('configurer' => 'configurer_domlang'));
	}
	return $flux;
}
