<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Ajout de la boussole dans la base de donnees
 *
 * @api
 *
 * @param string $boussole	Alias de la boussole
 * @param string $serveur	Alias du serveur fournissant les données sur la boussole
 * @return array
 */
function boussole_ajouter($boussole, $serveur='spip') {

	// On initialise le message de sortie
	$message = '';

	// Vérification de l'existence de la table spip_boussoles_extras pour éviter une erreur liée à l'activation
	// du CRON d'actualisation avant que la migration de schéma ait été effectuée
	$trouver = charger_fonction('trouver_table', 'base');
	if (!$trouver('spip_boussoles_extras')) {
		$message = '';
		return array(false, $message);
	};

	// On recupere les infos du fichier xml de description de la boussole
	$infos = phraser_xml_boussole($boussole, $serveur);
	if (!$infos OR !$infos['boussole']['alias']){
		$message = _T('boussole:message_nok_xml_invalide', array('fichier' => $boussole));
		return array(false, $message);
	}

	// On complete les infos de chaque site 
	// - par l'id_syndic si ce site est deja reference dans la table spip_syndic. 
	//   On reconnait le site par son url
	// - par la configuration de l'affichage si la boussole existe deja
	foreach ($infos['sites'] as $_cle => $_info) {
		// -- On recherche l'id_syndic en construisant deux urls possibles : l'une avec / l'autre sans
		$urls = array();
		$urls[] = $_info['url_site'];
		$urls[] = (substr($_info['url_site'], -1, 1) == '/') ? substr($_info['url_site'], 0, -1) : $_info['url_site'] . '/';
		if ($id_syndic = sql_getfetsel('id_syndic', 'spip_syndic', sql_in('url_site', $urls)))
			$infos['sites'][$_cle]['id_syndic'] = intval($id_syndic);
		// -- On recherche une configuration d'affichage (si elle existe)
		$where = array('aka_boussole=' .sql_quote($infos['boussole']['alias']),
					'aka_site=' . sql_quote($_info['aka_site']));
		if ($resultats = sql_fetsel('rang_groupe, rang_site, affiche', 'spip_boussoles', $where)) {
			$infos['sites'][$_cle]['rang_groupe'] = intval($resultats['rang_groupe']);
			$infos['sites'][$_cle]['rang_site'] = intval($resultats['rang_site']);
			$infos['sites'][$_cle]['affiche'] = $resultats['affiche'];
		}
	}
	
	// On insere le tableau des sites collecte dans la table spip_boussoles
	$meta_boussole = 'boussole_infos_' . $infos['boussole']['alias'];
	// -- suppression au prealable des sites appartenant a la meme boussole si elle existe
	//    et determination du type d'action (ajout ou actualisation)
	$actualisation = false;
	if (lire_meta($meta_boussole)) {
		$actualisation = true;
		boussole_supprimer($infos['boussole']['alias']);
	}
	// -- insertion de la nouvelle liste de sites pour cette boussole
	if (!sql_insertq_multi('spip_boussoles', $infos['sites'])) {
		$message = _T('boussole:message_nok_ecriture_bdd', array('table' => 'spip_boussoles'));
		return array(false, $message);
	}
	// -- insertion de la nouvelle liste des extras pour cette boussole
	if (sql_insertq_multi('spip_boussoles_extras', $infos['extras']) === false) {
		$message = _T('boussole:message_nok_ecriture_bdd', array('table' => 'spip_boussoles_extras'));
		return array(false, $message);
	}
	// -- consignation des informations de mise a jour de cette boussole dans la table spip_meta
	$infos['boussole']['nbr_sites'] = count($infos['sites']);
	$infos['boussole']['maj'] = date('Y-m-d H:i:s');
	ecrire_meta($meta_boussole, serialize($infos['boussole']));

	// On definit le message de retour ok (actualisation ou ajout)
	if ($actualisation)
		$message = _T('boussole:message_ok_boussole_actualisee', array('fichier' => $boussole));
	else
		$message = _T('boussole:message_ok_boussole_ajoutee', array('fichier' => $boussole));
	
	return array(true, $message);
}


/**
 * Suppression de la boussole dans la base de donnees
 *
 * @api
 *
 * @param string $aka_boussole	alias de la boussole
 * @return boolean
 */
function boussole_supprimer($aka_boussole) {
	
	// Alias non conforme
	if (!$aka_boussole)
		return false;

	// On supprime les sites de cette boussole
	sql_delete('spip_boussoles','aka_boussole='.sql_quote($aka_boussole));
	// On supprime les extras de cette boussole
	$trouver = charger_fonction('trouver_table', 'base');
	if ($trouver('spip_boussoles_extras'))
		sql_delete('spip_boussoles_extras','aka_boussole='.sql_quote($aka_boussole));
	// On supprime ensuite la meta consignant la derniere mise a jour de cette boussole
	effacer_meta('boussole_infos_' . $aka_boussole);

	return true;
}

/**
 * Renvoie, a partir du fichier xml de la boussole, un tableau des sites de la boussole
 * Les cles du tableau correspondent au nom des champs en base de donnees
 *
 * @param string $boussole	Alias de la boussole
 * @param string $serveur	Alias du serveur fournissant les données sur la boussole
 * @return array()
 */
function phraser_xml_boussole($boussole, $serveur='spip') {

	$infos = array();

	// Acquérir les informations de la boussole à partir du serveur
	include_spip('inc/distant');
	$action = str_replace(
				array('[action]','[arguments]'),
				array('serveur_informer_boussole', "&arg=${boussole}"),
				$GLOBALS['client_serveurs_disponibles'][$serveur]['api']);
	$page = recuperer_page($action);

	$convertir = charger_fonction('simplexml_to_array', 'inc');
	$tableau = $convertir(simplexml_load_string($page), false);

	if (isset($tableau['name'])
	AND ($tableau['name'] == 'boussole')) {
		$infos['sites'] = array();
		$infos['extras'] = array();

		// Collecter les attributs pour la meta de la boussole
		$infos['boussole'] = $tableau['attributes'];

		// Construire l'objet extras de la boussole
		$extra['aka_boussole'] = $infos['boussole']['alias'];
		$extra['type_objet'] = 'boussole';
		$extra['aka_objet'] = $infos['boussole']['alias'];
		$extra['logo_objet'] = $infos['boussole']['logo'];
		if (isset($tableau['children']['nom']))
			$extra['nom_objet'] = '<multi>' . $tableau['children']['nom'][0]['children']['multi'][0]['text'] . '</multi>';
		if (isset($tableau['children']['slogan']))
			$extra['slogan_objet'] = '<multi>' . $tableau['children']['slogan'][0]['children']['multi'][0]['text'] . '</multi>';
		if (isset($tableau['children']['description']))
			$extra['descriptif_objet'] = '<multi>' . $tableau['children']['description'][0]['children']['multi'][0]['text'] . '</multi>';
		$infos['extras'][] = $extra;

		// Collecter les informations des groupes
		if (isset($tableau['children']['groupe'])) {
			$rang_groupe = 0;
			foreach ($tableau['children']['groupe'] as $_groupe) {
				// Construire l'objet extras du groupe
				$extra['aka_boussole'] = $infos['boussole']['alias'];
				$extra['type_objet'] = 'groupe';
				$extra['aka_objet'] = $_groupe['attributes']['type'];
				$extra['logo_objet'] = '';
				if (isset($_groupe['children']['nom']))
					$extra['nom_objet'] = '<multi>' . $_groupe['children']['nom'][0]['children']['multi'][0]['text'] . '</multi>';
				$extra['slogan_objet'] = '';
				$extra['descriptif_objet'] = '';
				$infos['extras'][] = $extra;

				// On consigne l'alias et le rang du groupe
				$rang_groupe = ++$i;
				// On consigne l'alias et l'url de chaque site du groupe en cours de traitement
				$rang_site = 0;
				if (isset($_groupe['children']['site'])) {
					foreach ($_groupe['children']['site'] as $_site){
						if ($_site['attributes']['actif'] == 'oui') {
							// Alias de la boussole
							$site['aka_boussole'] = $infos['boussole']['alias'];
							// Infos du groupe
							$site['aka_groupe'] = $_groupe['attributes']['type'];
							$site['rang_groupe'] = $rang_groupe;
							// Infos du site
							$site['aka_site'] = $_site['attributes']['alias'];
							$site['url_site'] = $_site['attributes']['src'];
							$site['rang_site'] = ++$rang_site;
							$site['affiche'] = 'oui';
							$site['id_syndic'] = 0;
							$infos['sites'][] = $site;

							// Construire l'objet extra du site
							$extra['aka_boussole'] = $infos['boussole']['alias'];
							$extra['type_objet'] = 'site';
							$extra['aka_objet'] = $_site['attributes']['alias'];
							$extra['logo_objet'] = $_site['attributes']['logo'];
							if (isset($_site['children']['nom']))
								$extra['nom_objet'] = '<multi>' . $_site['children']['nom'][0]['children']['multi'][0]['text'] . '</multi>';
							if (isset($_site['children']['slogan']))
								$extra['slogan_objet'] = '<multi>' . $_site['children']['slogan'][0]['children']['multi'][0]['text'] . '</multi>';
							if (isset($_site['children']['description']))
								$extra['descriptif_objet'] = '<multi>' . $_site['children']['description'][0]['children']['multi'][0]['text'] . '</multi>';
							$infos['extras'][] = $extra;
						}
					}
				}
			}
		}
	}
	
	return $infos;
}

?>
