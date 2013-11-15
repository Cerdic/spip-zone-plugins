<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_projets_charger_dist($id_projet='new', $id_parent=0, $retour='', $lier_trad=0, $config_fonc='projet_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('projet',$id_projet,$id_parent,$lier_trad,$retour,$config_fonc,$row,$hidden);
	// il faut enlever l'id_rubrique car la saisie se fait sur id_parent
	// et id_rubrique peut etre passe dans l'url comme rubrique parent initiale
	// et sera perdue si elle est supposee saisie
	unset($valeurs['id_rubrique']);
	return $valeurs;
}

// Choix par defaut des options de presentation
// http://doc.spip.org/@articles_edit_config
function projet_edit_config($row)
{
	global $spip_ecran, $spip_lang, $spip_display;

	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
	$config['afficher_barre'] = $spip_display != 4;
	$config['langue'] = $spip_lang;

	$config['restreint'] = ($row['statut'] == 'publie');
	return $config;
}

function formulaires_editer_projets_verifier_dist($id_projet='new', $id_parent=0, $retour='', $lier_trad=0, $config_fonc='projet_edit_config', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('projet',$id_projet,array('titre'));
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_projets_traiter_dist($id_projet='new', $id_parent=0, $retour='', $lier_trad=0, $config_fonc='projet_edit_config', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('projet',$id_projet,$id_parent,$lier_trad,$retour,$config_fonc,$row,$hidden);
}

?>