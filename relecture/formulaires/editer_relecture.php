<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_editer_relecture_charger_dist($id_relecture='oui', $redirect='') {
	// Traitement standard de chargement
	$valeurs = formulaires_editer_objet_charger('relecture', $id_relecture, 0, 0, $redirect, 'relectures_edit_config');

	// Ouverture d'une relecture sur un article
	if (($id_relecture == 'oui')
	AND ($id_article = intval(_request('id_article')))) {
		// On supprime l'index 'id_article' du tableau des valeurs afin que id_article soit transmis dans
		// la fonction traiter() (car id_article est un champ de l'objet relecture)
		unset($valeurs['id_article']);
	}
	// Modification d'une relecture
	elseif ($id_relecture = intval($id_relecture)) {
		// On récupère l'id_article de la relecture
		$id_article = intval(sql_getfetsel('id_article', 'spip_relectures', array("id_relecture=$id_relecture")));
	}

	// Néanmoins, dans tous les cas, on a besoin d'afficher le titre et le lien de l'article associé à la relecture :
	// -> donc on le passe au formulaire
	$valeurs['_titre_article'] = sql_getfetsel('titre', 'spip_articles', array("id_article=$id_article"));
	$valeurs['_lien_article'] = generer_url_entite($id_article, 'article');

	return $valeurs;
}

function formulaires_editer_relecture_verifier_dist($id_relecture='oui', $redirect='') {
	$erreurs = formulaires_editer_objet_verifier('relecture', $id_relecture, array('description'));
	return $erreurs;
}

// https://code.spip.net/@inc_editer_article_dist
function formulaires_editer_relecture_traiter_dist($id_relecture='oui', $redirect='') {

	// Ouverture d'une relecture sur un article
	if (($id_relecture == 'oui')
	AND ($id_article = intval(_request('id_article')))) {
		// Pour éviter que le traitement standard ne cree un enregistrement dans la table spip_auteurs_liens
		// il faut supprimer la référence à l'auteur connecté
		set_request('id_auteur','');
	}

	// Les autres traitements particuliers de creation de  l'objet relecture sont faits dans le
	// pipeline pre_insertion
	// Pour les modifications, aucun traitement particulier n'est necessaire
	return formulaires_editer_objet_traiter('relecture', $id_relecture, 0, 0, $redirect);
}

function relectures_edit_config($row)
{
	global $spip_ecran, $spip_lang;

	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large") ? 8 : 5;
	$config['langue'] = $spip_lang;
	return $config;
}

?>