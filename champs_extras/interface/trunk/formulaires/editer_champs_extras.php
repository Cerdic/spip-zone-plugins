<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_editer_champs_extras_charger_dist($objet, $redirect=''){
	$valeurs = array(
		'objet' => $objet,
		'redirect' => $redirect
	);

	include_spip('inc/iextras');
	$saisies = iextras_champs_extras_definis( table_objet_sql($objet) );

	$valeurs['_saisies'] = $saisies;
	$valeurs['_options'] = array(
		"modifier_nom"=>true,
		"nom_unique"=>true
	);
		
	return $valeurs;
}


function formulaires_editer_champs_extras_verifier_dist($objet, $redirect=''){
	$erreurs = array();
	return $erreurs;
}


function formulaires_editer_champs_extras_traiter_dist($objet, $redirect=''){
	$retour = array(
		'redirect' => $redirect
	);
	
	include_spip('inc/iextras');
	$saisies = iextras_champs_extras_definis( table_objet_sql($objet) );
	

	$nouvelles_saisies = session_get('constructeur_formulaire_champs_extras_' . $objet);
	$diff = saisies_comparer_par_identifiant($saisies, $nouvelles_saisies);

	$extras = array();
	$table = table_objet_sql($objet);

	include_spip('inc/cextras');
	// supprimer les champs supprimes
	champs_extras_supprimer($table, $diff['supprimees']);
	// ajouter les nouveaux champs;
	champs_extras_creer($table, $diff['ajoutees']);
	// modifier les champs modifies;
	# champs_extras_modifier($table, $diff['modifiees']);	
	# champs_extras_modifier($table, # modifiees nouvelles, # modifiees anciennes);	

	
	ecrire_meta("champs_extras_" . $table, serialize($nouvelles_saisies));
	$retour['message_ok'] = 'Super !';
	return $retour;
}


?>
