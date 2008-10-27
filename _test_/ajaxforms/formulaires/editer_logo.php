<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_editer_logo_charger_dist($objet, $id_objet){
	$res = array(
		'editable'=>($GLOBALS['meta']['activer_logos'] == 'oui' ? ' ' : ''),
		'logo_survol'=>($GLOBALS['meta']['activer_logos_survol'] == 'oui' ? ' ' : ''),
		'objet'=>$objet,
		'id_objet'=>$id_objet
	);

	// pas dans une boucle ? formulaire pour le logo du site
	// dans ce cas, il faut chercher un 'siteon0.ext'	
	if (!$objet)
		$_id_objet = 'site';
	else
		$_id_objet = id_table_objet($objet);
	
	// rechercher le logo de l'objet
	// la fonction prend un parametre '_id_objet' etrange : 
	// le nom de la cle primaire (et non le nom de la table)
	// ou directement le nom du raccourcis a chercher
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$etats = $res['logo_survol'] ? array('on','off') : array('on');
	foreach($etats as $etat) {
		if ($logo = $chercher_logo($id_objet, $_id_objet, $etat)){
			$res['logo_'.$etat] = $logo[0];
		}
	}
	// pas de logo_on -> pas de formulaire pour le survol
	if (!isset($res['logo_on']))
		$res['logo_survol']='';
		
	return $res;
}

function formulaires_editer_logo_verifier_dist($objet, $id_objet){
	$erreurs = array();
	// verifier les extensions
	foreach(formulaire_editer_logo_get_sources() as $etat=>$file) {
		// seulement si une reception correcte a eu lieu
		if ($file AND $file['error'] == 0) {
			if (!in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)),array('jpg','png','gif','jpeg')))
				$erreurs['logo_'.$etat] = _L('Extension non reconnue');
		}
	}
	return $erreurs;
}

function formulaires_editer_logo_traiter_dist($objet, $id_objet){
	$res = array('editable'=>' ');
	
	// pas dans une boucle ? formulaire pour le logo du site
	// dans ce cas, il faut chercher un 'siteon0.ext'	
	if (!$objet)
		$_id_objet = 'site';
	else
		$_id_objet = id_table_objet($objet);

	// supprimer l'ancien logo puis copier le nouveau
	include_spip('inc/chercher_logo');
	include_spip('inc/flock');
	$type = type_du_logo($_id_objet);
	$chercher_logo = charger_fonction('chercher_logo','inc');
	
	// effectuer la suppression si demandee d'un logo
	if (($on = _request('supprimer_logo_on')) OR (_request('supprimer_logo_off'))){
		if ($logo = $chercher_logo($id_objet, $_id_objet, $on ? 'on' : 'off'))
			spip_unlink($logo[0]);
		$res['message_ok'] = _T('ajaxform:confirmer_suppression');
	}
	
	// sinon supprimer ancien logo puis copier le nouveau
	else {
		include_spip('action/iconifier');
		$ajouter_image = charger_fonction('spip_image_ajouter','action');
		foreach(formulaire_editer_logo_get_sources() as $etat=>$file) {
			if ($file and $file['error']==0)	{
				if ($logo = $chercher_logo($id_objet, $_id_objet, $etat))
					spip_unlink($logo[0]);
				$ajouter_image($type.$etat.$id_objet," ",$file);
				$res['message_ok'] = _T('ajaxform:logo_maj');
			}
		}
	}

	return $res;
}


function formulaire_editer_logo_get_sources(){
	if (!$_FILES) $_FILES = $GLOBALS['HTTP_POST_FILES'];
	if (!is_array($_FILES)) return array();
	
	$sources = array();
	foreach(array('on','off') as $etat) {
		if ($_FILES['logo_'.$etat]['error'] == 0) {
			$sources[$etat] = $_FILES['logo_'.$etat];
		}
	}
	return $sources;
}
?>
