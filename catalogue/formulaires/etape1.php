<?php
/**
 * Plugin catalogue pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function formulaires_etape1_charger_dist(){
	$valeurs = array('id_variante'=>'');
	return $valeurs;
}


function formulaires_etape1_verifier_dist(){
	$erreurs = array();
	// verifier que le champ formule est bien sélectionné :

	if (!_request('id_variante'))
			$erreurs['id_variante'] = 'Merci de choisir une formule parmi les formules propos&eacute;es ci-dessous.';
	
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient une ou plusieurs erreurs.';
		
	return $erreurs;
}

function formulaires_etape1_traiter_dist(){
	$message_ok = "<p>Merci pour votre choix; veuillez maintenant saisir vos informations personnelles.</p>";
	return array('message_ok'=>$message_ok);
}

?>