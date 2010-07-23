<?php
/**
 * Plugin Canevas pour Spip 2.0
 * Licence GPL
 * 
 *
 */
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_editer_canevas_charger_dist($id_canevas='new', $retour=''){
	
	$row = sql_fetsel('*','spip_canevas','id_canevas='.intval($id_canevas));
	
	$valeurs = formulaires_editer_objet_charger('canevas',$id_canevas,0,0,$retour,'',$row,$hidden);

	if (intval($id_canevas)){
		foreach($row as $key=>$val)
			$valeurs[$key] = $val;
	}

	$valeurs['editable'] = true;

	return $valeurs;
}

function formulaires_editer_canevas_verifier_dist($id_canevas='new', $retour=''){
	
	if (!$titre = _request('titre'))
		$erreurs['titre'] = _T('canevas:erreur_titre_obligatoire');

	return $erreurs;
}

function formulaires_editer_canevas_traiter_dist($id_canevas='new', $retour=''){

	$message = "";
	$action_editer = charger_fonction("editer_canevas",'action');

	list($id,$err) = $action_editer();
	if ($err){
		$message .= $err;
	}
	elseif ($retour) {
		include_spip('inc/headers');
		//$retour = parametre_url($retour,'id_canevas',$id);
		$message .= redirige_formulaire($retour);
	}
	return $message;
}

?>
