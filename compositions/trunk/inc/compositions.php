<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('compositions_fonctions');

/**
 * Separer le type et le nom de la composition dans un nom de fichier
 *
 * @param string $nom
 * @return array
 */
function compositions_decomposer_nom($nom){
	$reg = ",^([a-z][^-.]*)("._COMPOSITIONS_MATCH.")?$,i";
	if (
		// recuperer le type et la composition
	  preg_match($reg,$nom,$matches)
	  // il y a bien un type
	  AND $type=$matches[1]
	){
		$composition = isset($matches[3])?$matches[3]:'';
		return array($type,$composition);
	}
	return array($nom,"");
}

/**
 * Charger les informations contenues dans le xml d'une composition
 *
 * @param string $nom
 * @param string $info
 * @return array|string
 */
function compositions_charger_infos($nom,$info=""){
		// on peut appeller avec le nom du squelette
		$nom = preg_replace(',[.]html$,i','',$nom).".xml";
		include_spip('inc/xml');
		$composition = array();
		if ($xml = spip_xml_load($nom,false)){
			if (count($xml['composition'])){
				$xml = reset($xml['composition']);
				$composition['nom'] = _T_ou_typo(spip_xml_aplatit($xml['nom']));
				$composition['description'] = isset($xml['description'])?_T_ou_typo(spip_xml_aplatit($xml['description'])):'';
				if (isset($xml['icon'])) {
					$icon = chemin_image(reset($xml['icon']));
					if (!$icon)
						$icon = find_in_path(reset($xml['icon']));
				} else
					$icon = '';
				$composition['icon'] = $icon;
				$composition['class'] = isset($xml['class'])?trim(reset($xml['class'])):'';
				$composition['configuration'] = isset($xml['configuration'])?spip_xml_aplatit($xml['configuration']):'';
				$composition['branche'] = array();
				if (spip_xml_match_nodes(',^branche,', $xml, $branches)){
					foreach (array_keys($branches) as $branche){
						list($balise, $attributs) = spip_xml_decompose_tag($branche);
						$composition['branche'][$attributs['type']] = $attributs['composition'];
					}
				}
			}
		}
		if (!$info)
			return $composition;
		else 
			return isset($composition[$info])?$composition[$info]:"";
}


/**
 * Ecrire dans une meta la liste des objets qui sont sous le regime des
 * compositions
 * La fonction est appelee
 * - lors de la stylisation si la meta n'est pas encore definie
 * - a chaque fois qu'on selectionne un composition dans l'espace prive
 * - si var_mode=recalcul
 * On est sur ainsi que toute nouvelle composition selectionnee est dedans
 * Si une composition est retiree du file system sans etre deselectionnee
 * l'erreur sera evitee par la verification d'existence au moment de styliser
 *
 * @param array $liste
 */
function compositions_cacher($liste=null){
	// lister les compositions vraiment utilisees
	if (!is_array($liste)){
		include_spip('compositions_fonctions');
		$liste = compositions_lister_disponibles('',false);
	}
	// lister les objets dont on a active la composition dans la configuration
	$config = compositions_objets_actives();

	$liste = array_intersect($config,array_keys($liste));
	ecrire_meta('compositions_types',implode(',',$liste));
	spip_log('compositions: maj des compositions_types ['.$GLOBALS['meta']['compositions_types'].']');
}

?>