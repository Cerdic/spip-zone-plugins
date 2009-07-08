<?php

function echoppe_valider_informations_livraison($id_auteur){
	
	$erreurs = array();
	
	$demande = Array(
					"nom_livraison",
					"adresse_livraison",
					"ville_livraison",
					"code_postal_livraison",
					"ville_livraison",
					"pays_livraison"
					);
	$auteur = sql_fetsel($demande,"spip_auteurs_elargis",array("id_auteur"=>sql_quote($id_auteur)));
	
	
	
	if (!isset($auteur['nom_livraison'])){
		$erreurs[] = "nom_livraison" ;
	}
	
	
	if (!isset($auteur['adresse_livraison'])){
		$erreurs[] = "adresse_livraison";
	}
	
	
	if (!isset($auteur['ville_livraison'])){
		$erreurs[] = "ville_livraison";
	}
	
	
	if (!isset($auteur['code_postal_livraison'])){
		$erreurs[] = "code_postal_livraison";
	}
	
	
	if (strlen($auteur['ville_livraison']) < 1){
		$erreurs[] = "ville_livraison";
	}
	
	if (!isset($auteur['pays_livraison'])){
		$erreurs[] = "pays_livraison";
	}
	
	return $erreurs;
	
}

function echoppe_valider_informations_facturation(){
	$compte_banquaire = lire_config('echoppe/numero_de_compte_beneficiaire');
	$email_ben = lire_config('echoppe/email_beneficiaire');
	if (strlen($compte_banquaire) >= 14 && isset($email_ben)){
		return true;
	}else{
		return false;
	}
}

?>
