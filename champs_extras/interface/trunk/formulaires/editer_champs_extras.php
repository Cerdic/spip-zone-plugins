<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_editer_champs_extras_charger_dist($objet, $redirect=''){
	$valeurs = array(
		'objet' => $objet,
		'redirect' => $redirect
	);

	$saisies = unserialize( $GLOBALS['meta']["champs_extras_$objet"] );

	if (!is_array($saisies)) $saisies = array();
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
	
	$saisies = unserialize( $GLOBALS['meta']["champs_extras_$objet"] );
	if (!is_array($saisies)) $saisies = array();
	
	include_spip('inc/saisies');
	$nouvelles_saisies = session_get('constructeur_formulaire_champs_extras_' . $objet);
	$diff = saisies_comparer($saisies, $nouvelles_saisies);
	
	$extras = array();
	$table = table_objet_sql($objet);

	foreach ($diff['ajoutees'] as $saisie) {
		$nom = $saisie['options']['nom'];
		// a corriger
		if (true OR $sql = $saisie['options']['sql']) {
			$sql = "text default '' not null";
			$extra = new ChampExtra(array(
				'champ' => $nom,
				'table' => $table,
				'sql' => $sql
			));
			$extras[] = $extra;
		}
	}

	// l'enregistrer les modifs
	include_spip('inc/cextras_gerer');
	creer_champs_extras($extras);	
	
	ecrire_meta("champs_extras_$objet", serialize($nouvelles_saisies));
	$retour['message_ok'] = 'Super !';
	return $retour;
}

?>
