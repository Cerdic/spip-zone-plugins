<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_ajouter_boussole_charger_dist() {

	list($boussoles, $message) = charger_boussoles();
	return array(
			'boussole' => _request('boussole'),
			'_boussoles' => $boussoles,
			'message_erreur' => $message,
			'editable' => (count($boussoles)==0 ? false : true));
}


function formulaires_ajouter_boussole_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}


function formulaires_ajouter_boussole_traiter_dist(){
	$retour = array();

	$choix = _request('boussole');
	list($boussole, $serveur) = explode(':', $choix);

	// On insere la boussole dans la base
	include_spip('inc/client');
	list($ok, $message) = boussole_ajouter($boussole, $serveur);
		
	// Determination des messages de retour
	if (!$ok) {
		$retour['message_erreur'] = $message;
		spip_log("Ajout manuel : erreur lors de l'insertion de la boussole $boussole", 'boussole' . _LOG_ERREUR);
	}
	else {
		$retour['message_ok'] = $message;
		spip_log("Ajout manuel ok de la boussole $boussole", 'boussole' . _LOG_INFO);
	}
	$retour['editable'] = true;

	return $retour;
}

/**
 * Chargement des boussoles pouvant être ajoutées sur le client à partir de la liste des serveur configurés.
 * On distingue la boussole spip des autres boussoles en la placant en premier dans le tableau retourné.
 *
 * @return array
 */
function charger_boussoles() {

	include_spip('inc/distant');

	// Détermination des serveurs configurés
	include_spip('inc/config');
	$serveurs = lire_config('boussole/client/serveurs_disponibles');

	// Chargement de la fonction de convzersion xml en tableau
	$convertir = charger_fonction('xml_decode', 'inc');

	// On boucle sur tous les serveurs configurés pour le site client
	// -- pour chacun on acquiert la liste des boussoles disponibles
	$liste = array();
	$message = '';
	$index = 1;
	foreach($serveurs as $_serveur => $_infos) {
		$action = rtrim($_infos['url'], '/')
				. "/spip.php?action=serveur_lister_boussoles";
		$page = recuperer_page($action);

		$tableau = $convertir($page);

		if (isset($tableau['boussoles']['boussole'])) {
			if (isset($tableau['boussoles']['boussole'][0]))
				$boussole = $tableau['boussoles']['boussole'];
			else
				$boussole[0] = $tableau['boussoles']['boussole'];

			foreach ($boussole as $_boussole) {
				$infos_boussole = array('serveur' => $_serveur);
				$infos_boussole['alias'] = $_boussole['@attributes']['alias'];
				if (isset($_boussole['nom']))
					$infos_boussole['nom'] = '<multi>' . trim($_boussole['nom']['multi']) . '</multi>';
				if ($infos_boussole['alias'] == 'spip')
					$liste[0] = $infos_boussole;
				else {
					$liste[$index] = $infos_boussole;
					$index += 1;
				}
			}
		}
		else if (isset($tableau['erreur'])) {
			$message .= _T("boussole:message_nok_{$tableau['erreur']['@attributes']['id']}", array('serveur' => $_serveur)) . ' ';
		}
		else {
			$message .= _T('boussole:message_nok_reponse_invalide', array('serveur' => $_serveur)) . ' ';
		}
	}

	return array($liste, $message);
}

?>
