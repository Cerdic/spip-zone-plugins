<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_editer_relecture_charger_dist($id_relecture='oui', $redirect='') {
	// Traitement standard de chargement
	$valeurs = formulaires_editer_objet_charger('relecture', $id_relecture, 0, 0, $redirect, 'relectures_edit_config', $row, $hidden);
	if ($id_article = intval(_request('id_article'))) {
		// On supprime l'index 'id_article' du tableau desvaleurs afin que id_article soit transmis dans le GET
		unset($valeurs['id_article']);
	}

	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_relecture_identifier_dist($id_relecture='oui'){
	return serialize(array(intval($id_relecture)));
}

function formulaires_editer_relecture_verifier_dist($id_relecture='oui', $redirect='') {
	$erreurs = formulaires_editer_objet_verifier('relecture', $id_relecture, array('description'));
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_relecture_traiter_dist($id_relecture='oui', $redirect='') {

	// Traitement particulier de l'objet relecture si l'appel correspond a une ouverture :
	// - recuperation des informations de l'article concerne (id, chapo, texte, descriptif, ps et la revision courante)
	// - mise a jour de la date d'ouverture
	if ($id_relecture == 'oui') {
		if ($id_article = intval(_request('id_article'))) {
			$select = array('id_article, chapo AS article_chapo', 'descriptif AS article_descr', 'texte AS article_texte', 'ps AS article_ps');
			$from = 'spip_articles';
			$where = array("id_article=$id_article");
			$article = sql_fetsel($select, $from, $where);

			$from = 'spip_versions';
			$where = array("objet=" . sql_quote('article'), "id_objet=$id_article");
			$revision = sql_getfetsel('max(id_version) AS revision_ouverture', $from, $where);

			foreach ($article as $_cle => $_valeur) {
				set_request($_cle, $_valeur);
			}
			set_request('revision_ouverture', $revision);
			set_request('statut', 'ouverte');

			// Pour eviter que le traitement standard ne cree un enregistrement dans la table spip_auteurs_liens
			// il faut supprimer la reference a l'auteur connecte
			set_request('id_auteur','');
		}
	}

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