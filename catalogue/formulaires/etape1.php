<?php
/**
 * Plugin catalogue pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function formulaires_etape1_charger_dist(){
	$valeurs = array(
		'id_variante'=>'',
		'montant'=>''
	);

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
	/**
	 * Il faut maintenant calculer le montant de la transaction
	 * à partir de la somme du montant de la variante sélectionnée
	 * additionnée au montant de la ou des options choisies
	 * On place cette valeur dans la variable $montant
	 */
	$montant = 20;
	$message_ok = "<p>Merci pour votre choix; veuillez maintenant saisir vos informations personnelles.</p>";
	return array('message_ok'=>$message_ok);
}

?>