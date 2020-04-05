<?php
#---------------------------------------------------#
#  Plugin  : E-Learning                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : https://contrib.spip.net/Plugin-E-learning  #
#-----------------------------------------------------------------#

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/abstract_sql');
include_spip('inc/config');
include_spip('action/editer_liens');

// Met à jour les accès restreints lorsque l'on modifie la configuration du plugin E-Learning
function elearning_mettre_a_jour_les_zones($id_nouvelle_rubrique){
	$id_nouvelle_rubrique = intval($id_nouvelle_rubrique);
	$id_zone = elearning_verifier_la_zone();
	
	// Si la nouvelle rubrique n'était pas dans la zone
	// ou si la nouvelle rubrique est différente de celle de la config
	if (
		!elearning_verifier_le_lien($id_nouvelle_rubrique, $id_zone)
		or $id_nouvelle_rubrique != intval(lire_config('elearning/rubrique_elearning'))
	) {
		// On nettoie les anciennes zones
		elearning_nettoyer_les_zones_modules();
		
		// On peut maintenant définir la nouvelle rubrique comme celle du e-learning
		$ok = elearning_definir_la_rubrique_elearning($id_nouvelle_rubrique);
	}
	
	return _T('elearning:configuration_ok');
}


// Vérifie qu'il y a une zone dédiée au e-learning et la crée si nécessaire
// Retourne l'id_zone
function elearning_verifier_la_zone() {
	// On vérifie s'il y a une zone déjà enregistrée
	$id_zone = intval(lire_config('elearning/zone_elearning'));
	
	// Si oui, on vérifie qu'elle existe toujours
	if ($id_zone) {
		$id_zone = intval(sql_getfetsel(
			'id_zone',
			'spip_zones',
			'id_zone = '.$id_zone
		));
	}
	
	// Si elle n'existe pas, on la crée
	if (!$id_zone) {
		$id_zone = sql_insertq(
			'spip_zones',
			array(
				'titre' => 'Zone de la formation',
				'descriptif' => 'Cette zone contient la rubrique-mère de la formation.',
				'publique' => 'oui',
				'privee' => 'oui'
			)
		);
		ecrire_config('elearning/zone_elearning', $id_zone);
	}
	
	return $id_zone;
}


// Vérifie si une rubrique est dans une zone
// dont on considère qu'elle ne doit contenir qu'une rubrique
// Retourne si vrai ou pas
function elearning_verifier_le_lien($id_nouvelle_rubrique, $id_zone) {
	// On vérifie si la rubrique choisie est déjà dans la bonne zone
	$id_rubrique_test = intval(sql_getfetsel(
		'id_objet',
		'spip_zones_liens',
		array(
			'objet = "rubrique"',
			'id_zone = '.intval($id_zone)
		)
	));
	
	if (!$id_rubrique_test or $id_rubrique_test != $id_nouvelle_rubrique) {
		return false;
	}
	else {
		return true;
	}
}

// Définit une rubrique comme étant celle du e-learning :
// - lie la rubrique à sa zone dédiée
// - crée une zone pour chaque sous-rubrique c-à-d chaque module
// - lie chaque module à sa zone
function elearning_definir_la_rubrique_elearning($id_rubrique) {
	include_spip('inc/filtres');
	
	// On vérifie le paramètre
	$id_rubrique = intval($id_rubrique);
	if (!$id_rubrique or $id_rubrique <= 0) {
		return false;
	}
	
	// On récupère la zone e-learning
	$id_zone = intval(lire_config('elearning/zone_elearning'));
	
	// On lie la rubrique à sa zone
	objet_associer(array('zone'=>$id_zone), array('rubrique'=>$id_rubrique));
	
	// On récupère les rubriques de modules
	$modules = sql_select(
		array('id_rubrique', 'titre'),
		'spip_rubriques',
		array(
			'id_parent = '.$id_rubrique
		)
	);
	
	// On crée une zone pour chaque module et on les lie ensemble
	while ($module = sql_fetch($modules)){
		// Création de la zone
		$id_zone_module = sql_insertq(
			'spip_zones',
			array(
				'titre' => 'Restriction du module "'.supprimer_numero($module['titre']).'"',
				'publique' => 'oui',
				'privee' => 'oui'
			)
		);
	
		// Création du lien si ça a marché
		if ($id_zone_module) {
			objet_associer(array('zone'=>$id_zone_module), array('rubrique'=>intval($module['id_rubrique'])));
		}
		
		// Enregistrement de la zone dans la config de la rubrique, pour accès rapide
		ecrire_config('elearning/modules/'.intval($module['id_rubrique']).'/zone', $id_zone_module);
	}
	
	return true;
}


// Nettoie les zones de modules créées automatiquement par le plugin, ainsi que tous les liens qui vont avec
function elearning_nettoyer_les_zones_modules() {
	// On récupère la zone contenant le e-learning
	$id_zone = intval(lire_config('elearning/zone_elearning'));
	
	// On récupère l'ancienne rubrique choisie pour le e-learning
	$id_rubrique = intval(lire_config('elearning/rubrique_elearning'));
	
	// On supprime le lien entre les deux
	objet_dissocier(array('zone'=>$id_zone), array('rubrique'=>$id_rubrique));
	
	// On récupère les anciennes rubriques de modules
	$modules = sql_allfetsel(
		'id_rubrique',
		'spip_rubriques',
		array(
			'id_parent = '.$id_rubrique
		)
	);
	$modules = array_map('reset', $modules);
	
	// Seulement s'il y a bien des modules
	if (count($modules) > 0){
		// On récupère toutes les zones afférentes
		$zones_modules = sql_allfetsel(
			'distinct id_zone',
			'spip_zones_liens',
			array(
				'objet = "rubrique"',
				sql_in('id_objet', $modules),
			)
		);
		$zones_modules = array_map('reset', $zones_modules);
	
		// Seulement s'il y a des zones
		if (count($zones_modules) > 0){
			// On supprime toutes ces zones
			sql_delete(
				'spip_zones',
				'id_zone IN ('.join(', ', $zones_modules).')'
			);
	
			// On supprime les liens de ces zones
			objet_dissocier(array('zone'=>$zones_modules), array('*'=>'*'));
		}
	}
}


// Pipeline des zones autorisées, renvoie une liste à virgule
function elearning_liste_zones_autorisees($zones='', $id_auteur=NULL) {
	$id = NULL;
	if (!is_null($id_auteur)) {
		$id = $id_auteur;
	}
	
	$new = elearning_liste_zones_autorisees_auteur($id);
	if ($zones AND $new) {
		$zones = array_unique(array_merge(explode(',',$zones),$new));
		sort($zones);
		$zones = join(',', $zones);
	} else if ($new) {
		$zones = join(',', $new);
	}
	
	return $zones;
}


// Renvoie un tableau listant les zones de module autorisées pour un auteur
function elearning_liste_zones_autorisees_auteur($id_auteur=null) {
	include_spip('base/abstract_sql');
	
	// On va remplir petit à petit ce tableau
	$zones_modules = array();
	
	// On récupère la rubrique e-learning
	$id_rubrique = intval(lire_config('elearning/rubrique_elearning'));
	
	// On récupère tous les modules
	$ids_modules = sql_allfetsel(
		'id_rubrique',
		'spip_rubriques',
		array(
			'id_parent = '.$id_rubrique
		)
	);
	$ids_modules = array_map('reset', $ids_modules);
	
	// Si $id_auteur est nul, l'auteur est celui de la session, sinon on récupère dans la base
	if (is_null($id_auteur) or $id_auteur <= 0)
		$auteur = $GLOBALS['visiteur_session'];
	else
		$auteur = sql_fetsel(
			'*',
			'spip_auteurs',
			array(
				'id_auteur = '.$id_auteur
			)
		);
	
	// Si la personne est au moins rédacteur elle accède à tous les modules
	if ($auteur['statut'] <= '1comite'){
		// On récupère les zones de chaque modules
		foreach ($ids_modules as $id_module){
			$id_module = intval($id_module);
			$zones_modules[] = intval(lire_config('elearning/modules/'.$id_module.'/zone'));
		}
	}
	// Sinon on teste les résultats des jeux associés aux rubriques
	else{
		foreach ($ids_modules as $id_module){
			$id_module = intval($id_module);
			$id_jeu = intval(lire_config('elearning/modules/'.$id_module.'/jeu'));
			$score_a_atteindre = intval(lire_config('elearning/modules/'.$id_module.'/score'));
			$id_zone = intval(lire_config('elearning/modules/'.$id_module.'/zone'));
			
			// Si ya pas de jeu ou un score de 0, on ne teste rien et on autorise
			if (!$id_jeu or !$score_a_atteindre) {
				$zones_modules[] = $id_zone;
			}
			else{
				// On récupère le dernier résultat de jeu de la personne
				$resultat = sql_fetsel(
					array(
						'resultat_court',
						'total'
					),
					'spip_jeux_resultats',
					array(
						'id_jeu = '.$id_jeu,
						'id_auteur = '.$auteur['id_auteur']
					),
					'',
					'date desc'
				);
				if ($resultat) {
					$score = 100 * $resultat['resultat_court'] / $resultat['total'];
				}
				else {
					$score = 0;
				}
				
				// Si le score est bon, on autorise
				if ($score >= $score_a_atteindre) {
					$zones_modules[] = $id_zone;
				}
			}
		}
	}
	
	return $zones_modules;
}
