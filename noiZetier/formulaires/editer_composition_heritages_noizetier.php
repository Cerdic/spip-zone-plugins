<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function heritiers(){
	// Pour le moment (à reprendre en SPIP 3.0), on définit en dur les types pouvant recevoir un héritage
	return array('article','rubrique','breve','site');
};

function formulaires_editer_composition_heritages_noizetier_charger($id){
	include_spip('inc/autoriser');
	$contexte = array();
	
	$type = noizetier_page_type($id);
	$compo = noizetier_page_composition($id);
	// Seulement si on a le droit de configurer le noizetier
	// et qu'il s'agit d'une composition de rubrique
	if (autoriser('configurer', 'noizetier') AND $type AND $type=='rubrique') {
		$contexte['editable'] = true;
	} else {
		$contexte['message_erreur'] = _T('spip:erreur');
	}

	// Si on peut bien éditer la composition
	if ($contexte['editable']){
		$contexte['_noizetier_compositions'] = unserialize($GLOBALS['meta']['noizetier_compositions']);
		$contexte['_heritiers'] = heritiers();
		foreach($contexte['_heritiers'] as $t)
			if (isset($contexte['_noizetier_compositions']['rubrique'][$compo]['branche'][$t]))
				$contexte['heritage-'.$t] = $contexte['_noizetier_compositions']['rubrique'][$compo]['branche'][$t];
	}
	else{
		$contexte['editable'] = false;
	}

	return $contexte;
}

function formulaires_editer_composition_heritages_noizetier_traiter($id){
	include_spip('inc/autoriser');
	include_spip('inc/noizetier');
	$retours = array();
	$type = noizetier_page_type($id);
	$compo = noizetier_page_composition($id);
	if (autoriser('configurer', 'noizetier') AND $type AND $type=='rubrique') {
		$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
		$branche = array();
		foreach(heritiers() as $t)
			if ($h = _request('heritage-'.$t))
				$branche[$t] = $h;
		if (count($branche)>0)
			$noizetier_compositions['rubrique'][$compo]['branche'] = $branche;
		else
			unset($noizetier_compositions['rubrique'][$compo]['branche']);
		ecrire_meta('noizetier_compositions',serialize($noizetier_compositions));
		$retours['message_ok'] = _T('noizetier:formulaire_composition_mise_a_jour');
		
		//Si on est dans l'espace privé, on redirige vers la liste des compos
		if (_request('exec') == 'noizetier_composition_heritages')
			$retours['redirect'] = generer_url_ecrire('noizetier_compositions');
	}
	return $retours;
}

?>
