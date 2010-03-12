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
		$contenu = unserialize($formulaire['contenu']);
		if (!is_array($contenu)) $contenu = array();
		$contexte['_contenu'] = $contenu;
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
	$contenu = session_get("constructeur_formulaire_formidable_$id_formulaire");
	$contenu = serialize($contenu);
	
	// On l'envoie dans la table
	$ok = sql_updateq(
		'spip_formulaires',
		array(
			'contenu' => $contenu
		),
		'id_formulaire = '.$id_formulaire
	);
	
	if ($ok){
		$retours['message_ok'] = 'Le formulaire a bien été enregistré.';
		$retours['editable'] = true;
	}
	
	return $retours;
}

?>
