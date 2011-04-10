<?php

function formulaires_plugonet_verifier_charger(){
	$valeurs = array('pluginxml' => _request('pluginxml'));
	return $valeurs;
}

function formulaires_plugonet_verifier_verifier(){
	$erreurs = array();
	$obligatoires = array('pluginxml');
	foreach($obligatoires as $_obligatoire){
		if(!_request($_obligatoire)){
			$erreurs[$_obligatoire] = _T('langonet:message_nok_champ_obligatoire');
		}
	}
	return $erreurs;
}

function formulaires_plugonet_verifier_traiter(){
	// Recuperation des champs du formulaire
	$pluginxml = _request('pluginxml');

	// Verification du fichier
	$valider_xml = charger_fonction('valider', 'xml');
	$erreurs = array();
	if (lire_fichier($pluginxml, $contenu))
		$resultats = $valider_xml($contenu, false, false, 'plugin.dtd');
	$erreurs = is_array($resultats) ? $resultats[1] : $resultats->err; //2.1 ou 2.2
var_dump($erreurs);

	foreach ($erreurs as $_k => $_erreur) {
		$message = preg_replace('@<br[^>]*>|:|,@', ' ', $_erreur[0]);
		$message = preg_replace(',<b>[^>]*</b>,', '* ', $message);
		$message = "Ligne $_erreur[1] - " . $_erreur[0] . "\n";
	}
// 	$msg2 = $nom;
// 	if ($n = count($erreurs)) {
// 	$total+=$n;
// 	$ko++;
// 	$msg2 .= ' ' . $n . " erreur(s)" . $sep . join($sep, $erreurs);
// 	}
// 	$dir = dirname($nom);
// 	if ($old) {
// 	if (!$infos = $informer_xml(basename($dir), true, dirname($dir) .'/'))
// 	$msg2 .= " plugin.xml illisible";
	
	return array('message_ok' => $message, 'editable' => true);;
}

?>