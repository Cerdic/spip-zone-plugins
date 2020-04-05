<?php
/**
 * Options du plugin Lim au chargement
 *
 * @plugin     Lim
 * @copyright  2015
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Lim\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (test_espace_prive()) {
	// Surcharge du fichier /prive/formulaires/editer_article.php
	// la fonction editer_article_verifier est incomplète
	// voir https://core.spip.net/issues/3686
	include_spip('inc/editer');
	function formulaires_editer_article_verifier(
		$id_article = 'new',
		$id_rubrique = 0,
		$retour = '',
		$lier_trad = 0,
		$config_fonc = 'articles_edit_config',
		$row = array(),
		$hidden = ''
	) {
		// auto-renseigner le titre si il n'existe pas
		titre_automatique('titre', array('descriptif', 'chapo', 'texte'));
		// on ne demande pas le titre obligatoire : il sera rempli a la volee dans editer_article si vide
		$erreurs = formulaires_editer_objet_verifier('article', $id_article, array('id_parent'));
		if (!function_exists('autoriser')) {
			include_spip('inc/autoriser');
		} // si on utilise le formulaire dans le public
		if (!isset($erreurs['id_parent'])
			and !autoriser('creerarticledans', 'rubrique', _request('id_parent')) and !is_numeric($id_article)
		) {
			$erreurs['id_parent'] = _T('info_creerdansrubrique_non_autorise');
		}

		return $erreurs;
	}


	// Gestion de la désactivation de la notion de Portfolio dans l'affichage des documents
	// Effacer les boutons "Déposer dans le portfolio" et "Retirer du portfolio"
	include_spip('inc/config');
	if (lire_config('lim/divers/portfolio') == 'on') {
		include_spip('inc/filtres');
		include_spip('plugins/installer');
		$get_infos = charger_fonction('get_infos', 'plugins');
		$infos_medias = $get_infos(_DIR_RACINE.'plugins-dist/medias');
		$spip_version = spip_version();
		if (spip_version_compare(spip_version(), '3.2', '>') AND $infos_medias['version'] > '2.20.27') {
			define('_BOUTON_MODE_IMAGE', false);
		}
	}
}