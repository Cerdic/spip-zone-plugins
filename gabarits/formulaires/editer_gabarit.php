<?php
/**
 * Plugin Gabarits pour Spip 2.0
 * Licence GPL
 * 
 *
 */
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_editer_gabarit_charger_dist($id_gabarit='new', $retour=''){
	
	$row = sql_fetsel('*','spip_gabarits','id_gabarit='.intval($id_gabarit));
	
	$valeurs = formulaires_editer_objet_charger('gabarit',$id_gabarit,0,0,$retour,'',$row,$hidden);

	if (intval($id_gabarit)){
		foreach($row as $key=>$val)
			$valeurs[$key] = $val;
	}

	$valeurs['editable'] = true;

	return $valeurs;
}

function formulaires_editer_gabarit_verifier_dist($id_gabarit='new', $retour=''){
	
	if (!$titre = _request('titre'))
		$erreurs['titre'] = _T('gabarits:erreur_titre_obligatoire');

	return $erreurs;
}

function formulaires_editer_gabarit_traiter_dist($id_gabarit='new', $retour=''){

	$message = "";
	$action_editer = charger_fonction("editer_gabarit",'action');

	list($id,$err) = $action_editer();
	if ($err){
		$message .= $err;
	}
	elseif ($retour) {
		include_spip('inc/headers');
		//$retour = parametre_url($retour,'id_gabarit',$id);
		$message .= redirige_formulaire($retour);
	}
	return $message;
}

?>
