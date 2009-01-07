<?php

function formulaires_editer_presentation_produit_charger_dist(){
	$valeurs['new'] = _request('new');
	if ($valeurs['new'] != "oui"){
		$a_editer = array(
			'id_categorie',
			'id_produit',
			'ref_produit',
			'lang',
			'titre',
			'descriptif',
			'texte',
			'ps',
			'tva',
			'quantite_mini',
			'id_trad',
		);
		$valeurs = sql_fetsel($a_editer,'spip_echoppe_produits',"id_produit = '"._request('id_produit')."'");
	} else {
		$valeurs['id_trad'] = _request('id_trad');
		if ($valeurs['id_trad'] > 0){
			$valeurs['ref_produit'] = _request('ref_produit');
		}
		$valeurs['id_categorie'] = _request('id_categorie');
	}

	return $valeurs;
}

function formulaires_editer_presentation_produit_verifier_dist(){
	$erreurs = array();
	
	foreach(array('ref_produit','titre') as $obligatoire){
		if (!_request($obligatoire)) $erreurs[$obligatoire] = _T('echoppe:ce_champ_est_obligatoire');
	}
	
	include_spip('inc/echoppe');
	if (!ref_produit_unique(_request('ref_produit'), _request('id_produit')))
		$erreurs['ref_produit'] = _T('echoppe:cette_ref_produit_n_est_pas_unique');

	if (count($erreurs))
		$erreurs['message_erreur'] = _T('echoppe:votre_saisie_contient_des_erreurs');
	
	return $erreurs;
	
}

function formulaires_editer_presentation_produit_traiter_dist(){
	
	include_spip('inc/headers');
	
	$lang = _request('lang');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$texte = _request('texte');
	$ps = _request('ps');
	$id_categorie = _request('id_categorie');
	$id_produit = _request('id_produit');
	$new = _request('new');
	$ref_produit = _request('ref_produit');
	$quantite_mini = _request('quantite_mini');
	$tva = _request('tva');
	$date_debut = _request('annee_date_en_ligne').'-'._request('mois_date_en_ligne').'-'._request('jour_date_en_ligne').' 00:00:00';
	$date_fin = _request('annee_date_retrait_ligne').'-'._request('mois_date_retrait_ligne').'-'._request('jour_date_retrait_ligne').' 00:00:00';
	$poids = _request('poids');
	$largeur = _request('largeur');
	$longueur = _request('longueur');
	$hauteur = _request('hauteur');
	$colisage = _request('colisage');
	$ref_produit = _request('ref_produit');
	$prix_base_htva = _request('prix_base_htva');
	$id_trad = _request('id_trad');
	
	switch ($new){
		case 'oui':
			$arg_inser_produit = array(
			'id_produit' => '',
			'id_categorie' => $id_categorie,
			'date_debut' => $date_debut,
			'date_fin' => $date_in,
			'poids' => $poids,
			'hauteur' => $hauteur,
			'largeur' => $largeur,
			'longueur' => $longueur,
			'colisage' => $colissage,
			'ref_produit' => $ref_produit,
			'prix_base_htva' => $prix_base_htva,
			'maj' => $maj,
			'statut' => "prepa",
			'lang'=>$lang,
			'id_trad' => $id_trad,
			'titre'=> $titre,
			'descriptif'=> $descriptif,
			'texte' => $texte,
			'ps' => $ps,
			'tva' => $tva,
			'quantite_mini' => $quantite_mini
			);
			$id_produit = sql_insertq('spip_echoppe_produits',$arg_inser_produit);
			
			if (empty($id_trad)) sql_updateq('spip_echoppe_produits',array('id_trad' => $id_produit),"id_produit='".$id_produit."'");
			
			break;
		
		default :
			$arg_inser_produit = array(
			'id_categorie' => $id_categorie,
			'ref_produit' => $ref_produit,
			'lang' => $lang,
			'titre'=> $titre,
			'descriptif'=> $descriptif,
			'texte' => $texte,
			'ps' => $ps,
			'tva' => $tva,
			'quantite_mini' => $quantite_mini
			);
			sql_updateq('spip_echoppe_produits',$arg_inser_produit,"id_produit='".$id_produit."'");
			break;
	}
	//return array('message_ok'=>_T('echoppe:produit_enregistre'));
	
	$redirect = generer_url_ecrire('echoppe_produit', 'id_produit='.$id_produit,'&');
	
	redirige_par_entete($redirect);
}


?>
