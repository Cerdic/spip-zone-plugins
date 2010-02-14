<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Lister les noisettes disponibles dans les dossiers noisettes/
 *
 * @staticvar array $resultats
 * @param text $type
 * @param bool $informer
 * @return array
 */
function noizetier_lister_noisettes($type='tout',$informer=true){
	static $resultats = null;

	if (is_null($resultats[$type][$informer])){
		$resultats[$type][$informer] = array();
		
		// Si $type='tout' on recherche toutes les noisettes sinon seules celles qui commencent par $type
		if ($type=='tout')
			$prefix = '';
		else
			$prefix = $type.'-';
		
		$match = "[^-]*[.]html$";

		// lister les noisettes disponibles
		$liste = find_all_in_path('noisettes/', $prefix.$match);
		if (count($liste)){
			foreach($liste as $squelette=>$chemin) {
				$noisette = preg_replace(',[.]html$,i', '', $squelette);
				$dossier = str_replace($squelette, '', $chemin);
				// On ne garde que les squelettes ayant un XML de config
				if (file_exists("$dossier$noisette.xml")
					AND (
						$infos_noisette = !$informer OR ($infos_noisette = noizetier_charger_infos_noisette($dossier.$noisette))
					)){
					$resultats[$type][$informer][$noisette] = $infos_noisette;
				}
			}
		}
	}
	return $resultats[$type][$informer];
}

/**
 * Decrire une noisette
 *
 * @staticvar array $infos
 * @param string $noisette
 * @return array
 */
function noizetier_informer_noisette($noisette){
	static $infos = array();
	if (!isset($infos[$noisette])){
		$fichier = find_in_path("noisettes/$noisette.html");
		$infos[$noisette] = noizetier_charger_infos_noisette($fichier);
	}
	return $infos[$noisette];
}

/**
 * Charger les informations contenues dans le xml d'une noisette
 *
 * @param string $noisette
 * @param string $info
 * @return array
 */
function noizetier_charger_infos_noisette($noisette, $info=""){
		// on peut appeler avec le nom du squelette
		$fichier = preg_replace(',[.]html$,i','',$noisette).".xml";
		include_spip('inc/xml');
		include_spip('inc/texte');
		$infos_noisette = array();
		if ($xml = spip_xml_load($fichier, false)){
			if (count($xml['noisette'])){
				$xml = reset($xml['noisette']);
				$infos_noisette['nom'] = _T_ou_typo(spip_xml_aplatit($xml['nom']));
				$infos_noisette['description'] = isset($xml['description']) ? _T_ou_typo(spip_xml_aplatit($xml['description'])) : '';
				$infos_noisette['icone'] = isset($xml['icone']) ? find_in_path(reset($xml['icone'])) : '';
				// Décomposition des paramètres
				$infos_noisette['parametres'] = array();
				if (spip_xml_match_nodes(',^parametre,', $xml, $parametres)){
					foreach (array_keys($parametres) as $parametre){
						list($balise, $attributs) = spip_xml_decompose_tag($parametre);
						$infos_noisette['parametres'][$attributs['nom']] = array(
							'label' => $attributs['label'] ? _T($attributs['label']) : $attributs['nom'],
							'obligatoire' => $attributs['obligatoire'] == 'oui' ? true : false,
							'saisie' => $attributs['saisie'],
							'defaut' => $attributs['defaut']
						);
					}
				}
			}
		}
		if (!$info)
			return $infos_noisette;
		else 
			return isset($infos_noisette[$info]) ? $infos_noisette[$info] : "";
}

/**
 * Lister les pages utilisables par le noizetier définies dans noizetier/plugin-pages.xml
 *
 * @staticvar array $resultats
 * @param bool $informer
 * @return array
 */
function noizetier_lister_pages(){
	static $resultats = null;

	if (is_null($resultats)){
		$resultats = array();

		// lister les déclarations de pages
		$liste = find_all_in_path('noizetier/','pages.xml');
		if (count($liste)){
			foreach($liste as $chemin) {
				include_spip('inc/xml');
				include_spip('inc/texte');
				if ($xml = spip_xml_load($chemin, false)){
					foreach($xml[pages][0] as $tagpage => $xmlpage){
						// On récupère l'id de la page
						list($balise, $attributs) = spip_xml_decompose_tag($tagpage);
						$id_page = $attributs['id'];
						// On récupère ses infos
						$infos_page = array();
						$infos_page['nom'] = _T_ou_typo(spip_xml_aplatit($xmlpage[0]['nom']));
						$infos_page['description'] = isset($xmlpage[0]['description']) ? _T_ou_typo(spip_xml_aplatit($xmlpage[0]['description'])) : '';
						$infos_page['icone'] = isset($xmlpage[0]['icone']) ? find_in_path(reset($xmlpage[0]['icone'])) : '';
						// On ajoute les infos à $resultats
						if($id_page!='')
							$resultats[$id_page] = $infos_page;
					}
				}
			}
		}
	}
	return $resultats;
}

/**
 * Active une page pour le noizetier
 *
 * @param text $page
 * 
 */
function activer_page_noizetier($page) {
	if(!isset($GLOBALS['meta']['noizetier-pages-actives']))
		$pages_actives=array();
	else
		$pages_actives=unserialize($GLOBALS['meta']['noizetier-pages-actives']);
	$pages_actives[$page]='on';
	ecrire_meta('noizetier-pages-actives',serialize($pages_actives),'oui');
	
	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur($page);
}

/**
 * Active une page pour le noizetier
 *
 * @param text $page
 * 
 */
function desactiver_page_noizetier($page) {
	if(isset($GLOBALS['meta']['noizetier-pages-actives'])) {
		$pages_actives=unserialize($GLOBALS['meta']['noizetier-pages-actives']);
		unset($pages_actives[$page]);
		ecrire_meta('noizetier-pages-actives',serialize($pages_actives),'oui');
	}
	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur($page);

}

/**
 * Supprime de spip_noisettes les noisettes liées à une page
 *
 * @param text $page
 * 
 */
function supprimer_noisettes_page_noizetier($page) {
	$type_compo = explode ('-',$page,2);
	$type = $type_compo[0];
	$page = $type_compo[1];
	
	sql_delete('spip_noisettes','type='.sql_quote($type).'and composition='.sql_quote($composition));

	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur($page);
}

/**
 * Renvoie le type d'une page
 *
 * @param text $page
 * @return text
 */
function noizetier_page_type($page) {
	$type_compo = explode ('-',$page,2);
	return $type_compo[0];
}

/**
 * Renvoie la composition d'une page
 *
 * @param text $page
 * @return text
 */
function noizetier_page_composition($page) {
	$type_compo = explode ('-',$page,2);
	return $type_compo[1];
}

?>
