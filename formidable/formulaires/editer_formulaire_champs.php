<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_editer_formulaire_champs_charger($id_formulaire){
	$contexte = array();
	$id_formulaire = intval($id_formulaire);
	
	// On teste si le formulaire existe
	if ($id_formulaire and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id_formulaire)){
		$contenu = unserialize($formulaire['contenu']);
		if (!is_array($contenu)) $contenu = array();
		$contexte['_contenu'] = $contenu;
	}
	
	return $contexte;
}

function formulaires_editer_formulaire_champs_verifier($id_formulaire){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_editer_formulaire_champs_traiter($id_formulaire){
	$retours = array();
	
	return $retours;
}

?>
