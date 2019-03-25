<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2017                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion du formulaire de d'édition d'article
 *
 * @package SPIP\Core\Articles\Formulaires
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_annonce_charger_dist($id_article='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='articles_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('article',$id_article,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	// il faut enlever l'id_rubrique car la saisie se fait sur id_parent
	// et id_rubrique peut etre passe dans l'url comme rubrique parent initiale
	// et sera perdue si elle est supposee saisie
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_annonce_identifier_dist($id_article='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='articles_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_article), $lier_trad));
}

/**
 * Choix par défaut des options de présentation
 *
 * @param array $row
 *     Valeurs de la ligne SQL d'un article, si connu
 * return array
 *     Configuration pour le formulaire
 */
function articles_edit_config($row) {
	global $spip_lang;

	$config = $GLOBALS['meta'];
	$config['lignes'] = 8;
	$config['langue'] = $spip_lang;

	$config['restreint'] = ($row['statut'] == 'publie');
	$config['statut'] = 'publie';
	$row['statut'] == 'publie';
	return $config;
}

function formulaires_editer_annonce_verifier_dist($id_article='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='articles_edit_config', $row=array(), $hidden=''){
	// auto-renseigner le titre si il n'existe pas
// 	titre_automatique('titre', array('descriptif', 'chapo', 'texte'));
	// on ne demande pas le titre obligatoire : il sera rempli a la volee dans editer_article si vide
	//V onriis 2018
// 	$erreurs = formulaires_editer_objet_verifier('article',$id_article,array('id_parent'));
	$titre = _request('titre');
	$texte = _request('texte');
	if (empty($titre)) {
		$erreurs['message_erreur'] = _T('petitesannonces:info_letitrenedoitpasetrevide');
	}
	if (empty($texte)) {
		$erreurs['message_erreur'] = _T('petitesannonces:info_letextenedoitpasetrevide');
	}
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_annonce_traiter_dist($id_article='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='articles_edit_config', $row=array(), $hidden=''){
	
	$result = formulaires_editer_objet_traiter('article',$id_article,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	
	// obligé de passer par la, visiteur (env 6forum) crée un article qui se retrouve toujours en "prepa"
	// semble venir de ecrire/inc/editer function formulaires_editer_objet_traiter
	// ==> a posteriori on passe l'article en prop a la place de prepa
	$idarticle = _request('id_article');
	// si l'article existe et qu'on peut le modifier c'est qu'il est publie on le garde en publie
	// sinon on le met tout de suite en prop et il n'est plus modifiable jusqu'a publication par un admin
	if ($idarticle != '0') {
		$set=array('statut'=>"'publie'");
	} else {
		$set=array('statut'=>"'prop'");
	}
	$last_insert_id=$result['id_article'];
	// il y a peut-etre plus joli mais bon...
	sql_update('spip_articles',$set, "id_article=".$last_insert_id);
	
	return $result;
}
