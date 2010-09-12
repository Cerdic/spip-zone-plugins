<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;



define('_CACHE_CONTEXTE_NOISETTES', 'noisettes_contextes.php');
define('_CACHE_DESCRIPTIONS_NOISETTES', 'noisettes_descriptions.php');


/**
 * Lister les noisettes disponibles dans les dossiers noisettes/
 *
 * @staticvar array $liste_noisettes
 * @param text $type
 * @return array
 */
function noizetier_lister_noisettes($type='tout'){
	static $liste_noisettes = array();
	if ($type == 'tout') {
		return noizetier_obtenir_infos_noisettes();
	}
	if (isset($liste_noisettes[$type])) {
		return $liste_noisettes[$type];
	}
	
	$noisettes = noizetier_obtenir_infos_noisettes();
	if ($type == '') {
		$match = "^[^-]*$";
	} else {
		$match = $type."-[^-]*$";
	}
	
	foreach($noisettes as $noisette => $description) {
		if (preg_match("/$match/", $noisette)) {
			$liste_noisettes[$type][$noisette] = $description;
		}
	}
	
	return $liste_noisettes[$type];
}



/**
 * Obtenir les infos de toutes les noisettes disponibles dans les dossiers noisettes/
 * On utilise un cache php pour alleger le calcul.
 *
 * @param 
 * @return 
**/
function noizetier_obtenir_infos_noisettes() {
	static $noisettes = false;
	
	// seulement 1 fois par appel, on lit ou calcule tous les contextes
	if ($noisettes === false) {
		// lire le cache des contextes sauves
		lire_fichier_securise(_DIR_CACHE . _CACHE_DESCRIPTIONS_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);
		// s'il en mode recalcul, on recalcule toutes les descriptions des noisettes trouvees.
		if (!$noisettes or (_request('var_mode') == 'recalcul')) {
			$noisettes = noizetier_obtenir_infos_noisettes_direct();
			ecrire_fichier_securise(_DIR_CACHE . _CACHE_DESCRIPTIONS_NOISETTES, serialize($noisettes));
		}
	}
	
	return $noisettes;
}


/**
 * Obtenir les infos de toutes les noisettes disponibles dans les dossiers noisettes/
 * C'est un GROS calcul lorsqu'il est a faire.
 *
 * @return array
 */
function noizetier_obtenir_infos_noisettes_direct(){

	$liste_noisettes = array();
		
	$match = "[^-]*[.]html$";
	$liste = find_all_in_path('noisettes/', $match);
		
	if (count($liste)){
		foreach($liste as $squelette=>$chemin) {
			$noisette = preg_replace(',[.]html$,i', '', $squelette);
			$dossier = str_replace($squelette, '', $chemin);
			// On ne garde que les squelettes ayant un fichier YAML de config (si yaml est activé)
			if (file_exists("$dossier$noisette.yaml") AND defined('_DIR_PLUGIN_YAML')
				AND ($infos_noisette = noizetier_charger_infos_noisette_yaml($dossier.$noisette))
			){
				$liste_noisettes[$noisette] = $infos_noisette;
			}
			// ou les squelettes ayant un XML de config
			elseif (file_exists("$dossier$noisette.xml")
				AND ($infos_noisette = noizetier_charger_infos_noisette_xml($dossier.$noisette))
			){
				$liste_noisettes[$noisette] = $infos_noisette;
			}
		}
	}
	
	// supprimer de la liste les noisettes necissant un plugin qui n'est pas actif
	foreach ($liste_noisettes as $noisette => $infos_noisette)
		if (isset($infos_noisette['necessite']))
			foreach ($infos_noisette['necessite'] as $plugin)
				if (!defined('_DIR_PLUGIN_'.strtoupper($plugin)))
					unset($liste_noisettes[$noisette]);
	
	return $liste_noisettes;
}




/**
 * Charger les informations contenues dans le xml d'une noisette
 *
 * @param string $noisette
 * @param string $info
 * @return array
 */
function noizetier_charger_infos_noisette_xml($noisette, $info=""){
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
				$infos_noisette['icon'] = isset($xml['icon']) ? find_in_path(reset($xml['icon'])) : '';
				// Décomposition des paramètres (enregistrer sous la forme d'un tableau respectant la norme de saisies
				$infos_noisette['parametres'] = array();
				if (spip_xml_match_nodes(',^parametre,', $xml, $parametres)){
					foreach (array_keys($parametres) as $parametre){
						list($balise, $attributs) = spip_xml_decompose_tag($parametre);
						$infos_noisette['parametres'][] = array(
							'saisie' => $attributs['saisie'] ? $attributs['saisie'] : 'input',
							'options' => array(
								'nom' => $attributs['nom'],
								'label' => $attributs['label'] ? _T($attributs['label']) : $attributs['nom'],
								'defaut' => $attributs['defaut'],
								'obligatoire' => $attributs['obligatoire'] == 'oui' ? 'oui' : 'non'
							)
						);
					}
				}
				if (spip_xml_match_nodes(',^necessite,', $xml, $necessites)){
					$infos_noisette['necessite'] = array();
					foreach (array_keys($necessites) as $necessite){
						list($balise, $attributs) = spip_xml_decompose_tag($necessite);
						$infos_noisette['necessite'][] = $attributs['id'];
					}
				}
				// Décomposition informations du contexte a utiliser
				if (spip_xml_match_nodes(',^contexte,', $xml, $contextes)){
					$infos_noisette['contexte'] = array();
					foreach (array_keys($contextes) as $contexte){
						list($balise, $attributs) = spip_xml_decompose_tag($contexte);
						$infos_noisette['contexte'][] = $attributs['nom'];
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
 * Charger les informations contenues dans le YAML d'une noisette
 *
 * @param string $noisette
 * @param string $info
 * @return array
 */
function noizetier_charger_infos_noisette_yaml($noisette, $info=""){
		// on peut appeler avec le nom du squelette
		$fichier = preg_replace(',[.]html$,i','',$noisette).".yaml";
		include_spip('inc/yaml');
		include_spip('inc/texte');
		$infos_noisette = array();
		if ($infos_noisette = yaml_decode_file($fichier)) {
			if (isset($infos_noisette['nom']))
				$infos_noisette['nom'] = _T_ou_typo($infos_noisette['nom']);
			if (isset($infos_noisette['description']))
				$infos_noisette['description'] = _T_ou_typo($infos_noisette['description']);
			if (isset($infos_noisette['icon']))
				$infos_noisette['icon'] = find_in_path($infos_noisette['icon']);
				
			if (!isset($infos_noisette['parametres']))
				$infos_noisette['parametres'] = array();
				
			// contexte
			if (!isset($infos_noisette['contexte'])) {
				$infos_noisette['contexte'] = array();
			}
			if (is_string($infos_noisette['contexte'])) {
				$infos_noisette['contexte'] = array($infos_noisette['contexte']);
			}

		}

		if (!$info)
			return $infos_noisette;
		else 
			return isset($infos_noisette[$info]) ? $infos_noisette[$info] : "";
}

/**
 * Charger les informations des paramètres d'une noisette
 *
 * @param string $noisette
 * @staticvar array $params_noisettes
 * @return array
 */
function noizetier_charger_parametres_noisette($noisette){
	static $params_noisettes = null;

	if (is_null($params_noisettes[$noisette])){
		$noisettes = noizetier_lister_noisettes();
		$infos = $noisettes[$noisette];
		$params_noisettes[$noisette] = extrait_parametres_noisette($infos['parametres']);
	}
	return $params_noisettes[$noisette];
}

/**
 * Charger les informations des contexte pour une noisette
 *
 * @param string $noisette
 * @staticvar array $params_noisettes
 * @return array
 */
function noizetier_charger_contexte_noisette($noisette){
	static $contexte_noisettes = null;

	if (is_null($contexte_noisettes[$noisette])){
		$noisettes = noizetier_lister_noisettes();
		$contexte_noisettes[$noisette] =  $noisettes[$noisette]['contexte'];
	}
	return $contexte_noisettes[$noisette];
}


/**
 * Transforme un tableau au format du plugin saisies en un tableau de parametres dont les clés sont les noms des paramètres
 * S'il y a de fieldset, les paramètres sont extraits de son entrée saisie
 *
 * @param string $parametres
 * @return array
 */
function extrait_parametres_noisette($parametres){
	$res = array();
	foreach($parametres as $parametre) {
		if ($parametre['saisie']!='fieldset') {
			$nom = $parametre['options']['nom'];
			$res[$nom] = $parametre['options'];
			$res[$nom]['saisie'] = $parametre['saisie'];
			if(isset($parametre['verifier']))
				$res[$nom]['verifier']=$parametre['verifier'];
			if(isset($res[$nom]['label']))
				$res[$nom]['label'] = _T_ou_typo($res[$nom]['label']);
			if(isset($res[$nom]['datas']))
				foreach($res[$nom]['datas'] as $cle => $valeur)
					$res[$nom]['datas'][$cle] = _T_ou_typo($res[$nom]['datas'][$cle]);
		}
		else {
			$res = array_merge($res,extrait_parametres_noisette($parametre['saisies']));
		}
	}
	return $res;
}

/**
 * Lister les pages pouvant recevoir des noisettes
 * Par défaut, cette liste est basée sur le contenu du répertoire contenu/
 * Le tableau de résultats peut-être modifié via le pipeline noizetier_lister_pages.
 *
 * @staticvar array $liste_pages
 * @return array
 */
function noizetier_lister_pages(){
	static $liste_pages = null;

	if (is_null($liste_pages)){
		$liste_pages = array();
		$match = ".+[.]html$";

		// lister les fonds disponibles dans le répertoire contenu
		$rep = defined('_NOIZETIER_REPERTOIRE_PAGES')?_NOIZETIER_REPERTOIRE_PAGES:'contenu/';
		$liste = find_all_in_path($rep, $match);
		if (count($liste)){
			foreach($liste as $squelette=>$chemin) {
				$page = preg_replace(',[.]html$,i', '', $squelette);
				$dossier = str_replace($squelette, '', $chemin);
				// Les éléments situés dans prive/contenu sont écartés
				if (substr($dossier,-14)!='prive/contenu/')
					if(count($infos_page = noizetier_charger_infos_page($dossier,$page))>0)
						$liste_pages[$page] = $infos_page;
			}
		}
		
		// Dans le cas de Zpip, il faut supprimer la page 'page.html' et la page 'z_apl.html'
		if (defined('_DIR_PLUGIN_Z')) {
			unset($liste_pages['page']);
			unset($liste_pages['z_apl']);
		}
		
		// supprimer de la liste les pages necissant un plugin qui n'est pas actif
		foreach ($liste_pages as $page => $infos_page)
			if (isset($infos_page['necessite']))
				foreach ($infos_page['necessite'] as $plugin)
					if (!defined('_DIR_PLUGIN_'.strtoupper($plugin)))
						unset($liste_pages[$page]);
		
		$liste_pages = pipeline('noizetier_lister_pages',$liste_pages);
		
		// On ajoute les compositions du noizetier
		if(defined('_DIR_PLUGIN_COMPOSITIONS')){
			$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
			// On doit transformer le tableau de [type][compo] en [type-compo]
			$liste_compos = array();
			if (is_array($noizetier_compositions)) {
				foreach($noizetier_compositions as $type => $compos_type)
					foreach($compos_type as $compo => $infos_compo) {
						$infos_compo['nom'] = typo($infos_compo['nom']);
						$infos_compo['description'] = propre($infos_compo['description']);
						$infos_compo['icon'] = $infos_compo['icon']!='' ? find_in_path($infos_compo['icon']) : '';
						if (isset($liste_pages[$type]))
							$infos_compo['blocs'] = $liste_pages[$type]['blocs'];
						else
							$infos_compo['blocs'] = noizetier_blocs_defaut();
						$liste_compos[$type.'-'.$compo] = $infos_compo;
						}
			}
			$liste_pages = array_merge($liste_pages,$liste_compos);
		}
	}
	return $liste_pages;
}

/**
 * Charger les informations d'une page, contenues dans un xml de config s'il existe
 *
 * @param string $dossier
 * @param string $page
 * @param string $info
 * @return array
 */
function noizetier_charger_infos_page($dossier,$page, $info=""){
		// on peut appeler avec le nom du squelette
		$page = preg_replace(',[.]html$,i','',$page);
		
		// On autorise le fait que le fichier xml ne soit pas dans le même plugin que le fichier .html
		// Au cas où le fichier .html soit surchargé sans que le fichier .xml ne le soit
		$rep = defined('_NOIZETIER_REPERTOIRE_PAGES')?_NOIZETIER_REPERTOIRE_PAGES:'contenu/';
		$fichier = find_in_path("$rep$page.xml");
		
		include_spip('inc/xml');
		include_spip('inc/texte');
		$infos_page = array();
		// S'il existe un fichier xml de configuration (s'il s'agit d'une composition on utilise l'info de la composition)
		if (file_exists($fichier) AND $xml = spip_xml_load($fichier, false) AND count($xml['page']))
			$xml = reset($xml['page']);
		elseif (file_exists($fichier) AND $xml = spip_xml_load($fichier, false) AND count($xml['composition']))
			$xml = reset($xml['composition']);
		else
			$xml = '';
		if ($xml != '') {
			$infos_page['nom'] = _T_ou_typo(spip_xml_aplatit($xml['nom']));
			$infos_page['description'] = isset($xml['description']) ? _T_ou_typo(spip_xml_aplatit($xml['description'])) : '';
			$infos_page['icon'] = isset($xml['icon']) ? find_in_path(reset($xml['icon'])) : '';
			// Décomposition des blocs
			if (spip_xml_match_nodes(',^bloc,', $xml, $blocs)){
				$infos_page['blocs'] = array();
				foreach (array_keys($blocs) as $bloc){
					list($balise, $attributs) = spip_xml_decompose_tag($bloc);
					$infos_page['blocs'][$attributs['id']] = array(
						'nom' => $attributs['nom'] ? _T($attributs['nom']) : $attributs['id'],
						'icon' => isset($attributs['icon']) ? find_in_path($attributs['icon']) : '',
						'description' => _T($attributs['description'])
					);
				}
			}
			if (spip_xml_match_nodes(',^necessite,', $xml, $necessites)){
				$infos_page['necessite'] = array();
				foreach (array_keys($necessites) as $necessite){
					list($balise, $attributs) = spip_xml_decompose_tag($necessite);
					$infos_page['necessite'][] = $attributs['id'];
				}
			}
		}
		// S'il n'y a pas de fichier XML de configuration
		elseif (defined('_NOIZETIER_LISTER_PAGES_SANS_XML')?_NOIZETIER_LISTER_PAGES_SANS_XML:true) {
			$infos_page['nom'] = $page;
			$infos_page['icon'] = find_in_path('img/ic_page.png');
		}
		
		// Si les blocs n'ont pas été définis, on applique les blocs par défaut
		if (count($infos_page)>0 AND !isset($infos_page['blocs']))
			$infos_page['blocs'] = noizetier_blocs_defaut();
		
		// On renvoie les infos
		if (!$info)
			return $infos_page;
		else 
			return isset($infos_page[$info]) ? $infos_page[$info] : "";
}

/**
 * Charger les informations d'une page, contenues dans un xml de config s'il existe
 * La liste des blocs par défaut d'une page peut être modifiée via le pipeline noizetier_blocs_defaut.
 *
 * @staticvar array $blocs_defaut
 * @return array
 */
function noizetier_blocs_defaut(){
	static $blocs_defaut = null;

	if (is_null($blocs_defaut)){
	$blocs_defaut = array (
		'contenu' => array(
			'nom' => _T('noizetier:nom_bloc_contenu'),
			'description' => _T('noizetier:description_bloc_contenu'),
			'icon' => find_in_path('img/ic_bloc_contenu.png')
			),
		'navigation' => array(
			'nom' => _T('noizetier:nom_bloc_navigation'),
			'description' => _T('noizetier:description_bloc_navigation'),
			'icon' => find_in_path('img/ic_bloc_navigation.png')
			),
		'extra' => array(
			'nom' => _T('noizetier:nom_bloc_extra'),
			'description' => _T('noizetier:description_bloc_extra'),
			'icon' => find_in_path('img/ic_bloc_extra.png')
			),
	);
	$blocs_defaut = pipeline('noizetier_blocs_defaut',$blocs_defaut);
	}
	return $blocs_defaut;
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
	$composition = $type_compo[1];
	
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

/**
 * Liste les blocs pour lesquels il y a des noisettes à insérer.
 *
 * @staticvar array $liste_blocs
 * @return array
 */
function noizetier_lister_blocs_avec_noisettes(){
	static $liste_blocs = null;
	
	if (is_null($liste_blocs)){
		$liste_blocs = array();
		include_spip('base/abstract_sql');
		$resultats = sql_allfetsel (
			"CONCAT(`bloc`,'/',`type`,'-',`composition`)",
			'spip_noisettes',
			'1',
			'`bloc`,`type`,`composition`'
		);
		foreach ($resultats as $res) {
			$res = array_values($res);
			if (substr($res[0],-1)=='-')
				$liste_blocs[] = substr($res[0],0,-1);
			else
				$liste_blocs[] = $res[0];
		}
	}
	
	return $liste_blocs;
}

/**
 * Liste d'icônes obtenues en fouillant les répertoires img/ images/ image/ et /img-pack.
 *
 * @staticvar array $liste_icones
 * @return array
 */
function noizetier_lister_icones(){
	static $liste_icones = null;
	
	if (is_null($liste_icones)){
		$match = ".+[.](jpg|jpeg|png|gif)$";
		$liste_icones = array(
			'img' => find_all_in_path('img/', $match),
			'img-pack' => find_all_in_path('img-pack/', $match),
			'image' => find_all_in_path('image/', $match),
			'images' => find_all_in_path('images/', $match)
		);
	}
	
	return $liste_icones;
}

/**
 * Liste des configurations du noizetier disponibles
 * Fichiers YAML situés dans un sous-répertoire config_noizetier
 *
 * @staticvar array $liste_config
 * @return array
 */
function noizetier_lister_config(){
	static $liste_config = null;
	include_spip('inc/yaml');
	
	if (is_null($liste_config)){
		$liste_config = array();
		$match = ".+[.]yaml$";
		foreach (find_all_in_path('config_noizetier/', $match) as $fichier => $chemin) {
			// On lit le fichier, on vérifie les plugins demandé on vérifie s'il y a un champs nom
			$config = yaml_decode_file($chemin);
			$ok = true;
			if (isset($config['necessite']))
				foreach($config['necessite'] as $plugin)
					if (!defined('_DIR_PLUGIN_'.strtoupper($plugin)))
						$ok = false;
			//on vérifie s'il y a un champs nom
			if ($ok) {
				if (isset($config['nom']))
					$liste_config[$chemin] = _T_ou_typo($config['nom']);
				else
					$liste_config[$chemin] = $fichier;
			}
		}
	}
	return $liste_config;
}



/**
 * Retourne les elements du contexte uniquement
 * utiles a la noisette demande.
 *
 * @param 
 * @return 
**/
function noizetier_choisir_contexte($noisette, $contexte_entrant) {
	if (!$parametres) $parametres = array();
	if (!$contexte_entrant) $contexte_entrant = array();
	$contexte_noisette = array_flip(noizetier_obtenir_contexte($noisette));

	if (isset($contexte_noisette['aucun'])) {
		return array();
	}
	if (isset($contexte_noisette['env'])) {
		return $contexte_entrant;
	}
	if ($contexte_noisette) {
		return array_intersect_key($contexte_entrant, $contexte_noisette);
	}
	
	return $contexte_entrant;
}



/**
 * Retourne la liste des contextes donc peut avoir besoin une noisette. 
 *
 * @param 
 * @return 
**/
function noizetier_obtenir_contexte($noisette) {
	static $noisettes = false;
	
	// seulement 1 fois par appel, on lit ou calcule tous les contextes
	if ($noisettes === false) {
		// lire le cache des contextes sauves
		lire_fichier_securise(_DIR_CACHE . _CACHE_CONTEXTE_NOISETTES, $noisettes);
		$noisettes = @unserialize($noisettes);
		
		// s'il en mode recalcul, on recalcule tous les contextes des noisettes trouvees.
		if (!$noisettes or (_request('var_mode') == 'recalcul')) {
			include_spip('inc/noizetier');
			$infos = noizetier_lister_noisettes();
			$noisettes = array();
			foreach ($infos as $noisette => $infos) {
				$noisettes[$noisette] = ($infos['contexte'] ? $infos['contexte'] : array());
			}
			ecrire_fichier_securise(_DIR_CACHE . _CACHE_CONTEXTE_NOISETTES, serialize($noisettes));
		}
	}

	if (isset($noisettes[$noisette])) {
		return $noisettes[$noisette];
	}
	
	return array();
}


/**
 * Retourne le tableau des noisettes et des compositions du noizetier pour les exports
 *
 * 
 * @return 
**/
function noizetier_tableau_export() {
	$data = array();
	
	// On calcule le tableau des noisettes
	$data['noisettes'] = sql_allfetsel(
		'type, composition, bloc, noisette, parametres',
		'spip_noisettes',
		'1',
		'',
		'type, composition, bloc, rang'
	);
	
	// On remet au propre les parametres
	foreach ($data['noisettes'] as $cle => $noisette)
		$data['noisettes'][$cle]['parametres'] = unserialize($noisette['parametres']);
	
	// On récupère les compositions du noizetier
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	if (is_array($noizetier_compositions) AND count($noizetier_compositions)>0)
		$data['noizetier_compositions'] = $noizetier_compositions;
	
	return $data;
}

/**
 * Importe une configuration de noisettes et de compositions
 *
 * @param text $type_import
 * @param text $import_compos
 * @param array $config
 * @return boolean
 */
function noizetier_importer_configuration($type_import, $import_compos, $config){
	if ($import_compos!='remplacer')
		$import_compos = 'fusion';
	if ($import_compos!='oui')
		$import_compos = 'non';
	
	// On s'occupe déjà des noisettes
	$noisettes = $config['noisettes'];
	include_spip('base/abstract_sql');
	if (is_array($noisettes) AND count($noisettes)>0) {
		$noisettes_insert = array();
		$rang = 1;
		$page = '';
		if ($type_import=='remplacer')
			sql_delete('spip_noisettes','1');
		foreach($noisettes as $noisette) {
			$type = $noisette['type'];
			$composition = $noisette['composition'];
			if ($type.'-'.$composition!=$page) {
				$page = $type.'-'.$composition;
				$rang = 1;
				if ($type_import=='fusion')
					$rang = sql_getfetsel('rang','spip_noisettes','type='.sql_quote($type).' AND composition='.sql_quote($composition),'','rang DESC') + 1;
			}
			else {
				$rang = $rang + 1;
			}
			$noisette['rang']=$rang;
			$noisette['parametres'] = serialize($noisette['parametres']);
			$noisettes_insert[] = $noisette;
		}
		$ok = sql_insertq_multi('spip_noisettes',$noisettes_insert);
	}
	
	// On s'occupe des compositions du noizetier
	if ($import_compos=='oui') {
		include_spip('inc/meta');
		$compos_importees = $config['noizetier_compositions'];
		if (is_array($compos_importees) AND count($compos_importees)>0){
			if ($type_import=='remplacer')
				effacer_meta('noizetier_compositions');
			else 
				$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
			
			if (!is_array($noizetier_compositions))
				$noizetier_compositions = array();
			
			foreach($compos_importees as $type => $compos_type)
				foreach($compos_type as $composition => $info_compo)
					$noizetier_compositions[$type][$composition] = $info_compo;
			
			ecrire_meta('noizetier_compositions',serialize($noizetier_compositions));
			ecrire_metas();
		}
	}
	
	// On invalide le cache
	include_spip('inc/invalideur');
	suivre_invalideur('noizetier-import-config');
	
	return $ok;
}

?>
