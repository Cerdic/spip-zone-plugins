<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_echoppe_publier_produit(){
	
	$id_produit = _request('id_produit');
	
	$sql_preparer_produit = "UPDATE spip_echoppe_produits SET statut = 'publie' WHERE id_produit = '".$id_produit."'; ";
	$res_preparer_produit = spip_query($sql_preparer_produit);
	
	$redirect = generer_url_ecrire('echoppe_produit', 'id_produit='.$id_produit,'&');
	//echo $redirect;
	redirige_par_entete($redirect);
}

?>
