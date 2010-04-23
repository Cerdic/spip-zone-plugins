<?php

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

	// Determination du type de verification et appel de la fonction idoine
	$langonet_lister_items = charger_fonction('langonet_lister_items','inc');

	// Recuperation des champs du formulaire
	//   $rep        -> nom du repertoire parent de lang/
	//                  'langonet' pour 'langonet/lang/'
	//                  correspond generalement au 'nom' du plugin
	//   $module     -> prefixe du fichier de langue
	//                  'langonet' pour 'langonet_fr.php'
	//                  parfois different du 'nom' du plugin
	//   $langue     -> index du nom de langue
	//                  'fr' pour 'langonet_fr.php'
	//   $ou_langue  -> chemin vers le fichier de langue a verifier
	//                  'plugins/auto/langonet/lang'
	//   $ou_fichier -> racine de l'arborescence a verifier
	//                  'plugins/auto/langonet'
	$retour_select_langue = explode(':', _request('fichier_langue'));
	$module = $retour_select_langue[1];
	$langue = $retour_select_langue[2];
	$ou_langue = $retour_select_langue[3];

	// Recuperation des items du fichier et formatage des resultats pour affichage
	$retour = array();
	$resultats = $langonet_lister_items($module, $langue, $ou_langue);
	if ($resultats['erreur']) {
		$retour['message_erreur'] = $resultats['erreur'];
	}
	else {
		$retour = formater_table($resultats);
	}
	$retour['editable'] = true;
	return $retour;
}

function formater_table($resultats) {

	// On initialise le tableau des textes resultant contenant les index:
	// - ["message_ok"]["resume"] : le message de retour ok fournissant le fichier des resultats
	// - ["message_ok"]["table"] : le table des items
	// - ["message_erreur"] : le message d'erreur si on a erreur de traitement pendant l'execution
	$retour = array();
	$texte = 'bonjour';

	// Tout s'est bien passe on renvoie le message ok et les resultats de la verification
	$retour['message_ok']['resume'] = _T('langonet:message_ok_table_items');
	$retour['message_ok']['table'] = $texte;

	return $retour;
}

?>