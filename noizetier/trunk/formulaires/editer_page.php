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

function formulaires_editer_page_charger_dist($page, $new, $retour=''){
	$valeurs = array();
	$valeurs['editable'] = autoriser('configurer','noizetier') ? 'on' : '';
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	
	// Si on demande une page
	if ($page!=''){
		// On désactive de toute façon le nouveau
		$new = '';
		$type_page = noizetier_page_type($page);
		$composition = noizetier_page_composition($page);
		// On vérifie que cette composition existe
		// et si elle n'existe pas
		if (!is_array($noizetier_compositions[$type_page][$composition])){
			$contexte['editable'] = false;
			$contexte['message_erreur'] = _T('spip:erreur');
		}
		$valeurs['page'] = $page;
		$valeurs['type_page'] = $type_page;
		$valeurs['composition'] = $composition;
		$valeurs['nom'] = $noizetier_compositions[$type_page][$composition]['nom'];
		$valeurs['description'] = $noizetier_compositions[$type_page][$composition]['description'];
		$valeurs['icon'] = $noizetier_compositions[$type_page][$composition]['icon'];
		$valeurs['_heritiers'] = heritiers($type_page);
		foreach($valeurs['_heritiers'] as $t => $i)
			if (isset($noizetier_compositions[$type_page][$composition]['branche'][$t]))
				$valeurs['heritage-'.$t] = $noizetier_compositions[$type_page][$composition]['branche'][$t];
	}
	
	if ($new) {
		$valeurs['type_page'] = '';
		$valeurs['composition'] = '';
		$valeurs['nom'] = '';
		$valeurs['description'] = '';
		$valeurs['icon'] = '';
		// Définir la liste des objets avec compositions
		$valeurs['_objets_avec_compos'] = array();
		if(defined('_NOIZETIER_COMPOSITIONS_TYPE_PAGE') AND _NOIZETIER_COMPOSITIONS_TYPE_PAGE)
			$valeurs['_objets_avec_compos'][] = 'page';
		//Si on voulait se baser sur la config de compositions, on utiliserai compositions_objets_actives(). En fait, à la création d'une compo du noizetier, on modifiera la config de compositions. On se base donc sur la liste des objets sur lesquels compositions est activable et qui dispose déjà d'une page dans le noizetier.
		$liste_pages = noizetier_lister_pages();
		include_spip('base/objets');
		foreach(lister_tables_objets_sql() as $objet)
			if (isset($objet['page']) && ($obj = $objet['page']) && isset($liste_pages[$obj]))
				$valeurs['_objets_avec_compos'][] = $obj;
	}
	
	$valeurs['page'] = $page;
	$valeurs['new'] = $new;
	
	return $valeurs;
}

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

function formulaires_editer_page_traiter_dist($page, $new, $retour=''){
	if (!autoriser('configurer','noizetier'))
		return array('message_erreur' => _T('noizetier:probleme_droits'));
	
	$res = array();
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	$type_page = _request('type_page');
	$composition = _request('composition');
	
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
	
	// Si nouvelle composition, on la pré-remplie avec les noisettes de la page mère.
	if ($new && $type_page!='page') {
		include_spip('base/abstract_sql');
		$config_mere = sql_allfetsel(
			'rang, type, composition, bloc, noisette, parametres',
			'spip_noisettes',
			'type='.sql_quote($type_page).' AND composition=""'
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
	
	if($new) {
		// On invalide le cache
		include_spip('inc/invalideur');
		suivre_invalideur("id='page/$type_page-$composition'");
	}
	
	$res['message_ok'] = _T('info_modification_enregistree');
	if ($new) {
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
