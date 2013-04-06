<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_langonet_lister_charger() {
	return array('fichier_langue' => _request('fichier_langue'));
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
		$retour['message_ok']['explication'] =  _T('langonet:info_table');
		$retour['message_ok']['titre'] =  basename($resultats['langue'], '.php') . ' (' . $resultats['total'] . ')';
		$retour['message_ok']['table'] = $resultats['table'];
	}
	$retour['editable'] = true;
	return $retour;
}

?>