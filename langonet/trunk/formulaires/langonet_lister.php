<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_langonet_lister_charger() {
	$valeurs = array();
	$champs = array('fichier_langue', 'affichage');
	foreach($champs as $_champ){
		$valeurs[$_champ] = _request($_champ);
	}
	return $valeurs;
}

function formulaires_langonet_lister_verifier() {
	$erreurs = array();
	if (_request('fichier_langue') == '0') {
		$erreurs['fichier_langue'] = _T('langonet:message_nok_champ_obligatoire');
	}
	return $erreurs;
}

function formulaires_langonet_lister_traiter() {

	// Recuperation des champs du formulaire
	//   $module     -> prefixe du fichier de langue
	//                  'langonet' pour 'langonet_fr.php'
	//                  parfois different du 'nom' du plugin
	//   $langue     -> index du nom de langue
	//                  'fr' pour 'langonet_fr.php'
	//   $ou_langue  -> chemin vers le fichier de langue à vérifier
	//                  'plugins/auto/langonet/lang'
	$retour_select_langue = explode(':', _request('fichier_langue'));
	$module = $retour_select_langue[1];
	$langue = $retour_select_langue[2];
	$ou_langue = $retour_select_langue[3];

	// Chargement de la fonction d'affichage
	$langonet_lister_items = charger_fonction('langonet_lister_items','inc');

	// Recuperation des items du fichier et formatage des resultats pour affichage
	$retour = array();
	$resultats = $langonet_lister_items($module, $langue, $ou_langue);
	if ($resultats['erreur']) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour['message_ok']['resume'] = _T('langonet:message_ok_table_creee', array('langue' => $resultats['langue']));
		$retour['message_ok']['titre'] =  basename($resultats['langue'], '.php') . ' (' . $resultats['total'] . ')';
		$retour['message_ok']['items'] = $resultats['items'];
		$retour['message_ok']['tradlang'] = $resultats['tradlang'];
		$retour['message_ok']['reference'] = $resultats['reference'];
		$retour['message_ok']['affichage'] = _request('affichage');
	}
	$retour['editable'] = true;
	return $retour;
}

?>