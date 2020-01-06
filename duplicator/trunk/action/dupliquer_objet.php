<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/objets');
include_spip('action/editer_objet');

/**
 * Point d'entrée pour dupliquer un objet
 *
 * On ne peut entrer que par un appel en fournissant $id et $objet
 * ou avec un argument d'action sécurisée de type "objet/id"
 *
 * @param string $objet
 * 		Type de l'objet à dupliquer
 * @param int $id
 * 		Identifiant de l'objet à dupliquer
 * @param array $modifications
 * 		Tableau de champ=>valeur avec les modifications à apporter sur le contenu dupliqué
 * @return array
 */
function action_dupliquer_objet_dist($objet = null, $id_objet = null) {
	// appel direct depuis une url avec arg = "objet/id/enfants"
	if (is_null($id_objet) or is_null($objet)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
		list($objet, $id_objet, $enfants) = array_pad(explode("/", $arg), 3, null);
	}
	
	if ($objet and $id_objet) {
		include_spip('inc/config');
		include_spip('base/objets');
		$config = lire_config('duplicator', array());
		$objet = objet_type($objet); // assurance
		$modifications = array();
		$options = array();
		
		// S'il y a des champs précis à dupliquer pour cet objet, on rajoute aux options
		if (isset($config[$objet]['champs']) and $champs = $config[$objet]['champs']) {
			$options['champs'] = $champs;
		}
		
		// S'il y a un statut forcé pour cet objet
		if (isset($config[$objet]['statut']) and $statut = $config[$objet]['statut']) {
			$modifications['statut'] = $statut;
		}
		
		// Si on demande à dupliquer aussi les enfants
		if ($enfants) {
			$options['dupliquer_enfants'] = true;
			
			// On cherche si seulement certains enfants sont acceptés à dupliquer pour cet objet
			if (isset($config[$objet]['enfants']) and $enfants = $config[$objet]['enfants']) {
				$options['enfants'] = array_map('objet_type', $config[$objet]['enfants']);
			}
			
			// Dans ce cas on passe aussi le tableau de toutes les options, avec "champs" et "enfants" qui seront pris en compte
			$options['options_objets'] = $config;
		}
		
		// Si on a réussi à dupliquer
		if ($id_objet_duplicata = intval(objet_dupliquer($objet, $id_objet, $modifications, $options))) {
			include_spip('inc/headers');
			
			// S'il y avait une demande de redirection
			if ($redirect = _request('redirect')) {
				redirige_par_entete(
					str_replace('&amp;', '&', $redirect)
				);
			}
			// Sinon on redirige sur la page de l'objet (TODO choix à configurer ?)
			else {
				redirige_par_entete(
					str_replace('&amp;', '&', generer_url_entite($id_objet_duplicata, $objet))
				);
			}
		}
	}
}

/**
 * Duplique un objet, ses liaisons et ses enfants
 * 
 * @param $objet
 * 		Type de l'objet à dupliquer
 * @param $id_objet 
 * 		Identifiant de l'objet à dupliquer
 * @param $modifications 
 * 		Tableau de champ=>valeur avec les modifications à apporter sur le contenu dupliqué
 * @param $options
 * 		Tableau d'options :
 * 		- champs : liste des champs à dupliquer, sinon * par défaut
 * 		- ajout_titre : ajouter une chaine à la fin du titre
 * 		- dupliquer_liens : booléen précisant si on duplique les liens ou pas, par défaut oui
 * 		- liens : liste d'objets liables dont on veut dupliquer les liens
 * 		- liens_exclus : liste d'objets liables dont on ne veut pas dupliquer les liens
 * 		- dupliquer_enfants : booléen précisant si on duplique les enfants ou pas, par défaut non
 * 		- enfants : liste d'objets d'enfants acceptés pour la duplication en cascade
 * 		- options_objets : tableau indexé par objet, avec pour chacun un tableau des options précédentes
 * 		  Cela permet de passer en cascade aux enfants certaines options qui ne sont pas forcément les mêmes que dans l'appel de départ
 * 		  'article' => array('champs'=>array(…), 'enfants'=>array(…))
 * @return int
 * 		Retourne l'identifiant du duplicata
 */
function objet_dupliquer($objet, $id_objet, $modifications=array(), $options=array()) {
	include_spip('inc/filtres');
	include_spip('action/editer_liens');
	$id_objet_duplicata = false;
	$cle_objet = id_table_objet($objet);
	$id_objet = intval($id_objet);
	
	// On cherche la liste des champs à dupliquer, par défaut tout
	if (isset($options['champs']) and is_array($options['champs'])) {
		$champs = $options['champs'];
		
		// On s'assure qu'il y a toujours le statut quand même
		if (
			$declaration_statut = objet_info($objet, 'statut')
			and isset($declaration_statut[0]['champ'])
			and $champ_statut = $declaration_statut[0]['champ']
		) {
			$champs[] = $champ_statut;
		}
	}
	else {
		$champs = '*';
	}
	
	// On récupère les infos à dupliquer
	$infos_a_dupliquer = sql_fetsel($champs, table_objet_sql($objet), "$cle_objet = $id_objet");
	// On retire la clé primaire
	unset($infos_a_dupliquer[$cle_objet]);
	
	// Si on a demandé à ajouter une chaine après le titre
	// TODO : on n'a toujours rien pour trouver uniquement le champ de titre SEUL
	if (isset($options['ajout_titre']) and isset($infos_a_dupliquer['titre'])) {
		$infos_a_dupliquer['titre'] .= $options['ajout_titre'];
	}
	
	// On applique des modifications s'il y en a
	if ($modifications and is_array($modifications)) {
		$infos_a_dupliquer = array_merge($infos_a_dupliquer, $modifications);
	}
	
	// On commence la duplication de l'objet lui-même
	$id_objet_duplicata = objet_inserer($objet, 0, $infos_a_dupliquer);
	
	// Si on a bien notre nouvel objet
	if ($id_objet_duplicata = intval($id_objet_duplicata)) {
		// Si on duplique bien les liens
		if (!isset($options['dupliquer_liens']) or $options['dupliquer_liens']) {
			// On cherche quels liens
			$liens = $liens_exclus = null;
			if (isset($options['liens']) and is_array($options['liens'])) {
				$liens = $options['liens'];
			}
			if (isset($options['liens_exclus']) and is_array($options['liens_exclus'])) {
				$liens_exclus = $options['liens_exclus'];
			}
			
			// On duplique les liens
			objet_dupliquer_liens($objet, $id_objet, $id_objet_duplicata, $liens, $liens_exclus);
			
			// Cas particulier de ces satanées rubriques poly qui ne suivent pas l'API des liens !
			if (test_plugin_actif('polyhier')) {
				include_spip('inc/polyhier');
				$id_parents = polyhier_get_parents($id_objet, $objet);
				polyhier_set_parents($id_objet_duplicata, $objet, $id_parents);
			}
		}
		
		// On duplique les logos
		logo_dupliquer($objet, $id_objet, $id_objet_duplicata, 'on');
		logo_dupliquer($objet, $id_objet, $id_objet_duplicata, 'off');
		
		// On continue de lancer l'ancien pipeline
		pipeline('duplicator', array(
			'objet' => $objet,
			'id_objet_origine' => $id_objet,
			'id_objet' => $id_objet_duplicata,
		));
		
		// On duplique peut-être aussi tous les enfants
		if (
			isset($options['dupliquer_enfants'])
			and $options['dupliquer_enfants']
			and include_spip('base/objets_parents')
			and $enfants_methodes = type_objet_info_enfants($objet)
			and $enfants = objet_trouver_enfants($objet, $id_objet)
			// S'il n'y a pas de config d'enfants alors tous, sinon seulement les enfants autorisés
			and (
				!isset($options['enfants'])
				or $options['enfants'] == 'tous'
				or (
					$enfants_autorises = ($options['enfants'] ? $options['enfants'] : array())
					and $enfants_autorises = array_flip(array_map('objet_type', $enfants_autorises))
					and $enfants = array_intersect_key($enfants, $enfants_autorises)
				)
			)
		) {
			// On parcourt tous les types d'enfants autorisés
			foreach ($enfants as $objet_enfant => $ids) {
				if (is_array($ids)) {
					foreach ($ids as $id_enfant) {
						$modifications_enfant = array();
						$options_enfant = $options;
						
						// On enlève des options qui n'ont pas à venir du parent de départ
						unset($options_enfant['champs']);
						unset($options_enfant['ajout_titre']);
						
						// S'il existe des options d'objets, on utilise
						if (isset($options['options_objets'][$objet_enfant])) {
							$options_enfant = array_merge($options_enfant, $options['options_objets'][$objet_enfant]);
						}
						
						// Les modifications nécessaires pour mettre le bon parent suivant la méthode
						if (isset($enfants_methodes[$objet_enfant]['champ'])) {
							$modifications_enfant[$enfants_methodes[$objet_enfant]['champ']] = $id_objet_duplicata;
						}
						if (isset($enfants_methodes[$objet_enfant]['champ_type'])) {
							$modifications_enfant[$enfants_methodes[$objet_enfant]['champ_type']] = $objet;
						}
						
						$id_enfant_duplicata = objet_dupliquer($objet_enfant, $id_enfant, $modifications_enfant, $options_enfant);
					}
				}
			}
		}
	}
	
	return $id_objet_duplicata;
}

if (!function_exists('logo_dupliquer')) {
/**
 * Dupliquer un logo entre deux contenus
 * 
 * Cette fonction est destinée à être remplacé par une plus moderne dans le plugin Rôles de documents, qui gère alors aussi les logos en documents.
 * 
 * @param $objet 
 * 		Type de l'objet dont on veut dupliquer le logo
 * @param $id_source 
 * 		Identifiant de l'objet dont on veut dupliquer le logo
 * @param $id_cible 
 * 		Identifiant de l'objet sur lequel mettre le logo dupliqué
 * @param $etat 
 * 		État du logo (on ou off)
 * @return
 * 		Retourne le chemin du nouveau logo si tout s'est déroulé correctement
 */
function logo_dupliquer($objet, $id_source, $id_cible, $etat='on') {
	include_spip('action/editer_logo');
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$cle_objet = id_table_objet($objet);
	
	// Si on trouve le logo pour la source
	if ($logo_source = $chercher_logo($id_source, $cle_objet, $etat)) {
		return logo_modifier($objet, $id_cible, $etat, $logo_source[0]);
	}
	
	return false;
}
}
