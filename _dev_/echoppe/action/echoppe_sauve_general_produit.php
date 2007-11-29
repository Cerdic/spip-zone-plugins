<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_sauve_general_produit(){
	$lang_produit = _request('lang_produit');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$texte = _request('texte');
	$ps = _request('ps');
	$id_categorie = _request('id_categorie');
	$id_new_categorie = _request('id_new_categorie');
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
	
	$sql_maj_produit_general = "UPDATE spip_echoppe_produits SET date_debut = '".$date_debut."', date_fin = '".$date_fin."', poids = '".$poids."', hauteur = '".$hauteur."', longueur = '".$longueur."', largeur= '".$largeur."', colisage = '".$colisage."', ref_produit ='".$ref_produit."', prix_base_htva='".$prix_base_htva."' WHERE id_produit = '".$id_produit."';";
	$res_maj_produit_general = spip_query($sql_maj_produit_general);
	
	$sql_maj_lien_categorie = "UPDATE spip_echoppe_categories_produits SET id_categorie = '".$id_new_categorie."' WHERE id_produit = '".$id_produit."';";
	$res_maj_lien_categorie = spip_query($sql_maj_lien_categorie);

	$redirect = generer_url_ecrire('echoppe_produit', 'id_produit='.$id_produit,'&');
	//echo $redirect;
	redirige_par_entete($redirect);
}

?>
