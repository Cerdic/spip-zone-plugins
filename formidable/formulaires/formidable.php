<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/saisies');

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
		$contexte['_hidden'] = '<input type="hidden" name="id_formulaire" value="'.$contexte['id'].'"/>';
	}	
	
	return $contexte;
}

function formulaires_formidable_verifier($id_formulaire){
	$erreurs = array();
	
	$id_formulaire = intval(_request('id_formulaire'));
	$formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id_formulaire);
	$saisies = unserialize($formulaire['saisies']);
	
	$erreurs = saisies_verifier($saisies);
		
	return $erreurs;
}

function formulaires_formidable_traiter($id_formulaire){
	$retours = array();
	
	$id_formulaire = intval(_request('id_formulaire'));
	$formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id_formulaire);
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	
	if (is_array($traitements) and !empty($traitements))
		foreach($traitements as $type_traitement=>$options){
			if ($appliquer_traitement = charger_fonction($type_traitement, 'traiter/', true))
				$retours = array_merge($retours, $appliquer_traitement($saisies, $options, $retours));
		}
	else{
		$retours['message_ok'] = _T('formidable:retour_aucun_traitement');
	}
	
	return $retours;
}

?>
