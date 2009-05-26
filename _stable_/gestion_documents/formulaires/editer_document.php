<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_document_charger_dist($id_document='new', $id_parent='', $retour='', $lier_trad=0, $config_fonc='documents_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('document',$id_document,$id_parent,$lier_trad,$retour,$config_fonc,$row,$hidden);
	
	// relier les parents
	$valeurs['id_parents'] = array();
	$valeurs['_hidden'] = "";
	$parents = sql_allfetsel('objet,id_objet','spip_documents_liens','id_document='.intval($id_document));
	foreach($parents as $p){
		if (in_array($p['objet'],array('article','rubrique')))
			$valeurs['id_parents'][] = $p['objet'].'|'.$p['id_objet'];
		else 
			$valeurs['_hidden'] .= "<input type='hidden' name='id_parents[]' value='".$p['objet'].'|'.$p['id_objet']."' />";
	}

	$valeurs['saisie_date'] = affdate($valeurs['date'],'d/m/Y');
	$valeurs['saisie_heure'] = affdate($valeurs['date'],'H:i');
	// en fonction du format
	$valeurs['_editer_dimension'] = autoriser('tailler','document',$id_document)?' ':'';
	
	// type du document
	$valeurs['type_document'] = sql_getfetsel('titre as type_document','spip_types_documents','extension='.sql_quote($valeurs['extension']));
	if (in_array($valeurs['extension'],array('jpg','gif','png'))){
		$valeurs['apercu'] = get_spip_doc($valeurs['fichier']);
	}

	// verifier les infos de taille et dimensions sur les fichiers locaux
	// cas des maj de fichier directes par ftp
	if ($valeurs['distant']!=='oui'){
		include_spip('inc/renseigner_document');
		$infos = renseigner_taille_dimension_image(get_spip_doc($valeurs['fichier']),$valeurs['extension']);
		if ($infos['taille']!=$valeurs['taille']
			OR $infos['largeur']!=$valeurs['largeur']
			OR $infos['hauteur']!=$valeurs['hauteur']){
			$valeurs['_taille_modif'] = $infos['taille'];
			$valeurs['_largeur_modif'] = $infos['largeur'];
			$valeurs['_hauteur_modif'] = $infos['hauteur'];
			$valeurs['_hidden'].=
			"<input type='hidden' name='_taille_modif' value='".$infos['taille']."' />"
			. "<input type='hidden' name='_largeur_modif' value='".$infos['largeur']."' />"
			. "<input type='hidden' name='_hauteur_modif' value='".$infos['hauteur']."' />";
		}
	}

	return $valeurs;
}

// Choix par defaut des options de presentation
function documents_edit_config($row)
{
	global $spip_ecran, $spip_lang, $spip_display;

	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == "large")? 8 : 5;
	$config['langue'] = $spip_lang;

	$config['restreint'] = ($row['statut'] == 'publie');
	return $config;
}

function formulaires_editer_document_verifier_dist($id_document='new', $id_parent='', $retour='', $lier_trad=0, $config_fonc='documents_edit_config', $row=array(), $hidden=''){
	
	$erreurs = formulaires_editer_objet_verifier('document',$id_document,is_numeric($id_document)?array():array('titre'));

	if (!$date = recup_date(_request('saisie_date').' '._request('saisie_heure').':00')
	  OR !($date = mktime($date[3],$date[4],0,$date[1],$date[2],$date[0])))
	  $erreurs['saisie_date'] = _T('gestdoc:format_date_incorrect');
	else {
		set_request('saisie_date',date('d/m/Y',$date));
		set_request('saisie_heure',date('H:i',$date));
		set_request('date',date("Y-m-d H:i:s",$date));
	}

	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_document_traiter_dist($id_document='new', $id_parent='', $retour='', $lier_trad=0, $config_fonc='documents_edit_config', $row=array(), $hidden=''){
	if (is_null(_request('id_parents')))
		set_request('id_parents',array());

	// verifier les infos de taille et dimensions sur les fichiers locaux
	// cas des maj de fichier directes par ftp
	foreach(array('taille','largeur','hauteur') as $c)
	if (($v=_request("_{$c}_modif")) AND !_request($c)){
		set_request($c,$v);
	}

	$res = formulaires_editer_objet_traiter('document',$id_document,$id_parent,$lier_trad,$retour,$config_fonc,$row,$hidden);
	if (!isset($res['redirect']))
		$res['editable'] = true;
	if (!isset($res['message_erreur']))
		$res['message_ok'] = _L('Votre modification a &eacute;t&eacute; enregistr&eacute;e');
	return $res;
}

?>
