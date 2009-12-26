<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Lister les types d'entrée de menus disponibles dans les dossiers menus/
 *
 * @staticvar array $resultats
 * @param bool $informer
 * @return array
 */
function menus_lister_disponibles($informer=true){
	static $resultats = null;

	if (is_null($resultats[$informer])){
		$resultats[$informer] = array();
		// rechercher les skel du type truc.html
		$match = ".+[.]html$";

		// lister les entrées disponibles
		$liste = find_all_in_path('menus/', $match);
		if (count($liste)){
			foreach($liste as $squelette=>$chemin) {
				$type = preg_replace(',[.]html$,i', '', $squelette);
				$dossier = str_replace($squelette, '', $chemin);
				// On ne garde que les squelettes ayant un XML de config
				if (file_exists("$dossier$type.xml")
					AND (
						$entree = !$informer OR ($entree = menus_charger_infos($dossier.$type))
					)){
					$resultats[$informer][$type] = $entree;
				}
			}
		}
	}
	return $resultats[$informer];
}

/**
 * Decrire un type de menu
 *
 * @staticvar array $infos
 * @param string $type
 * @return array
 */
function menus_informer($type){
	static $infos = array();
	if (!isset($infos[$type])){
		$fichier = find_in_path("menus/$type.html");
		$infos[$type] = menus_charger_infos($fichier);
	}
	return $infos[$type];
}

/**
 * Charger les informations contenues dans le xml d'une entrée de menu
 *
 * @param string $type
 * @param string $info
 * @return array
 */
function menus_charger_infos($type, $info=""){
		// on peut appeler avec le nom du squelette
		$fichier = preg_replace(',[.]html$,i','',$type).".xml";
		include_spip('inc/xml');
		include_spip('inc/texte');
		$entree = array();
		if ($xml = spip_xml_load($fichier, false)){
			if (count($xml['entree'])){
				$xml = reset($xml['entree']);
				$entree['nom'] = typo(_T(spip_xml_aplatit($xml['nom'])));
				$entree['description'] = isset($xml['description']) ? typo(_T(spip_xml_aplatit($xml['description']))) : '';
				$entree['icone'] = isset($xml['icone']) ? find_in_path(reset($xml['icone'])) : '';
				$entree['refuser_sous_menu'] = isset($xml['refuser_sous_menu']);
				// Décomposition des paramètres
				$entree['parametres'] = array();
				if (spip_xml_match_nodes(',^parametre,', $xml, $parametres)){
					foreach (array_keys($parametres) as $parametre){
						list($balise, $attributs) = spip_xml_decompose_tag($parametre);
						$entree['parametres'][$attributs['nom']] = array(
							'label' => $attributs['label'] ? _T($attributs['label']) : $attributs['nom'],
							'obligatoire' => $attributs['obligatoire'] == 'oui' ? true : false
						);
					}
				}
				
			}
		}
		if (!$info)
			return $entree;
		else 
			return isset($entree[$info]) ? $entree[$info] : "";
}

// Suprrimer une entrée (et les éventuels sous-menus en cascade)
function menus_supprimer_entree($id_menus_entree){
	include_spip('base/abstract_sql');
	$id_menus_entree = intval($id_menus_entree);
	
	// On regarde d'abord s'il y a un sous-menu
	$id_menu = intval(sql_getfetsel(
		'id_menu',
		'spip_menus',
		'id_menus_entree = '.$id_menus_entree
	));
	
	// Dans ce cas on le supprime d'abord
	$ok = true;
	if ($id_menu)
		$ok = menus_supprimer_menu($id_menu);
	
	// Si c'est bon, on peut alors supprimer l'entrée
	if ($ok)
		$ok = sql_delete(
			'spip_menus_entrees',
			'id_menus_entree = '.$id_menus_entree
		);
	
	return $ok;
}

// Supprimer un menu (et donc toutes ses entrées aussi)
function menus_supprimer_menu($id_menu){
	include_spip('base/abstract_sql');
	$id_menu = intval($id_menu);
	
	// On récupère toutes les entrées
	$entrees = sql_allfetsel(
		'id_menus_entree',
		'spip_menus_entrees',
		'id_menu = '.$id_menu
	);
	if (is_array($entrees))
		$entrees = array_map('reset', $entrees);
	
	// On les supprime
	$ok = true;
	if (is_array($entrees))
		foreach ($entrees as $id_menus_entree){
			if ($ok)
				$ok = menus_supprimer_entree($id_menus_entree);
		}
	
	// Si tout s'est bien passé on peut enfin supprimer le menu
	if ($ok)
		$ok = sql_delete(
			'spip_menus',
			'id_menu = '.$id_menu
		);
	
	return $ok;
}

?>
