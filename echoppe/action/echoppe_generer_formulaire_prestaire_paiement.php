<?php

function action_echoppe_generer_formulaire_prestaire_paiement(){
	
	$contexte = array();
	
	
	$contexte['prefix_prestataire_paiement'] = session_get("prefix_prestataire_paiement");
	$contexte['version_prestataire_paiement'] = session_get("version_prestataire_paiement");
	
	
	
	pipeline("");
	
}

?>
