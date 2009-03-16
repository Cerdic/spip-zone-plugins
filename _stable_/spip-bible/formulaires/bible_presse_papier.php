<?php
include_spip('inc/texte');
include_spip('inc/lien');

function formulaires_bible_presse_papier_charger_dist(){
	
	$valeurs = array('action'=>$script);
	return $valeurs;
}
function formulaires_bible_presse_papier_verifier_dist(){
	
	
	$texte = _request('bible');
	$original = $texte;
	$texte = str_replace('bible','bible_pas_propre',$texte);//on passe le paramètre "pas propre" au modèles
	
	$texte = traiter_modeles($texte);
    
	$texte = echappe_retour($texte);
	$message_ok = $texte;
	return array('message_ok'=>$message_ok,'original'=>$original);
}

function formulaires_bible_presse_papier_traiter_dist(){
	return formulaires_bible_presse_papier_verifier_dist(); // on vérifie pas ...
}



?>