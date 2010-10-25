<?php
function formulaires_annuaire_charger_dist() {
	$valeurs = array(
		"id_auteur" => "",
		"prenom" => "",
		"nom" => "",
		"tel_fixe" => "",
		"tel_portable" => "",
		"adhesion" => "",
	);
	return $valeurs;
}

function formulaires_annuaire_verifier_dist(){
	$erreurs = array();
	$verifier = charger_fonction('verifier','inc',true);
	if (($erreur = $verifier(_request('tel_fixe'),'telephone')) !== '') {
		$erreurs['tel_fixe'] = $erreur;
	}
	if (($erreur = $verifier(_request('tel_portable'),'telephone')) !== '') {
		$erreurs['tel_portable'] = $erreur;
	}
	return $erreurs;
}

function formulaires_annuaire_traiter_dist(){
	refuser_traiter_formulaire_ajax();

	$auteur = _request('id_auteur');
	$prenom = _request('prenom');
	$nom = _request('nom');
	$tel_fixe = _request('tel_fixe');
	$tel_portable = _request('tel_portable');
	$adhesion = _request('adhesion');

	$id_produit = sql_insertq (
						'spip_amap_personnes', 
						array(
							"id_auteur" => $auteur,
							"prenom" => $prenom,
							"nom" => $nom,
							"tel_fixe" => $tel_fixe,
							"tel_portable" => $tel_portable,
							"adhesion" => $adhesion,
							)
	);
	// Valeurs de retours
	$message['message_ok'] = _T('amap:annuaire_enregistre');
	return $message;
}
?>
