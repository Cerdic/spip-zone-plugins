<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

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
 * @return array/string
 */
function compositions_charger_infos($nom,$info=""){
		// on peut appeller avec le nom du squelette
		$nom = preg_replace(',[.]html$,i','',$nom).".xml";
		include_spip('inc/xml');
		$composition = array();
		if ($xml = spip_xml_load($nom,false)){
			if (count($xml['composition'])){
				$xml = reset($xml['composition']);
				$composition['nom'] = spip_xml_aplatit($xml['nom']);
				$composition['description'] = isset($xml['description'])?spip_xml_aplatit($xml['description']):'';
				$composition['icon'] = isset($xml['icon'])?find_in_path(reset($xml['icon'])):'';
				$composition['configuration'] = isset($xml['configuration'])?spip_xml_aplatit($xml['configuration']):'';
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
 * @param <type> $liste
 */
function compositions_cacher($liste=null){
	if (!is_array($liste)){
		include_spip('compositions_fonctions');
		$liste = compositions_lister_disponibles('',false);
	}
	include_spip('inc/metas');
	ecrire_meta('compositions_types',implode(',',array_keys($liste)));
	spip_log('compositions: maj des compositions_types ['.$GLOBALS['meta']['compositions_types'].']');
}

?>