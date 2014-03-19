<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// Liste des zones a laquelle appartient le visiteur, au format '1,2,3'
// Cette fonction est appelee a chaque hit et peut etre completee (pipeline)
/**
 * Liste des zones a laquelle appartient le visiteur, au format '1,2,3'.
 * Cette fonction est appelee a chaque hit et peut etre completee (pipeline)
 *
 * @param string $zones '1,2,3'
 * @param int $id_auteur
 * @return string '1,2,3'
 */
function accesrestreint_liste_zones_autorisees($zones='', $id_auteur=NULL) {
	$id = NULL;
	
	if (!is_null($id_auteur)) {
		$id = $id_auteur;
	}
	elseif (isset($GLOBALS['visiteur_session']['id_auteur']) && $GLOBALS['visiteur_session']['id_auteur']) {
		$id = $GLOBALS['visiteur_session']['id_auteur'];
	}
	
	if (!is_null($id)) {
		$new = accesrestreint_liste_zones_appartenance_auteur($id);
		if ($zones AND $new) {
			$zones = array_unique(array_merge(explode(',', $zones), $new));
			sort($zones);
			$zones = join(',', $zones);
		} else if ($new) {
			$zones = join(',', $new);
		}
	}
	
	return $zones;
}

/**
 * Liste des rubriques contenues dans une zone ou plusieurs zones, directement.
 * pour savoir quelles rubriques on peut decocher
 * si id_zone = '' : toutes les rub en acces restreint
 *
 * @param int|string|array $id_zone
 *		Peut être un identifiant de zone OU un where sql OU un tableau de where
 * @return array
 */
function accesrestreint_liste_contenu_zone_rub_direct($id_zone_ou_where) {
	include_spip('base/abstract_sql');
	$liste_rubriques=array();
	
	// Liste des rubriques directement liees a la zone
	$where = array();
	if (is_numeric($id_zone_ou_where)) {
		$where[] = "z.id_zone=".intval($id_zone_ou_where);
	}
	elseif ($id_zone_ou_where) {
		$where = $id_zone_ou_where;
	}

	if (is_array($where)) {
		$where[] = "zr.objet='rubrique'";
	}
	else {
		$where = "($where) AND zr.objet='rubrique'";
	}

	$liste_rubriques = sql_allfetsel('id_objet', 'spip_zones_liens AS zr INNER JOIN spip_zones AS z ON zr.id_zone=z.id_zone', $where);
	$liste_rubriques = array_map('reset', $liste_rubriques);
	$liste_rubriques = array_unique($liste_rubriques);
	
	return $liste_rubriques;
}

/**
 * liste des rubriques contenues dans une zone, directement ou par heritage.
 *
 * @use calcul_branche_in
 * @param int/string $id_zone
 * @return array
 */
function accesrestreint_liste_contenu_zone_rub($id_zone) {
	include_spip('inc/rubriques');
	
	$liste_rubriques = accesrestreint_liste_contenu_zone_rub_direct($id_zone);
	if (!count($liste_rubriques)) {
		return $liste_rubriques;
	}
	
	// On va cherche les rubriques de toute les branches, avec la fonction du noyau
	$liste_rubriques = calcul_branche_in(join(',', $liste_rubriques));
	
	if (!strlen($liste_rubriques)) {
		return array();
	}
	
	// On remet sous forme de tableau
	$liste_rubriques = explode(',', $liste_rubriques);
	
	return $liste_rubriques;
}


/**
 * liste des rubriques d'une zone et leurs rubriques parentes.
 *
 * @param int/string $id_zone
 * @return array
 */
function accesrestreint_liste_parentee_zone_rub($id_zone) {
	include_spip('inc/rubriques');
	
	$liste_rubriques = accesrestreint_liste_contenu_zone_rub_direct($id_zone);
	if (!count($liste_rubriques)) {
		return $liste_rubriques;
	}

	$id = $liste_rubriques;
	while (
		$parents = sql_allfetsel('id_parent', 'spip_rubriques', sql_in('id_rubrique', $id))
	) {
		$parents = array_map('array_shift', $parents);
		$parents = array_diff($parents, array(0));
		$id = $parents;
		$liste_rubriques = array_merge($liste_rubriques, $parents);
	}
	
	return $liste_rubriques;
}

/**
 * Lister les zones auxquelles un auteur appartient
 *
 * @param int $id_auteur
 * @return array
 */
function accesrestreint_liste_zones_appartenance_auteur($id_auteur) {
	static $liste_zones = array();
	
	if (!isset($liste_zones[$id_auteur])) {
		include_spip('base/abstract_sql');
		
		$liste_zones[$id_auteur] = sql_allfetsel("id_zone","spip_zones_liens","objet='auteur' AND id_objet=".intval($id_auteur));
		$liste_zones[$id_auteur] = array_map('reset',$liste_zones[$id_auteur]);
	}
	
	return $liste_zones[$id_auteur];
}

/**
 * Verifier si un auteur appartient a une zone.
 * utilise la fonction precedente qui met en cache son resultat
 * on optimise en fonction de l'hypothese que le nombre de zones est toujours reduit
 *
 * @param unknown_type $id_zone
 * @param unknown_type $id_auteur
 * @return unknown
 */
function accesrestreint_test_appartenance_zone_auteur($id_zone, $id_auteur) {
	return in_array($id_zone,accesrestreint_liste_zones_appartenance_auteur($id_auteur));
}

/**
 * liste des auteurs contenus dans une zone
 *
 * @param int $id_zone
 * @return array
 */
function accesrestreint_liste_contenu_zone_auteur($id_zone) {
	include_spip('base/abstract_sql');
	$liste_auteurs=array();
	
	$liste_auteurs = sql_allfetsel("id_objet", "spip_zones_liens", "objet='auteur' AND id_zone=".intval($id_zone));
	$liste_auteurs = array_map('reset', $liste_auteurs);
	
	return $liste_auteurs;
}

/**
 * fonctions de filtrage rubrique
 * -> condition NOT IN
 * Cette fonction renvoie la liste des rubriques interdites
 * au visiteur courant
 * d'ou le recours a $GLOBALS['accesrestreint_zones_autorisees']
 *
 * @param bool $publique Selectionner les rubriques interdites dans l'espace public (true) ou prive (false)
 * @param int $id_auteur Identifiant de l'auteur
 * @param bool $quelquesoit_visibilite Si true, on ne s'occupe pas de savoir si une zone est restreinte sur le prive ou sur le public.
 * @return array
 */
function accesrestreint_liste_rubriques_exclues($publique=true, $id_auteur=NULL, $quelquesoit_visibilite = false) {
	// cache static
	static $liste_rub_exclues = array();
	static $liste_rub_inclues = array();
	if ($quelquesoit_visibilite) { $publique = 'tout'; }

	// Si pas d'auteur, on prend le visiteur en cours si connecté
	if (is_null($id_auteur) AND isset($GLOBALS['visiteur_session']['id_auteur'])) {
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	}
	
	if (!isset($liste_rub_exclues[$id_auteur][$publique]) or !is_array($liste_rub_exclues[$id_auteur][$publique])) {
		$where = array();
		
		// Ne selectionner que les zones pertinentes
		if (!$quelquesoit_visibilite) {
			if ($publique)
				$where[] = "publique='oui'";
			else
				$where[] = "privee='oui'";
		}

		// Si le visiteur est autorise sur certaines zones publiques,
		// on selectionne les rubriques correspondant aux autres zones,
		// sinon on selectionne toutes celles correspondant a une zone.
		include_spip('base/abstract_sql');
		if (
			$GLOBALS['accesrestreint_zones_autorisees']
			and $id_auteur==$GLOBALS['visiteur_session']['id_auteur']
		) {
			$where[] = sql_in('zr.id_zone',$GLOBALS['accesrestreint_zones_autorisees'],'NOT');
		}
		elseif ($id_auteur) {
			$where[] = sql_in('zr.id_zone',accesrestreint_liste_zones_autorisees('',$id_auteur),'NOT');
		}

		// liste les rubriques (+branches) des zones dont ne fait pas parti l'auteur
		$liste_rub_exclues[$id_auteur][$publique] = accesrestreint_liste_contenu_zone_rub($where);
		#$liste_rub_exclues[$publique] = array_unique($liste_rub_exclues[$publique]);
	}
	
	// On stocke la liste finale qui pourra être modifiée suivant l'option ci-dessous
	$final_liste_rub_exclues = $liste_rub_exclues[$id_auteur][$publique];
	
	// AR_TYPE_RESTRICTION définit le type de réstriction pour traiter les élements communs à plusieurs zone
	//
	// - Une restriction exclusive (ou forte) donne l'acces aux rubriques restreintes par 
	//   plusieurs zones aux seuls membres de *toutes* les zones concernees.
	// - Une restriction faible donne acces à une rubrique, même restreinte par
	//   plusieurs zones, aux membres de chaque zone concernée.
	// - Valeurs possibles : 'faible', 'forte, ou 'exclusive'		
	//
	// Autrement dit, si une rubrique 2 est enfant d'une rubrique 1,
	// et qu'il existe une zone 1 (rubrique 1) et une zone 2 (rubrique 2) :
	// - un auteur present dans la zone 1 (uniquement) ne pourra pas voir la rubrique 2
	//   lorsque la restriction est "forte". Il le pourra avec une restriction "faible"
	//
	// - A l'inverse, un auteur present uniquement dans la zone 2 ne pourra pas voir
	//   la rubrique 1 meme si la restriction est "faible" car la parentee n'est pas concernee.
	//   il faut (si souhaite) dans ce cas definir en plus AR_TYPE_RESTRICTION_PARENTEE a "faible"
	//   pour l'autoriser.
	if (defined("AR_TYPE_RESTRICTION") AND AR_TYPE_RESTRICTION == "faible") {
		// Ne faire la recherche que si la variable statique n'est pas déjà définie
		if (!isset($liste_rub_inclues[$id_auteur][$publique]) or !is_array($liste_rub_inclues[$id_auteur][$publique])) {
			$where = array();
			// Ne selectionner que les zones pertinentes
			if (!$quelquesoit_visibilite) {
				if ($publique)
					$where[] = "publique='oui'";
				else
					$where[] = "privee='oui'";
			}

			// Calcul des rubriques dans des zones autorisees
			include_spip('base/abstract_sql');
			if (
				$GLOBALS['accesrestreint_zones_autorisees']
				and $id_auteur==$GLOBALS['visiteur_session']['id_auteur']
			){
				$where[] = sql_in('zr.id_zone', $GLOBALS['accesrestreint_zones_autorisees']);
			}
			elseif ($id_auteur) {
				$where[] = sql_in('zr.id_zone', accesrestreint_liste_zones_autorisees('',$id_auteur));
			}

			// liste les rubriques (+branches) des zones de l'auteur
			$liste_rub_inclues[$id_auteur][$publique] = accesrestreint_liste_contenu_zone_rub($where);

			// pour autoriser la vue des rubriques parentes
			// memes si elles sont restreintes par une autre zone
			if (defined("AR_TYPE_RESTRICTION_PARENTEE") AND AR_TYPE_RESTRICTION_PARENTEE == "faible") {
				$liste_rub_inclues[$id_auteur][$publique] = array_merge(
					$liste_rub_inclues[$id_auteur][$publique],
					accesrestreint_liste_parentee_zone_rub($where)
				);
			}
		}

		// Ne pas exclure les elements qui sont autorises
		$final_liste_rub_exclues = array_diff(
			$final_liste_rub_exclues,
			array_intersect($final_liste_rub_exclues, $liste_rub_inclues[$id_auteur][$publique])
		);
	}

	return $final_liste_rub_exclues;
}

