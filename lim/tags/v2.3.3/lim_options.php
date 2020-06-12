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
}