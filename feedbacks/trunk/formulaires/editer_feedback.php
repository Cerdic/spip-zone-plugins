<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_feedback_charger_dist($id_feedback='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='feedbacks_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('feedback',$id_feedback,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	// un bug a permis a un moment que des feedback soient dans des sous rubriques
	// lorsque ce cas se presente, il faut relocaliser la feedback dans son secteur, plutot que n'importe ou
	if ($valeurs['id_parent'])
		$valeurs['id_parent'] = sql_getfetsel('id_secteur','spip_rubriques','id_rubrique='.intval($valeurs['id_parent']));
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_feedback_identifier_dist($id_feedback='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='feedbacks_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_feedback),$lier_trad));
}


// Choix par defaut des options de presentation
function feedbacks_edit_config($row)
{
	global $spip_lang;

	$config = $GLOBALS['meta'];
	$config['lignes'] = 8;
	$config['langue'] = $spip_lang;

	$config['restreint'] = ($row['statut'] == 'publie');
	return $config;
}

function formulaires_editer_feedback_verifier_dist($id_feedback='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='feedbacks_edit_config', $row=array(), $hidden=''){
	// auto-renseigner le titre si il n'existe pas
	titre_automatique('titre',array('texte'));
	// on ne demande pas le titre obligatoire : il sera rempli a la volee dans editer_article si vide
	$erreurs = formulaires_editer_objet_verifier('feedback',$id_feedback,array('id_parent'));
	return $erreurs;
}

// http://code.spip.net/@inc_editer_article_dist
function formulaires_editer_feedback_traiter_dist($id_feedback='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='feedbacks_edit_config', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('feedback',$id_feedback,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
}

?>
