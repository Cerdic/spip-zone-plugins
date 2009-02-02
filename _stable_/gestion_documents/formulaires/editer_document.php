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
	$res = formulaires_editer_objet_traiter('document',$id_document,$id_parent,$lier_trad,$retour,$config_fonc,$row,$hidden);
	if (!isset($res['redirect']))
		$res['editable'] = true;
	if (!isset($res['message_erreur']))
		$res['message_ok'] = _L('Votre modification a &eacute;t&eacute; enregistr&eacute;e');
	return $res;
}

?>
