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

	// Verification du fichier avec le valideur et la pseudo-DTD plugin.dtd
	$valider_xml = charger_fonction('valider', 'xml');
	if (lire_fichier($pluginxml, $contenu))
		$resultats = $valider_xml($contenu, false, false, 'plugin.dtd');

	// Construction du tableau des messages d'erreur de pseudo-validation XML
	// -- aucune des ces erreurs ce ne peut-etre considerees comme fatales pour la production du fichier paquet.xml
	$erreurs_valxml = array();
	$erreurs = is_array($resultats) ? $resultats[1] : $resultats->err; //2.1 ou 2.2

	if (count($erreurs) > 0)
		$message_valxml = _T('plugonet:message_nok_validation_pluginxml');

	// Verification de la lecture du fichier avec la fonction get_infos ou infos_plugin
	// -- Toute erreur de lecture est consideree comme fatale pour la production du fichier paquet.xml
	$dir = dirname($pluginxml);
	$informer_xml = charger_fonction('infos_plugin', 'plugins', true) ?
					'plugin2paquet_infos' : charger_fonction('get_infos', 'plugins');
#	if ($informer_xml == 'plugin2paquet_infos')
#		include_spip('inc/plugonet_generer');
	$message_infxml = '';
	if (!$infos = $informer_xml(basename($dir), true, dirname($dir) .'/'))
		$message_infxml = _T('plugonet:message_nok_information_pluginxml');
		// $message_infxml = "plugin.xml est illisible avec la fonction standard de SPIP";

	// Formatage des resultats pour affichage
	$retour = array();
	if ($message_infxml)
		$retour['message_erreur'] = $pluginxml . 
									"<br />$message_valxml" . 
									($message_infxml ? "<br />$message_infxml" : "");
	else
		$retour['message_ok']['resume'] = 
			$pluginxml . "<br />" .
			($message_infxml ? $message_infxml : _T('plugonet:message_ok_validation_pluginxml'));
	$retour['message_ok']['erreurs'] = '';
	foreach ($erreurs as $_erreur)
		$retour['message_ok']['erreurs'] .= "Ligne $_erreur[1] - " . $_erreur[0] . '<br />';
	if ($retour['message_ok']['erreurs'])
		$retour['message_ok']['erreurs'] = '<div class="notice">' . 
											"\n\t" . $retour['message_ok']['erreurs'] . 
											"\n</div>";
	$retour['editable'] = true;

	return $retour;
}

function plugin2paquet_infos($plug, $bof, $dir) 
{ 
	$f = charger_fonction('infos_plugin', 'plugins'); 
	return $f(file_get_contents("$dir$plug/plugin.xml"), $plug, $dir); 
} 
?>