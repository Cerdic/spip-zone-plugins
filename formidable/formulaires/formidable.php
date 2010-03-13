<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_formidable_charger($id_formulaire){
	$contexte = array();
	
	// On peut donner soit un id soit un identifiant
	if (intval($id_formulaire) > 0)
		$where = 'id_formulaire = '.intval($id_formulaire);
	elseif (is_string($id_formulaire))
		$where = 'identifiant = '.sql_quote($id_formulaire);
	else
		return;
	
	// On cherche si le formulaire existe
	if ($formulaire = sql_fetsel('*', 'spip_formulaires', $where)){
		$saisies = unserialize($formulaire['saisies']);
		// On déclare les champs
		$contexte = array_fill_keys(saisies_lister_champs($saisies), '');
		// On ajoute le formulaire complet
		$contexte['_saisies'] = $saisies;
		$contexte['id'] = $formulaire['id_formulaire'];
	}	
	
	return $contexte;
}

function formulaires_formidable_verifier($id_formulaire){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_formidable_traiter($id_formulaire){
	$retours = array();
	
	return $retours;
}

?>
