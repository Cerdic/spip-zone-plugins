<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_langonet_generer_charger() {
	$valeurs = array();
	$champs = array('fichier_langue', 'langue_cible', 'mode');
	foreach($champs as $_champ){
		$valeurs[$_champ] = _request($_champ);
	}
	return $valeurs;
}

function formulaires_langonet_generer_verifier() {
	$erreurs = array();
	if (_request('fichier_langue') == '0') {
		$erreurs['fichier_langue'] = _T('langonet:message_nok_champ_obligatoire');
	}
	if (!_request('langue_cible')) {
		$erreurs['langue_cible'] = _T('langonet:message_nok_champ_obligatoire');
	}
	return $erreurs;
}

function formulaires_langonet_generer_traiter() {
	// Recuperation des champs du formulaire :
	//   $module     	-> prefixe du fichier de langue 'langonet' pour 'langonet_fr.php'
	//                  parfois different du 'nom' du plugin
	//   $langue_source	-> index du nom de langue 'fr' pour 'langonet_fr.php'
	//   $ou_langue  	-> chemin vers le fichier de langue à vérifier 'plugins/auto/langonet/lang'
	list($plugin, $module, $langue_source, $ou_langue) = explode(':', _request('fichier_langue'));
	$langue_cible = _request('langue_cible');
	$mode = _request('mode');

	// Generation du fichier toujours en UTF-8 aujourd'hui
	$langonet_generer = charger_fonction('generer_fichier','inc');
	$resultats = $langonet_generer($module, $langue_source, $ou_langue, $langue_cible, $mode, 'utf8');
	if (isset($resultats['erreur'])) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour['message_ok']['fichier'] = $resultats['fichier'];
		$retour['message_ok']['resume'] = _T('langonet:message_ok_fichier_genere', array('langue' => $langue_cible, 'module' => $module, 'fichier' => $resultats['fichier']));
	}
	$retour['editable'] = true;

	return $retour;
}

?>