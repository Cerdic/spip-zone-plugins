<?php

function formulaires_plugonet_generer_charger(){
	$valeurs = array();
	return $valeurs;
}

function formulaires_plugonet_generer_verifier(){
	$erreurs = array();
	$obligatoires = array('pluginxml');
	foreach($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_plugonet_generer_traiter(){
	// Recuperation des champs du formulaire
	$champs = array('pluginxml', 'encodage');
	foreach($champs as $_champ){
		$champs[$_champ] = _request($_champ);
	}

	// Generation du fichier
 	$generer = charger_fonction('plugonet_generer','inc');
 	list($msg, $erreurs, $res) = $generer($champs['pluginxml'], true);

	// Traitement et affichage des resultats
	if ($erreurs)
		$msg .= join("<br />\n",  $erreurs) . "<br /><br />";
	else 
		$msg = '';
	foreach ($res as $nom => $xml) {
		preg_match_all('/<!--(.*?)-->/', $xml, $m);
		$msg .="<fieldset style='border: 0.1em solid; margin:1em'><legend>$nom</legend><code>" . join("<br/>", $m[1]) . "</code></fieldset>";
	}
	
	return array('message_ok' => $msg, 'editable' => true);;
}

?>