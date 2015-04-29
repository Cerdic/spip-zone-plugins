<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Declaration du champs pour stocker les IP
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables Liste des objets et leur description
 * @return array
 */
function accesrestreintip_declarer_tables_objets_sql($tables){
	// déclaration du champ
	$tables['spip_zones']['field']['ips'] = 'text DEFAULT "" NOT NULL';
	// éditable etc
	$tables['spip_zones']['champs_editables'][] = 'ips';
	
	return $tables;
}

/**
 * Ajouter le champ des IP dans l'édition d'une zone
 *
 * @pipeline editer_contenu_objet
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/ 
function accesrestreintip_editer_contenu_objet($flux){
	if ($flux['args']['type'] == 'zone') {
		$champ_ips = recuperer_fond('formulaires/inc-editer_zone-ips', $flux['args']['contexte']);
		$flux['data'] = preg_replace('|(<li[^>]*editer_descriptif[^>]*>.*?</li>)|is', "$1\n$champ_ips", $flux['data']);
	}

	return $flux;
}

/**
 * Vérifier le champ des IP d'une zone
 *
 * @pipeline formulaire_verifier
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/ 
function accesrestreintip_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'editer_zone') {
	}

	return $flux;
}

/**
 * Ajoute les zones quand on détecte les bonnes IP
 *
 * @pipeline accesrestreint_liste_zones_autorisees
 * @param array $flux Données du pipeline
 * @return array      Données du pipeline
**/ 
function accesrestreintip_accesrestreint_liste_zones_autorisees($flux){
	// On s'assure d'avoir la fonction
	if (!function_exists('accesrestreintip_lister_zones_par_ip')) {
		include_spip('accesrestreintip_options');
	}
	
	// On récupère les zones par IP
	$zones_par_ip = array_filter(explode(',', accesrestreintip_lister_zones_par_ip()));
	
	// On ajoute les éventuelles zones par rapport à l'IP du visiteur
	$flux = explode(',', $flux);
	$flux = array_merge($flux, $zones_par_ip);
	$flux = array_unique($flux);
	$flux = join(',', $flux);
	return $flux;
}
