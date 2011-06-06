<?php

function formulaires_asso_etiquettes_param_charger_dist(){	
	
	include_spip('base/abstract_sql');
	$valeurs = array();

	$valeurs['nb_colonne']				= $GLOBALS['association_metas']['etiquette_nb_colonne'];
	$valeurs['nb_ligne']				= $GLOBALS['association_metas']['etiquette_nb_ligne'];
	$valeurs['largeur_page']			= $GLOBALS['association_metas']['etiquette_largeur_page'];
	$valeurs['hauteur_page']			= $GLOBALS['association_metas']['etiquette_hauteur_page'];
	$valeurs['marge_haut_page']			= $GLOBALS['association_metas']['etiquette_marge_haut_page'];
	$valeurs['marge_bas_page']			= $GLOBALS['association_metas']['etiquette_marge_bas_page'];
	$valeurs['marge_droite_page']		= $GLOBALS['association_metas']['etiquette_marge_droite_page'];
	$valeurs['marge_gauche_page']		= $GLOBALS['association_metas']['etiquette_marge_gauche_page'];
	$valeurs['marge_haut_etiquette']	= $GLOBALS['association_metas']['etiquette_marge_haut_etiquette'];
	$valeurs['marge_gauche_etiquette']	= $GLOBALS['association_metas']['etiquette_marge_gauche_etiquette'];
	$valeurs['marge_droite_etiquette']	= $GLOBALS['association_metas']['etiquette_marge_droite_etiquette'];
	$valeurs['espace_etiquettesh']		= $GLOBALS['association_metas']['etiquette_espace_etiquettesh'];
	$valeurs['espace_etiquettesl']		= $GLOBALS['association_metas']['etiquette_espace_etiquettesl'];
	$valeurs['type_sortie']				= $GLOBALS['association_metas']['etiquette_type_sortie'];	
	$valeurs['avec_civilite']			= $GLOBALS['association_metas']['etiquette_avec_civilite'];
	return $valeurs;
}

function formulaires_asso_etiquettes_param_verifier_dist(){
	$erreurs = array();	
	// Verifier si il a au moins une selection
	$etiquette = _request('statut_interne') ;
	/*if($etiquette=='')		{$erreurs['etiquette'] = _T('asso:etiquette_aucun_choix');
	$message=$erreurs['etiquette'] ;
	}*/
	if (count($erreurs))	{$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !<BR/>'.$message;	
    }
   return $erreurs; // si c'est vide, traiter sera appele, sinon le formulaire sera re-soumis
}



function formulaires_asso_etiquettes_param_traiter_dist(){
		$ok=false;
		include_spip('base/abstract_sql');
		include_spip('inc/acces');
		$message = "La configuration a bien été enregistrée";
		
		$table_meta='association_metas';
		
		$avec_civilite=_request('avec_civilite');
		ecrire_meta('etiquette_avec_civite', $avec_civilite, null, $table_meta);		

		$nb_colonne=_request('nb_colonne');
		ecrire_meta('etiquette_nb_colonne', $nb_colonne, null, $table_meta);
		$nb_ligne=_request('nb_ligne');
		ecrire_meta('etiquette_nb_ligne', $nb_ligne, null, $table_meta);
		$largeur_page=_request('largeur_page');
		ecrire_meta('etiquette_largeur_page', $largeur_page, null, $table_meta);
		$hauteur_page=_request('hauteur_page');
		ecrire_meta('etiquette_hauteur_page', $hauteur_page, null, $table_meta);
		$marge_haut_etiquette=_request('marge_haut_etiquette');
		ecrire_meta('etiquette_marge_haut_etiquette', $marge_haut_etiquette, null, $table_meta);
		$marge_haut_page=_request('marge_haut_page');
		ecrire_meta('etiquette_marge_haut_page', $marge_haut_page, null, $table_meta);
		$marge_bas_page=_request('marge_bas_page');
		ecrire_meta('etiquette_marge_bas_page', $marge_bas_page, null, $table_meta);
		$marge_gauche_page=_request('marge_gauche_page');
		ecrire_meta('etiquette_marge_gauche_page', $marge_gauche_page, null, $table_meta);
		$marge_droite_page=_request('marge_droite_page');
		ecrire_meta('etiquette_marge_droite_page', $marge_droite_page, null, $table_meta);
		$marge_gauche_etiquette=_request('marge_gauche_etiquette');
		ecrire_meta('etiquette_marge_gauche_etiquette', $marge_gauche_etiquette, null, $table_meta);
		$marge_droite_etiquette=_request('marge_droite_etiquette');
		ecrire_meta('etiquette_marge_droite_etiquette', $marge_droite_etiquette, null, $table_meta);
		$espace_etiquettesh=_request('espace_etiquettesh');
		ecrire_meta('etiquette_espace_etiquettesh', $espace_etiquettesh, null, $table_meta);
		$espace_etiquettesl=_request('espace_etiquettesl');
		ecrire_meta('etiquette_espace_etiquettesl', $espace_etiquettesl, null, $table_meta);
	
	
	return array('editable' => $ok, 'message_ok'=> $message );

}
?>
