<?php
/**
 * Plugin catalogue pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

function formulaires_etape1_charger_dist(){
	$valeurs = array(
		'id_variante'=>'',
		'id_option'=>'',
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
	$montant_variante=0;
	$montant_options=0;	
	$montant_total;
	
	// On calcule d'abord le montant de la variante
	// On récupère d'abord la variante
	$id_variante = _request('id_variante');
	// Ensuite le montant
	$infos = sql_allfetsel('prix_ht','spip_variantes','id_variante='.$id_variante);	
	$montant_variante = $infos[0]['prix_ht'];
	// Ensuite on calcule le montant d'après les options
	// On récupère les options
	// Tant qu'il y a des options dans le tableau
	// on fait la somme des prix_ht

	// le tableau contient des id de checkboxes cochées
	$id_option = _request('id_option');	
	$id_option = (is_array($id_option) ? $id_option : array($id_option));
	foreach ($id_option as $id) {
		$montant_options += sql_getfetsel('prix_ht', 'spip_options', 'id_option='.sql_quote($id));	
	}
	
	// maintenant on fait le total
	$montant_total = $montant_variante + $montant_options;
	set_request('montant_total', $montant_total);

	$message_ok = "<p>Merci pour votre choix; veuillez maintenant saisir vos informations personnelles.</p>";
	return array('message_ok'=>$message_ok);
}

?>