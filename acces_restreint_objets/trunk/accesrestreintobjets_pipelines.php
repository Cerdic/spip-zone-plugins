<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Ajouter le formulaire de config
function accesrestreintobjets_affiche_milieu($flux){
	if ($flux["args"]["exec"] == "configurer_accesrestreint") {
		$flux["data"] =  recuperer_fond('prive/squelettes/inclure/configurer',array('configurer'=>'configurer_accesrestreintobjets')).$flux["data"];
	}
	return $flux;
}

function accesrestreintobjets_afficher_contenu_objet($flux){
	include_spip('inc/config');
	include_spip('inc/autoriser');
	$objets_ok = lire_config('accesrestreintobjets/objets');
	
	if (
		// Si on a les arguments qu'il faut
		$type = $flux['args']['type']
		and $id = intval($flux['args']['id_objet'])
		// Si on est sur un objet restrictible
		and in_array(table_objet_sql($flux['args']['type']), $objets_ok)
		// Et que l'on peut configurer le site
		and autoriser('configurer')
	) {
		$liens = recuperer_fond(
			'prive/objets/editer/liens',
			array(
				'table_source'=>'zones',
				'objet' => $type,
				'id_objet' => $id,
			)
		);
		$flux['data'] = $liens.$flux['data'];
	}
	
	return $flux;
}

// Invalider le cache quand on ajoute ou enlève quelque chose à une zone
function accesrestreintobjets_post_edition_lien($flux){
	// Si on a modifié un lien avec une zone (ajout ou retrait, peu importe) : on invalide le cache
	if ($flux['args']['objet_source'] == 'zone'){
		include_spip('inc/invalideur');
		suivre_invalideur(1);
	}
	
	return $flux;
}
