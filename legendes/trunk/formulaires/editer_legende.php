<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_editer_legende_charger_dist($id_legende='new', $id_document='', $retour=''){
	
	$row = sql_fetsel('*','spip_legendes','id_legende='.intval($id_legende));
	
	$valeurs = formulaires_editer_objet_charger('legende',$id_legende,0,0,$retour,'',$row,$hidden);

	if (intval($id_legende)){
		foreach($row as $key=>$val)
			$valeurs[$key] = $val;
	}else{
		$valeurs['id_document'] = $id_document;
	}

	$valeurs['editable'] = true;

	return $valeurs;
}

function formulaires_editer_legende_verifier_dist($id_legende='new', $id_document='', $retour=''){
	$erreurs = array();
	return $erreurs;
}

function formulaires_editer_legende_traiter_dist($id_legende='new', $id_document='', $retour=''){
	
	$message = array('editable'=>true, 'message_ok'=>'');

	if (_request('effacer')) {
		include_spip("action/editer_legende");
		legendes_action_supprime_legende($id_legende);
		$message['message_ok'] = _T("legendes:legende_supprimer_ok");
	}

	if (_request('valider')) {
		$action_editer = charger_fonction("editer_legende",'action');

		list($id,$err) = $action_editer();
		$message['message_ok'] = _T("legendes:legende_enregistrer_ok");
		set_request('id_legende',$id);
		if ($err){
			$message .= $err;
		}
		elseif ($retour) {
			include_spip('inc/headers');
			//$retour = parametre_url($retour,'id_legende',$id);
			$message .= redirige_formulaire($retour);
		}
	}
	
	return $message;
	
}

?>
