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
	
	$plugins_actifs = unserialize($GLOBALS['meta']['plugin']);

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
					    //on ne garde que les menus repondants aux necessites
				        $necessite = true;
					    if (! empty($entree['necessites']['plugin'])) {
					        /* la globale $plugins liste tout en majuscule */
					        $entree['necessites']['plugin'] = array_map("strtoupper", $entree['necessites']['plugin']);
					        foreach($entree['necessites']['plugin'] as $plugin) {
					            if ( ! array_key_exists($plugin,$plugins_actifs) )
					                $necessite = false;
					        }
					    }
					    if ($necessite)
                            $resultats[$informer][$type] = $entree;
				}
			}
		}
		$resultats[$informer] = pipeline('menus_lister_disponibles',array(
			'args' => array(),
			'data' => $resultats[$informer]
			)
		);
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
				$entree['nom'] = _T_ou_typo(spip_xml_aplatit($xml['nom']));
				$entree['rang'] = intval(trim(spip_xml_aplatit(!empty($xml['rang']) ? $xml['rang'] : 1000)));
				$entree['description'] = isset($xml['description']) ? _T_ou_typo(spip_xml_aplatit($xml['description'])) : '';
				$entree['icone'] = isset($xml['icone']) ? find_in_path(reset($xml['icone'])) : '';
				$entree['refuser_sous_menu'] = isset($xml['refuser_sous_menu']);
				// Décomposition des paramètres
				$entree['parametres'] = array();
				if (spip_xml_match_nodes(',^parametre,', $xml, $parametres)){
					foreach (array_keys($parametres) as $parametre){
						list($balise, $attributs) = spip_xml_decompose_tag($parametre);
						$entree['parametres'][$attributs['nom']] = array(
							'label' => (isset($attributs['label']) && $attributs['label']) ? _T($attributs['label']) : $attributs['nom'],
							'obligatoire' => (isset($attributs['obligatoire']) && $attributs['obligatoire'] == 'oui') ? true : false,
							'class' => (isset($attributs['class']) && $attributs['class']) ? $attributs['class'] : ''
						);
					}
				}
				//Décomposition des necessites
				if (spip_xml_match_nodes(',^necessite,', $xml, $necessites)){
			        $entree['necessites']['plugin'] = array();
					foreach (array_keys($necessites) as $necessite){
						list($balise, $attributs) = spip_xml_decompose_tag($necessite);
						array_push($entree['necessites'][$attributs['type']] , $attributs['nom']);
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
