<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérifier une saisie de type upload
 * 
 * @pipeline formulaire_verifier()
 * @param array $saisie description de la saisie
 * @return array tableau d'erreur
 */
function inc_verifier_upload_dist($saisie){
	$erreur = '';
	// Récupérer les infos sur le fichier
	$nom = $saisie['options']['nom'];
	$file = $_FILES[$nom];
	// D'abord verifier si cela devait être rendu obligatoire
	if ($file['error']=='4' and $saisie['options']['obligatoire'] == 'on'){
		$erreur = (isset($saisie['options']['erreur_obligatoire']) and $saisie['options']['erreur_obligatoire'])
		? $saisie['options']['erreur_obligatoire']
		: _T('info_obligatoire');
		return $erreur;
	}
	
	// Vérifier ensuite le mime type
	if ($saisie['options']['tous_mime_type'] == 'on'){
		$mime_types_possibles = sql_allfetsel('extension,mime_type AS type','spip_types_documents');
	}
	else{
		$mime_types_possibles = array();
		foreach ($saisie['options']['uniquement_mime_type'] as $desc){
			$explode = explode("+++",$desc);
			$mime_types_possibles[] = array('type' => $explode[0],'extension'=>$explode[1]);
		}
	}
	$verifier = charger_fonction('verifier', 'inc', true);
	$options = array();
	return $verifier($file, 'upload_document', array('types'=>$mime_types_possibles));
	
	
}
