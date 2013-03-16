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

	// Recuperation des champs du formulaire
	//   $module     -> prefixe du fichier de langue
	//                  'langonet' pour 'langonet_fr.php'
	//                  parfois different du 'nom' du plugin
	//   $langue     -> index du nom de langue
	//                  'fr' pour 'langonet_fr.php'
	//   $ou_langue  -> chemin vers le fichier de langue a verifier
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
		$retour = formater_table($resultats);
	}
	$retour['editable'] = true;
	return $retour;
}

function formater_table($resultats) {

	include_spip('inc/layer');
	// On initialise le tableau des textes resultant contenant les index:
	// - ["message_ok"]["resume"] : le message de retour ok fournissant le fichier des resultats
	// - ["message_ok"]["table"] : le table des items
	// - ["message_erreur"] : le message d'erreur si on a erreur de traitement pendant l'execution
	$retour = array();
	
	// Creation de la liste:
	// - un bloc dépliable par lettre initiale
	// - le bloc est un tableau item/traduction
	$texte = '';
	foreach ($resultats['table'] as $_initiale => $_table) {
		// On demarre un nouveau bloc depliable et une nouvelle table
		$i = 0;
		$texte .= bouton_block_depliable(strtoupper($_initiale) . ' (' . count($_table) . ')', true) .
		          debut_block_depliable(true) .
		          '<div class="cadre_padding">' . "\n" .
		          '<table style="width:100%;" class="spip">' . "\n" .
		          '<tbody>' . "\n";
		// On ajoute une ligne par item
		foreach ($_table as $_item => $_traduction) {
			$texte.= '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">' . "\n" .
			         '<td style="border:medium none;"><strong>' . $_item . '</strong></td>' . "\n" .
			         '<td style="border:medium none;">' . $_traduction . '</td>' . "\n" .
			         '</tr>' . "\n";
			$i += 1;
		}
		// On ferme la table et le bloc courant
		$texte .= '</tbody>' . "\n" . '</table>' . "\n" . '</div>' . "\n" .
		          fin_block();
	}

	// Tout s'est bien passe on renvoie le message ok et les resultats de la verification
	$retour['message_ok']['resume'] = _T('langonet:message_ok_table_creee', array('langue' => $resultats['langue']));
	$retour['message_ok']['explication'] =  _T('langonet:info_table', array('total' => $resultats['total'], 'langue' => $resultats['langue']));
	$retour['message_ok']['table'] = $texte;

	return $retour;
}

?>