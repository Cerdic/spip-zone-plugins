<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/client');
include_spip('inc/actions');

function formulaires_editer_boussole_charger($aka_boussole){
	$valeurs = array('alias' => $aka_boussole);
	return $valeurs;
}

function formulaires_editer_boussole_verifier($aka_boussole){
	$erreurs = array();
	return $erreurs;
}

function formulaires_editer_boussole_traiter($aka_boussole){
	$retours = array();

	// Si on demande le changement d'etat d'affichage d'un site -------------
	if ($params = _request('afficher')) {
		// On recupere l'id du site et l'etat demande
		preg_match('/^([\d]+)-(oui|non)$/', $params, $matches);
		$id_site = intval($matches[1]);
		$affiche = $matches[2];
		$ok = sql_updateq('spip_boussoles',	array('affiche' => $affiche), 'id_site='. sql_quote($id_site));
		if (!$ok)
			$retours['message_erreur'] = _T('boussole:message_nok_ecriture_bdd');
	}

	// Si on demande a deplacer un groupe -----------------------------------
	if ($params = _request('deplacer_groupe')) {
		// On recupere les parametres alias du groupe et sens de deplacement
		preg_match('/^([\w]+)-(bas|haut)$/', $params, $matches);
		$aka_groupe = $matches[1];
		$sens = $matches[2];

		// On recupere des infos sur le placement du groupe concerne et tous les sites qui le compose
		$sites = sql_allfetsel('id_site, rang_groupe', 'spip_boussoles', 
								array('aka_boussole=' . sql_quote($aka_boussole),
									'aka_groupe=' . sql_quote($aka_groupe)));
		$rang_source = intval($sites[0]['rang_groupe']);
		$id_sites_source = array_map('reset', $sites);
		$id_sites_source = array_map('intval', $id_sites_source);
		// On calcule le rang de destination du groupe concerne
		$rang_destination = ($sens == 'bas') ? ($rang_source + 1) : ($rang_source - 1);

		// On sait que le deplacement est toujours possible donc on l'opere sans se poser de question
		// -- On positionne d'abord le groupe pour l'instant positionne au rang de destination
		$sites = sql_allfetsel('id_site', 'spip_boussoles', 
								array('aka_boussole=' . sql_quote($aka_boussole),
									'rang_groupe=' . sql_quote($rang_destination)));
		$id_sites_destination = array_map('reset', $sites);
		$id_sites_destination = array_map('intval', $id_sites_destination);
		$ok = true;
		foreach($id_sites_destination as $_id_site) {
			if ($ok)
				$ok = sql_updateq('spip_boussoles',	array('rang_groupe' => $rang_source), 
								'id_site='. sql_quote($_id_site));
		}

		// -- On positionne maintenant le groupe choisi au rang de destination
		if ($ok) {
			foreach($id_sites_source as $_id_site) {
				if ($ok)
					$ok = sql_updateq('spip_boussoles',	array('rang_groupe' => $rang_destination), 
									'id_site='. sql_quote($_id_site));
			}
		}

		if (!$ok) 
			$retours['message_erreur'] = _T('boussole:message_nok_ecriture_bdd');
	}

	// Si on demande a deplacer un site -------------------------------------
	if ($params = _request('deplacer_site')) {
		// On recupere les parametres id du site et sens de deplacement
		preg_match('/^([\d]+)-(bas|haut)$/', $params, $matches);
		$id_site = intval($matches[1]);
		$sens = $matches[2];

		// On recupere des infos sur le placement actuel et le groupe d'appartenance du site
		$site = sql_fetsel('aka_groupe, rang_site', 'spip_boussoles', 'id_site='. sql_quote($id_site));
		$aka_groupe = $site['aka_groupe'];
		$rang_source = intval($site['rang_site']);

		// On calcule le rang de destination du site concerne
		$rang_destination = ($sens == 'bas') ? ($rang_source + 1) : ($rang_source - 1);

		// On sait que le deplacement est toujours possible donc on l'opere sans se poser de question
		// -- On positionne d'abord le site pour l'instant positionne au rang de destination
		$ok = sql_updateq('spip_boussoles',	array('rang_site' => $rang_source),
							array('aka_boussole=' . sql_quote($aka_boussole),
								'aka_groupe=' . sql_quote($aka_groupe),
								'rang_site=' . sql_quote($rang_destination)));

		// -- On positionne maintenant le site choisi au rang de destination
		if ($ok)
			$ok = sql_updateq('spip_boussoles',	array('rang_site' => $rang_destination),
								'id_site='. sql_quote($id_site));

		if (!$ok) 
			$retours['message_erreur'] = _T('boussole:message_nok_ecriture_bdd');
	}

	$retours['editable'] = true;

	return $retours;
}

?>