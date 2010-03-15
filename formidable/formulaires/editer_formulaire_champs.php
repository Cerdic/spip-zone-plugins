<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_editer_formulaire_champs_charger($id_formulaire){
	$contexte = array();
	$id_formulaire = intval($id_formulaire);
	
	// On teste si le formulaire existe
	if ($id_formulaire
		and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id_formulaire)
		and autoriser('editer', 'formulaire', $id_formulaire)
	){
		$saisies = unserialize($formulaire['saisies']);
		if (!is_array($saisies)) $saisies = array();
		$contexte['_saisies'] = $saisies;
		$contexte['id'] = $id_formulaire;
	}
	
	return $contexte;
}

function formulaires_editer_formulaire_champs_verifier($id_formulaire){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_editer_formulaire_champs_traiter($id_formulaire){
	$retours = array();
	$id_formulaire = intval($id_formulaire);
	
	// On récupère le formulaire dans la session
	$saisies = session_get("constructeur_formulaire_formidable_$id_formulaire");
	$saisies = serialize($saisies);
	
	// On l'envoie dans la table
	$ok = sql_updateq(
		'spip_formulaires',
		array(
			'saisies' => $saisies
		),
		'id_formulaire = '.$id_formulaire
	);
	
	// Si c'est bon on renvoie vers la config des traitements
	if ($ok){
		$retours['redirect'] = parametre_url(
			parametre_url(
				parametre_url(
					generer_url_ecrire('formulaires_editer')
					, 'id_formulaire', $id_formulaire
				)
				, 'configurer', 'traitements'
			)
			, 'avertissement', 'oui'
		);
	}
	
	return $retours;
}

?>
