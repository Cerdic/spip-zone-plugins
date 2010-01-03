<?php
/***************************************************************************\
 * Plugin Duplicator pour Spip 2.0
 * Licence GPL (c) 2010 - Apsulis
 * Duplication de rubriques et d'articles
 *
\***************************************************************************/

function formulaires_duplicator_confirm_charger_dist(){
	$valeurs = array();
	
	return $valeurs;
}

function formulaires_duplicator_confirm_verifier_dist($rubrique){
	$erreurs = array();

	if (!$rubrique)
		$erreurs['message_erreur'] = 'Une erreur est survenue.';
		
	return $erreurs;
}

function formulaires_duplicator_confirm_traiter_dist($rubrique){
	
	if(_request('confirmer')){
		include_spip('action/dupliquer');

		// On duplique la rubrique
		$nouvelle_rubrique = dupliquer_rubrique($rubrique);

		$message = array('message_ok'=>array(
										'message'=>_T('duplicator:operation_executee'),
										'cible'=>$nouvelle_rubrique,
										'type_retour'=>_T('duplicator:operation_retour_ok')
						));			
	}
	if(_request('annuler')){
		$message = array('message_ok'=>array(
										'message'=>_T('duplicator:operation_annulee'),
										'cible'=>$rubrique,
										'type_retour'=>_T('duplicator:operation_retour_ko')
					));			
	}

	return $message;
}
