<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauver_produit(){
	$lang_produit = _request('lang_produit');
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
	
	$tva = str_replace(" ","",$tva);
	$tva = str_replace(".","",$tva);
	$tva = str_replace(",",".",$tva);
	
	$prix_base_htva = str_replace(" ","",$prix_base_htva);
	$prix_base_htva = str_replace(".","",$prix_base_htva);
	$prix_base_htva = str_replace(",",".",$prix_base_htva);
	
	
	$poids = str_replace(" ","",$poids);
	$poids = str_replace(".","",$poids);
	$poids = str_replace(",",".",$poids);
	
	$largeur = str_replace(" ","",$largeur);
	$largeur = str_replace(".","",$largeur);
	$largeur = str_replace(",",".",$largeur);
	
	$longueur = str_replace(" ","",$longueur);
	$longueur = str_replace(".","",$longueur);
	$longueur = str_replace(",",".",$longueur);
	
	
	$hauteur = str_replace(" ","",$hauteur);
	$hauteur = str_replace(".","",$hauteur);
	$hauteur = str_replace(",",".",$hauteur);
	
	
	//echo $new.'<---';
	
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
			'lang'=>$lang_produit,
			'titre'=> $titre,
			'descriptif'=> $descriptif,
			'texte' => $texte,
			'ps' => $ps,
			'tva' => $tva,
			'quantite_mini' => $quantite_mini
			);
			$id_produit = sql_insertq('spip_echoppe_produits',$arg_inser_produit);
			//echo $sql_insert_produit_descriptif.'<hr />';
			
			break;
		
		default :
			$arg_inser_produit = array(
			'id_produit' => $id_produit,
			'id_categorie' => $id_categorie,
			'ref_produit' => $ref_produit,
			'titre'=> $titre,
			'descriptif'=> $descriptif,
			'texte' => $texte,
			'ps' => $ps,
			'tva' => $tva,
			'quantite_mini' => $quantite_mini
			);
			sql_updateq('spip_echoppe_produits',$arg_inser_produit);
			break;
		
	}
	
	$redirect = generer_url_ecrire('echoppe_produit', 'id_produit='.$id_produit,'&');
	
	redirige_par_entete($redirect);
}

?>
