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
		spip_log("Ajout manuel : erreur lors de l'insertion de la boussole $boussole", _BOUSSOLE_LOG . _LOG_ERREUR);
	}
	else {
		$retour['message_ok'] = $message;
		spip_log("Ajout manuel ok de la boussole $boussole", _BOUSSOLE_LOG . _LOG_INFO);
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
	$convertir = charger_fonction('decoder_xml', 'inc');

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

		if (isset($tableau[_BOUSSOLE_NOMTAG_LISTE_BOUSSOLES][_BOUSSOLE_NOMTAG_BOUSSOLE])) {
			$boussoles = array();
			if (isset($tableau[_BOUSSOLE_NOMTAG_LISTE_BOUSSOLES][_BOUSSOLE_NOMTAG_BOUSSOLE][0]))
				$boussoles = $tableau[_BOUSSOLE_NOMTAG_LISTE_BOUSSOLES][_BOUSSOLE_NOMTAG_BOUSSOLE];
			else
				$boussoles[0] = $tableau[_BOUSSOLE_NOMTAG_LISTE_BOUSSOLES][_BOUSSOLE_NOMTAG_BOUSSOLE];

			foreach ($boussoles as $_boussole) {
				$infos = array('serveur' => $_serveur);
				$infos['alias'] = $_boussole['@attributes']['alias'];
				if (isset($_boussole['nom']))
					$infos['nom'] = '<multi>' . trim($_boussole['nom']['multi']) . '</multi>';
				if ($infos['alias'] == 'spip')
					$liste[0] = $infos;
				else {
					$liste[$index] = $infos;
					$index += 1;
				}
			}
		}
		else if (isset($tableau[_BOUSSOLE_NOMTAG_ERREUR])) {
			$message .= _T("boussole:message_nok_{$tableau[_BOUSSOLE_NOMTAG_ERREUR]['@attributes']['id']}", array('serveur' => $_serveur)) . ' ';
		}
		else {
			$message .= _T('boussole:message_nok_reponse_invalide', array('serveur' => $_serveur)) . ' ';
		}
	}

	return array($liste, $message);
}

?>
