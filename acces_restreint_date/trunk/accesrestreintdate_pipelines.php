<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function accesrestreintdate_affiche_gauche($flux){
	// Si on est sur la vue d'une rubrique et qu'on a le droit d'administrer les zones
	if (
		$exec = trouver_objet_exec($flux['args']['exec'])
		and $exec['type'] == 'rubrique'
		and $exec['edition'] == false
		and $id_rubrique = $flux['args']['id_rubrique']
		and autoriser('administrer', 'zone', 0)
	) {
		$flux['data'] .= recuperer_fond(
			'prive/squelettes/inclure/acces_restreint_date',
			array('objet'=>'rubrique', 'id_objet'=>$id_rubrique),
			array('ajax' => true)
		);
	}
	
	return $flux;
}

function accesrestreintdate_accesrestreint_liste_objets_exclus($flux){
	// Pour l'instant on ne gère que les articles
	if ($flux['args']['table_objet'] == 'articles') {
		$objet = objet_type($flux['args']['table_objet']);
		$id_auteur = $flux['args']['id_auteur'];
		$where = array();
		
		// Pour les articles, on doit chercher les rubriques
		$where[] = array('=', 'objet', sql_quote('rubrique'));
		
		// On cherche les zones auxquelles le visiteur n'a PAS accès
		if (
			$GLOBALS['accesrestreint_zones_autorisees']
			and $id_auteur == $GLOBALS['visiteur_session']['id_auteur']
		) {
			$where[] = sql_in('id_zone', $GLOBALS['accesrestreint_zones_autorisees'], 'NOT');
		}
		// Sinon on calcule les zones d'un auteur, lorsqu'il y en a un
		elseif ($id_auteur) {
			$where[] = sql_in('id_zone', accesrestreint_liste_zones_autorisees('', $id_auteur), 'NOT');
		}
		
		// On cherche les configs de date, qui ne sont PAS pour des zones autorisées
		if ($listes_rubriques_dates = sql_allfetsel(
			'*',
			'spip_zones_dates',
			$where
		)) {
			// Pour chaque rubrique configurée avec une date
			foreach ($listes_rubriques_dates as $rubrique_date) {
				// Avant ou après la date à comparer
				$quand = ($rubrique_date['quand'] == 'avant') ? '>' : '<';
				
				// À quand comparer la date
				switch ($rubrique_date['periode']){
					case 'jours':
						$date_comparaison = " - ${rubrique_date['duree']} days";
						break;
					case 'mois':
						$date_comparaison = " - ${rubrique_date['duree']} months";
						break;
					default:
						$date_comparaison ='';
						break;
				}
				
				// Si on a bien ce qu'il faut pour la comparaison
				if ($date_comparaison and $quand) {
					// La date à comparer est N jours ou mois avant aujourd'hui
					$date_comparaison = date('Y-m-d H:i:s', strtotime($date_comparaison));
					
					// On cherche tous les rubriques qui héritent de cette restriction
					include_spip('inc/rubriques');
					$rubriques = explode(',', calcul_branche_in($rubrique_date['id_objet']));
					
					// On cherche alors tous les articles avant ou après la date
					if ($listes_objets = sql_allfetsel(
						'id_article',
						'spip_articles',
						array(
							array($quand, 'date', sql_quote($date_comparaison)),
							sql_in('id_rubrique', $rubriques),
						)
					)) {
						$listes_objets = array_map('reset', $listes_objets);
						$flux['data'] = array_unique(array_merge($flux['data'], $listes_objets));
					}
				}				
			}
		}
	}
	
	return $flux;
}
