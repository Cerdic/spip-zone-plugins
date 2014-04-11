<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function accesrestreintobjets_affiche_milieu($flux){
	// Ajouter le formulaire de config
	if ($flux["args"]["exec"] == "configurer_accesrestreint") {
		$flux["data"] =  recuperer_fond('prive/squelettes/inclure/configurer',array('configurer'=>'configurer_accesrestreintobjets')).$flux["data"];
	}
	// Ajouter la config des zones sur la vue de chaque objet autorisé
	elseif (
		$exec = trouver_objet_exec($flux['args']['exec'])
		and include_spip('inc/config')
		and include_spip('inc/autoriser')
		and $objets_ok = lire_config('accesrestreintobjets/objets')
		// Si on a les arguments qu'il faut
		and $type = $exec['type']
		and $id = intval($flux['args'][$exec['id_table_objet']])
		// Si on est sur un objet restrictible
		and in_array($exec['table_objet_sql'], $objets_ok)
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
		if ($liens){
			if ($pos = strpos($flux['data'],'<!--affiche_milieu-->'))
				$flux['data'] = substr_replace($flux['data'], $liens, $pos, 0);
			else
				$flux['data'] .= $liens;
		}
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

function accesrestreintobjets_recuperer_fond($flux){
	if ($flux['args']['fond'] == 'prive/squelettes/contenu/zone_edit'){
		include_spip('inc/config');
		if ($objets = lire_config('accesrestreintobjets/objets') and is_array($objets)){
			$objets = array_map('objet_type', $objets);
			$flux['data']['texte'] .= recuperer_fond(
				'prive/objets/liste/zone_liaisons',
				array(
					'id_zone' => $flux['args']['contexte']['id_zone'],
					'objets' => $objets,
				)
			);
		}
	}
	
	return $flux;
}

