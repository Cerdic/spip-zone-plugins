<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}
/**
 * Vérifier un upload de document
 *
 * @param array $valeur
 *   Le sous tableau de $_FILES à vérifier, $_FILES['logo'] par exemple
 *   Doit être un champ avec un seul upload
 * @param array $options
 *   Options à vérifier :
 *   - types (tableau de tableaux array('type' => letypemime, 'extension' =>l'extension))
 * @return string
 */
function verifier_upload_document_dist($file,$options){
	$type = $file['type'];
	$name = $file['name'];
	$pathinfo = pathinfo($file['name']);
	$extension = $pathinfo['extension'];
	$pas_bon_type = True;
	
	// Commençons par vérifier type mime et extension
	foreach ($options['types'] as $type_possible){
		if ($type_possible['extension'] == $extension and $type_possible['type'] == $type){
			$pas_bon_type = False;
			break;
		}
	}
	if ($pas_bon_type){
		return _T('verifier:erreur_type_non_autorise',array('name'=>$name));
	}
	return '';
}
