<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


// ----------------------- Traitements des boussoles ---------------------------------

/**
 * Teste la validite d'une url d'une description de boussole
 *
 * @param string $url
 * @return boolean
 */

// $url	=> url du fichier xml de description de la boussole (choix perso)
function boussole_verifier_adresse($url){
	include_spip('inc/distant');
	return (!$xml = recuperer_page($url)) ? false : true;
}


/**
 * Teste la validite d'un xml de boussole d'apres la DTD
 *
 * @param string $xml
 * @return boolean
 */

// $xml	=> url du fichier xml de description de la boussole
function boussole_verifier_xml($xml){

	// A IMPLEMENTER
	return true;
}


/**
 * Ajout du depot et de ses extensions dans la base de donnees
 *
 * @param string $url
 * @param string &$erreur
 * @return boolean
 */

// $url	=> url ou path du fichier xml de description de la boussole
// $erreur	=> message d'erreur deja traduit
function boussole_ajouter($url, &$erreur=''){

	// On recupere les infos du fichier xml de description de la balise
	if (!$infos = boussole_parser_xml($url)){
		$erreur = _T('boussole:message_nok_xml_invalide', array('fichier' => $url));
		return false;
	}
	
	// On insere le tableau des sites collecte dans la table spip_boussoles
	$meta_boussole = 'boussole_infos_' . $infos['boussole']['alias'];
	// -- suppression au prealable des sites appartenant a la meme boussole si elle existe
	if (lire_meta($meta_boussole))
		boussole_supprimer($infos['boussole']['alias']);
	// -- insertion de la nouvelle liste de sites pour cette boussole
	if (!$ids = sql_insertq_multi('spip_boussoles', $infos['sites'])) {
		$erreur = _T('boussole:message_nok_ecriture_bdd', array('fichier' => $url));
		return false;
	}
	// -- consignation des informations de mise a jour de cette boussole dans la table spip_meta
	$infos['boussole']['nbr_sites'] = count($infos['sites']);
	$infos['boussole']['xml'] = $url;
	ecrire_meta($meta_boussole, serialize($infos['boussole']));
	
	return true;
}


/**
 * Suppression du depot et de ses extensions dans la base de donnees
 *
 * @param int $aka_boussole
 * @return boolean
 */

// $aka_boussole	=> alias de la boussole, par defaut, spip
function boussole_supprimer($aka_boussole){
	
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
 * Renvoie, a partir du fichier xml de la boussole, un tableau des sites de la boussole
 * Les cles du tableau correspondent au nom des champs en base de donnees
 *
 * @param string $url
 * @return array()
 */

// $url	=> url ou path du fichier xml de description de la boussole
function boussole_parser_xml($url){

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
					// On ajoute le site ainsi defini aux tableau des sites
					$infos['sites'][] = $site;
				}
			}
		}
	}
	
	return $infos;
}

?>
