<?php

/**
 * Charger les valeurs par défaut du formulaire d'import des medias 
 *
 * @return array
 */
function formulaires_doc2article_charger_dist(){
	$valeurs = array();
	return $valeurs;
}

/**
 * Vérifier les données postées par le formulaire avant validation finale
 *
 * @return array
 */
function formulaires_doc2article_verifier_dist(){
	$erreurs = array();
	$fichiers= _request('fichiers');
	if($fichiers == null){
		$erreurs['message_erreur'] = _T('doc2article:erreur_fichiers');
	}
	return $erreurs;
}

/**
 * Traitement final du formulaire
 *
 * @return array
 */
function formulaires_doc2article_traiter_dist(){
	
	$message = "";
	
	$fichiers= _request('fichiers');
	$id_auteur = _request('auteur');
	$id_rubrique = _request('rubrique');
	
	if($fichiers != null){
		$message = _T('doc2article:message_ajout_ok');
		foreach($fichiers as $item){
			// ajouter le doc à la file d'attente qui sera traitée par le cron
			sql_insertq('spip_doc2article',array(
				'id_auteur' => $id_auteur,
				'id_rubrique' => $id_rubrique,
				'fichier' => $item,
				'date' => date('Y-m-d H:i:s')
			));
			spip_log("ajout dans la file d'attente : $item","doc2article");
		}
	}
	else{
		$message = _T('doc2article:selectionner_un_fichier');
	}
	return array('message_ok' => $message, 'editable' => true);
}

?>