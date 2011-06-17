<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// ----------------------- Traitements des boussoles ---------------------------------

/**
 * Ajout de la boussole dans la base de donnees
 *
 * @param string $url
 * @param string &$erreur
 * @return boolean
 */

// $url	=> url ou path du fichier xml de description de la boussole
// $erreur	=> message d'erreur deja traduit
function boussole_ajouter($url) {

	// On initialise le message de sortie
	$message = '';
	
	// On recupere les infos du fichier xml de description de la balise
	$infos = boussole_parser_xml($url);
	if (!infos OR !$infos['boussole']['alias']){
		$message = _T('boussole:message_nok_xml_invalide', array('fichier' => $url));
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
	if (!$ids = sql_insertq_multi('spip_boussoles', $infos['sites'])) {
		$message = _T('boussole:message_nok_ecriture_bdd');
		return array(false, $message);
	}
	// -- consignation des informations de mise a jour de cette boussole dans la table spip_meta
	$infos['boussole']['nbr_sites'] = count($infos['sites']);
	$infos['boussole']['xml'] = $url;
	ecrire_meta($meta_boussole, serialize($infos['boussole']));

	// On definit le message de retour ok (actualisation ou ajout)
	if ($actualisation)
		$message = _T('boussole:message_ok_boussole_actualisee', array('fichier' => $url));
	else
		$message = _T('boussole:message_ok_boussole_ajoutee', array('fichier' => $url));
	
	return array(true, $message);
}


/**
 * Suppression de la boussole dans la base de donnees
 *
 * @param int $aka_boussole
 * @return boolean
 */

// $aka_boussole	=> alias de la boussole, par defaut, spip
function boussole_supprimer($aka_boussole) {
	
	// Alias non conforme
	if (!$aka_boussole)
		return false;

	// On supprime les sites de cette boussole
	sql_delete('spip_boussoles','aka_boussole='.sql_quote($aka_boussole));
	// On supprime ensuite la meta consignant la derniere mise a jour de cette boussole
	effacer_meta('boussole_infos_' . $aka_boussole);
	return true;
}


// ----------------------- Traitements des fichiers xml ---------------------------------

/**
 * Teste l'existence d'un xml de boussole et renvoie le path complet ou l'url absolue
 *
 * @param string $xml
 * @return string
 */
function boussole_localiser_xml($xml) {

	include_spip('inc/distant');
	include_spip('inc/filtres'); // url_absolue
	$retour = '';

	// On calcul une url absolue dans tous les cas
	if (preg_match(",^(http|ftp)://,",$xml))
		// Mode standard ou mode perso : on a passe une url
		$url = url_absolue($xml);
	else
		// Mode perso : on a passe un fichier seul, 
		// on calcule l'url sachant que le fichier doit etre dans a la racine
		$url = url_absolue(find_in_path($xml));

	// On verifie que le fichier existe
	if (recuperer_page($url, false, false))
		$retour = $url;

	return $retour;
}


/**
 * Teste la validite du fichier xml de la boussole en fonction de la DTD boussole.dtd
 *
 * @param string $url
 * @param array &$erreur
 * @return boolean
 */

// $url	=> url absolue du fichier xml de description de la boussole
// $erreur	=> tableau des erreurs collectees suite a la validation xml
function boussole_valider_xml($url, &$erreur) {

	include_spip('inc/distant');
	include_spip('inc/plugin');
	$ok = true;

	// On verifie la validite du contenu en fonction de la dtd
	$valider_xml = charger_fonction('valider', 'xml');
	// -- Compatibilite SPIP 2.0 : pas possible de verifer, on renvoie systematiquement true
	if (spip_version_compare($GLOBALS['spip_version_branche'], '2.1', '<'))
		return $ok;
	// -- En SPIP >= 2.1 on peut effectuer la validation
	$retour = $valider_xml(recuperer_page($url));
	$erreurs = is_array($retour) ? $retour[1] : $retour->err;
	if ($erreurs === false) {
		$ok = false;
	}
	else if ($erreurs) {
		$erreur['detail'] = $erreurs;
		$ok = false;
	}

	return $ok;
}


/**
 * Renvoie, a partir du fichier xml de la boussole, un tableau des sites de la boussole
 * Les cles du tableau correspondent au nom des champs en base de donnees
 *
 * @param string $url
 * @return array()
 */

// $url	=> url ou path du fichier xml de description de la boussole
function boussole_parser_xml($url) {

	$infos = array();

	// Lire les donnees du fichier xml d'une boussole
	include_spip('inc/xml');
	$xml = spip_xml_load($url);
	
	// On recupere les infos de la balise boussole
	if (spip_xml_match_nodes(',^boussole,', $xml, $matches)){
		$tag = array_keys($matches);
		list($balise, $attributs) = spip_xml_decompose_tag($tag[0]);
		$infos[$balise] = $attributs;
	
		// On recupere les infos des balises groupe et site
		if (spip_xml_match_nodes(',^groupe,', $xml, $groupes)){
			$infos['sites'] = array();
			$rang_groupe = 0;
			foreach (array_keys($groupes) as $_groupe){
				$site = array();
				// On consigne l'alias et le rang du groupe
				list($balise_groupe, $attributs_groupe) = spip_xml_decompose_tag($_groupe);
				$rang_groupe = ++$i;
				// On consigne l'alias et l'url de chaque site du groupe en cours de traitement
				$rang_site = 0;
				foreach (array_keys($groupes[$_groupe][0]) as $_site){
					// Alias de la boussole
					$site['aka_boussole'] = $infos['boussole']['alias'];
					// Infos du groupe
					$site['aka_groupe'] = $attributs_groupe['type'];
					$site['rang_groupe'] = $rang_groupe;
					// Infos du site
					list($balise_site, $attributs_site) = spip_xml_decompose_tag($_site);
					$site['aka_site'] = $attributs_site['alias'];
					$site['url_site'] = $attributs_site['src'];
					$site['rang_site'] = ++$rang_site;
					$site['affiche'] = 'oui';
					$site['id_syndic'] = 0;
					// On ajoute le site ainsi defini aux tableau des sites si celui-ci est actif
					if ($attributs_site['actif'] == 'oui')
						$infos['sites'][] = $site;
				}
			}
		}
	}
	
	return $infos;
}

?>
