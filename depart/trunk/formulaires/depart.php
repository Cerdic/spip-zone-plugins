<?php
/**
 * Formulaire de déplacement des articles d'une rubrique
 *
 * @plugin     Déplacer des articles par lot
 * @copyright  © 2018
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP/Form/Depart
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_depart_charger_dist() {
	// Contexte du formulaire.
	$contexte = array();

	$contexte['articles'] = (_request('articles')) ? _request('articles') : null ;
	$contexte['rubrique_dest'] = (_request('rubrique_dest')) ? _request('rubrique_dest') : null ;

	return $contexte;
}

/*
*   Fonction de vérification, cela fonction avec un tableau d'erreur.
*   Le tableau est formater de la sorte:
*   if (!_request('NomErreur')) {
*       $erreurs['message_erreur'] = '';
*       $erreurs['NomErreur'] = '';
*   }
*   Pensez à utiliser _T('info_obligatoire'); pour les éléments obligatoire.
*/
function formulaires_depart_verifier_dist() {
	include_spip('inc/utils');
	$erreurs = array();

	// On vérifie qu'on a bien sélectionné des articles et une rubrique de destination
	$obligatoires = array('articles', 'rubrique_dest');
	foreach ($obligatoires as $obligatoire) {
		$values = _request($obligatoire);
		if (!is_array($values) or count($values) == 0) {
			$erreurs[$obligatoire] = _T('info_obligatoire');
		}
	}
	include_spip('inc/autoriser');
	// ****
	// Vérifions les autorisations sur les articles
	// ****
	if (!isset($erreurs['articles'])) {
		$articles = _request('articles');
		$articles_interdit = '';

		foreach ($articles as $article) {
			$article = explode('|', $article);
			$autoriser = autoriser('modifier', 'rubrique', $article[1]);
			if ($autoriser == false) {
				$_article_info = sql_getfetsel('titre,id_article', "spip_articles", "id_article=$article[1]");
				$articles_interdit .= '<li class="item"><a href="' . generer_url_ecrire('article', "id_article=" . $_article_info['id_article']) . '">' . $_article_info['titre'] . '</a></li>';
			}
		}
		// Si on a identifié des articles que l'auteur ne peut modifier, on les affiche dans l'erreur
		$articles_interdit = trim($articles_interdit);
		if (!empty($articles_interdit)) {
			$erreurs['articles'] = _T('depart:articles_interdits');
			$erreurs['articles'] .= '<ul class="spip">';
			$erreurs['articles'] .= $articles_interdit;
			$erreurs['articles'] .= '</ul>';
		}
	}
	// ****
	// Vérifions les autorisations sur la rubrique de destination
	// ****
	$rubrique_dest = _request('rubrique_dest');
	$rubrique_dest = explode('|', $rubrique_dest[0]);
	$rubrique_dest = $rubrique_dest[1];
	$autoriser = autoriser('modifier', 'rubrique', $rubrique_dest);

	if ($autoriser == false) {
		$erreurs['rubrique_dest'] = _T('depart:rubrique_dest_interdite');
	}

	return $erreurs;
}

function formulaires_depart_traiter_dist() {
	include_spip('inc/utils');
	include_spip('base/abstract_sql');

	$retour = array(
		'editable' => true,
		'message_ok' => '',
		'redirect' => '',
	);
	// Traitement du formulaire.
	$rubrique_dest = _request('rubrique_dest');
	$rubrique_dest = explode('|', $rubrique_dest[0]);
	$rubrique_dest = $rubrique_dest[1];
	$_rubrique_info = sql_fetsel('id_secteur, titre', "spip_rubriques", "id_rubrique=$rubrique_dest");
	$articles = _request('articles');
	$articles_sql = array();

	foreach ($articles as $article) {
		$article = explode('|', $article);
		$articles_sql[] = $article[1];
	}
	$resultat = sql_updateq('spip_articles', array(
		'id_secteur' => $_rubrique_info['id_secteur'],
		'id_rubrique' => $rubrique_dest
	), 'id_article IN (' . join(',', $articles_sql) . ')');

	// On met à jour les rubriques.
	include_spip('inc/rubriques');
	calculer_rubriques();

	if ($resultat) {
		include_spip('inc/urls');
		$retour['message_ok'] = _T('depart:deplacement_art_reussi', array('titre' => $_rubrique_info['titre'], 'url' => generer_url_ecrire('rubrique', "id_rubrique=" . $rubrique_dest)));
	} else {
		$retour['message_ko'] = _T('erreur_technique_ajaxform');
	}

	// Donnée de retour.
	return $retour;
}