<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('noizetier_fonctions');
if (!function_exists('autoriser'))
	include_spip('inc/autoriser');	 // si on utilise le formulaire dans le public


// Détermine les compositions héritables par ce type de page
function heritiers($type){
	$heritiers = array();
	foreach (compositions_recuperer_heritage() as $enfant => $parent)
		if ($parent == $type)
			$heritiers = array_merge($heritiers,compositions_lister_disponibles($enfant));
	return $heritiers;
}


/**
 * Formulaire d'édition d'une page de composition de noisettes
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * 3 possibilités en fonction des paramètres :
 *
 * - Créer une page :     $new
 * - Modifier une page :  $page
 * - Dupliquer une page : $new + $page (on veut une nouvelle page basée sur une existante)
 *
 * @param string $page
 *     identifiant d'une composition
 * @param string $new
 *     pour créer une nouvelle composition
 * @param string $retour
 *     URL de redirection
 */
function formulaires_editer_page_charger_dist($page, $new, $retour=''){
	$valeurs = array();
	$valeurs['editable'] = autoriser('configurer','noizetier') ? 'on' : '';
	$noizetier_compositions = isset($GLOBALS['meta']['noizetier_compositions']) ? unserialize($GLOBALS['meta']['noizetier_compositions']) : array();

	// On définit l'action à effectuer en fonction des paramètres
	// $new :         création d'une nouvelle page
	// $page :        modification d'une page existante
	// $page + $new : création d'une nouvelle page prépeuplée avec le contenu d'une page existante = duplication
	$acte = ($page AND $new) ? 'dupliquer' : ($page ? 'modifier' : ($new ? 'creer' : ''));
	// on ne duplique pas les compositions mères
	//if ($acte == 'dupliquer' AND is_null(noizetier_page_composition($page))) $acte = 'creer';

	// modification ou duplication
	if (in_array($acte, array('modifier','dupliquer'))) {
		$type_page = noizetier_page_type($page);          // objet ou page
		$composition = noizetier_page_composition($page); // identifiant de la composition
		// On vérifie que cette composition existe
		// et si elle n'existe pas
		if (!is_array($noizetier_compositions[$type_page][$composition])){
			$contexte['editable'] = false;
			$contexte['message_erreur'] = _T('spip:erreur');
		}

		// si on duplique, on récupére également les infos des compos xml
		// (le meta ne liste que les compositions de noisettes)
		if ($acte == 'dupliquer') {
			$infos_xml_page = noizetier_lister_pages($page);
			$infos_compo_xml = array( $composition => array (
					'nom' => $infos_xml_page['nom'],
					'description' => $infos_xml_page['description'],
					'icon' => $infos_xml_page['icon']
			));
			$infos_compo_meta = $noizetier_compositions[$type_page];
			if (is_null($infos_compo_meta)) $infos_compo_meta = array();
			$infos_compo = array_merge($infos_compo_meta, $infos_compo_xml);
			$noizetier_compositions = array_merge($noizetier_compositions, array($type_page => $infos_compo));
		}

		$valeurs['page'] = $page;
		$valeurs['type_page'] = $type_page;
		$valeurs['composition'] = ($acte == 'modifier') ? $composition : ''; // si on duplique, nouvelle valeur
		if ($acte == 'dupliquer') $valeurs['composition_ref'] = $composition;
		$valeurs['nom'] = $noizetier_compositions[$type_page][$composition]['nom'];
		$valeurs['description'] = $noizetier_compositions[$type_page][$composition]['description'];
		$valeurs['icon'] = $noizetier_compositions[$type_page][$composition]['icon'];
		$valeurs['_heritiers'] = heritiers($type_page);
		foreach($valeurs['_heritiers'] as $t => $i)
			if (isset($noizetier_compositions[$type_page][$composition]['branche'][$t]))
				$valeurs['heritage-'.$t] = $noizetier_compositions[$type_page][$composition]['branche'][$t];
	}

	// création
	if ($acte == 'creer') {
		$valeurs['type_page'] = '';
		$valeurs['composition'] = '';
		$valeurs['nom'] = '';
		$valeurs['description'] = '';
		$valeurs['icon'] = '';
		// Définir la liste des objets avec compositions
		$valeurs['_objets_avec_compos'] = array();
		if(defined('_NOIZETIER_COMPOSITIONS_TYPE_PAGE') AND _NOIZETIER_COMPOSITIONS_TYPE_PAGE)
			$valeurs['_objets_avec_compos'][] = 'page';
		// Si on voulait se baser sur la config de compositions, on utiliserait compositions_objets_actives().
		// En fait, à la création d'une compo du noizetier, on modifiera la config de compositions.
		// On se base donc sur la liste des objets sur lesquels compositions est activable et qui dispose déjà d'une page dans le noizetier.
		$liste_pages = noizetier_lister_pages();
		include_spip('base/objets');
		foreach(lister_tables_objets_sql() as $objet)
			if (isset($objet['page']) && ($obj = $objet['page']) && isset($liste_pages[$obj]))
				$valeurs['_objets_avec_compos'][] = $obj;
	}

	$valeurs['page'] = $page;
	$valeurs['new'] = $new;
	$valeurs['acte'] = $acte;

	return $valeurs;
}


/**
 * Formulaire d'édition d'une page de composition de noisettes
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @param string $page
 *     identifiant d'une composition
 * @param string $new
 *     pour créer une nouvelle composition
 * @param string $retour
 *     URL de redirection
 */
function formulaires_editer_page_verifier_dist($page, $new, $retour=''){
	$erreurs = array();
	foreach(array('type_page','composition','nom') as $champ)
		if (!_request($champ))
			$erreurs[$champ] = _T('noizetier:formulaire_obligatoire');
	// On vérifie, dans le cas d'une nouvelle composition que $composition n'est pas déjà pris
	// On vérifie aussi que $composition ne contient ni espace, ni tiret
	if (_request('new') AND _request('composition')) {
		$type_page = _request('type_page');
		$composition = _request('composition');
		$liste_pages = noizetier_lister_pages();
		if (is_array($liste_pages[$type_page.'-'.$composition]))
			$erreurs['composition'] = _T('noizetier:formulaire_identifiant_deja_pris');
		if (preg_match('#^[a-z0-9_]+$#',$composition)==0)
			$erreurs['composition'] = _T('noizetier:formulaire_erreur_format_identifiant');
	}
	return $erreurs;
}


/**
 * Formulaire d'édition d'une page de composition de noisettes
 *
 * Traiter les champs postés
 *
 * @param string $page
 *     identifiant d'une composition
 * @param string $new
 *     pour créer une nouvelle composition
 * @param string $retour
 *     URL de redirection
 */
function formulaires_editer_page_traiter_dist($page, $new, $retour=''){
	if (!autoriser('configurer','noizetier'))
		return array('message_erreur' => _T('noizetier:probleme_droits'));

	$res = array();
	$noizetier_compositions = isset($GLOBALS['meta']['noizetier_compositions']) ? unserialize($GLOBALS['meta']['noizetier_compositions']) : array();
	$type_page = _request('type_page');
	$composition = _request('composition');
	$acte = ($page AND $new) ? 'dupliquer' : ($page ? 'modifier' : ($new ? 'creer' : ''));
	$peupler = ($acte == 'dupliquer') ? true : ( ($acte == 'creer' AND _request('peupler')) ? true : false );

	// Au cas où on n'a pas encore configuré de compositions
	if (!is_array($noizetier_compositions))
		$noizetier_compositions = array();

	$noizetier_compositions[$type_page][$composition] = array(
		'nom' => _request('nom'),
		'description' => _request('description'),
		'icon' => _request('icon')
	);

	$branche = array();
	foreach(heritiers($type_page) as $t => $i)
		if ($h = _request('heritage-'.$t))
			$branche[$t] = $h;
	if (count($branche)>0)
		$noizetier_compositions[$type_page][$composition]['branche'] = $branche;

	ecrire_meta('noizetier_compositions',serialize($noizetier_compositions));
	$retours['message_ok'] = _T('noizetier:formulaire_composition_mise_a_jour');

	// On pré-remplit avec les noisettes de la page mère en cas de nouvelle composition,
	// ou avec celles de la page de référence en cas de duplication
	if (
		$peupler
		AND $type_page != 'page'
	) {
		$composition_ref = ($acte == 'creer') ? '' : noizetier_page_composition($page); // si on ne créé pas, c'est qu'on duplique
		include_spip('base/abstract_sql');
		$config_mere = sql_allfetsel(
			'rang, type, composition, bloc, noisette, parametres',
			'spip_noisettes',
			'type='.sql_quote($type_page).' AND composition="'.$composition_ref.'"'
		);
		if (count($config_mere)>0) {
			foreach($config_mere as $cle => $noisette)
				$config_mere[$cle]['composition'] = $composition;
			sql_insertq_multi('spip_noisettes',$config_mere);
		}
		// On vérifie également que les compositions sont actives sur ce type d'objet
		$compositions_actives = compositions_objets_actives();
		if (!in_array($type_page,$compositions_actives)) {
			$compositions_config = unserialize($GLOBALS['meta']['compositions']);
			include_spip('base/objets');
			$compositions_config['objets'][] = table_objet_sql($type_page);
			ecrire_meta('compositions',serialize($compositions_config));
		}
	}

	// On invalide le cache en cas de création ou  de dpulication
	if (in_array($acte, array('creer','dupliquer'))) {
		include_spip('inc/invalideur');
		suivre_invalideur("id='page/$type_page-$composition'");
	}

	$res['message_ok'] = _T('info_modification_enregistree');
	if (in_array($acte,array('creer','dupliquer'))) {
		$res['redirect'] = parametre_url(parametre_url(self(),'new',''),'page',$type_page.'-'.$composition);
	}
	elseif ($retour) {
		if (strncmp($retour,'javascript:',11)==0){
			$res['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($retour,11).'/*]]>*/</script>';
			$res['editable'] = true;
		}
		else
			$res['redirect'] = $retour;
	}

	return $res;
}

?>
