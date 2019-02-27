<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette API a vocation à migrer dans ce fichier à terme
include_spip('base/objets');

/**
 * Cherche le contenu parent d'un contenu précis
 * 
 * @api
 * @param $objet
 * 		Type de l'objet dont on cherche le parent
 * @param $id_objet
 * 		Identifiant de l'objet dont on cherche le parent
 * @return array|false
 * 	Retourne un tableau avec la clé "objet" et la clé "id_objet" décrivant le parent trouvé, ou false sinon
 * 
 */
function objet_trouver_parent($objet, $id_objet) {
	$parent = false;
	
	// Si on trouve une ou des méthodes de parent
	if ($parent_methodes = type_objet_info_parent($objet)) {

		include_spip('base/abstract_sql');
		$table = table_objet_sql($objet);
		$cle_objet = id_table_objet($objet);
		$id_objet = intval($id_objet);


		// On teste chacun méthode dans l'ordre, et dès qu'on a trouvé un parent on s'arrête
		foreach ($parent_methodes as $parent_methode) {
			$where = array("$cle_objet = $id_objet");
			
			if (isset($parent_methode['condition'])) {
				$where[] = $parent_methode['condition'];
			}
			
			$select = array();
			if (isset($parent_methode['champ'])) {
				$select[] = $parent_methode['champ'];
			}
			if (isset($parent_methode['champ_type'])) {
				$select[] = $parent_methode['champ_type'];
			}

			// On lance la requête
			if ($ligne = sql_fetsel($select, $table, $where)) {
				
				// Si le type est fixe
				if (isset($parent_methode['type'])) {
					return array(
						'objet' 	=> $parent_methode['type'],
						'id_objet'	=> intval($ligne[$parent_methode['champ']]),
					);
				}
				elseif (isset($parent_methode['champ_type'])) {
					return array(
						'objet' 	 => $ligne[$parent_methode['champ_type']],
						'id_objet' 	 => intval($ligne[$parent_methode['champ']]),
					);
				}
			}
		}
	}
	
	// On passe par un pipeline avant de retourner
	$parent = pipeline(
		'objet_trouver_parent',
		array(
			'args' => array(
				'objet' => $objet,
				'id_objet' => $id_objet,
			),
			'data' => $parent,
		)
	);
	
	return $parent;
}

/**
 * Cherche tous les contenus enfants d'un contenu précis
 * 
 * @api
 * @param $objet
 * 		Type de l'objet dont on cherche les enfants
 * @param $id_objet
 * 		Identifiant de l'objet dont on cherche les enfants
 * @return
 * 	Retourne un tableau de tableaux, avec comme clés les types des objets, et dans chacun un tableau des identifiants trouvés
 * 
 */
function objet_trouver_enfants($objet, $id_objet) {
	$enfants = array();
	
	// Si on trouve des types d'enfants et leurs méthodes
	if ($enfants_methodes = type_objet_info_enfants($objet)) {
		include_spip('base/abstract_sql');
		$id_objet = intval($id_objet);
		
		// On parcourt tous les types d'enfants trouvés
		foreach ($enfants_methodes as $objet_enfant => $methode) {
			$table_enfant = table_objet_sql($objet_enfant);
			$cle_objet_enfant = id_table_objet($objet_enfant);
			
			$where = array();
			// L'identifiant du parent
			if (isset($methode['champ'])) {
				$where[] = $methode['champ'] . ' = ' . $id_objet;
			}
			// Si le parent est variable
			if (isset($methode['champ_type'])) {
				$where[] = $methode['champ_type'] . ' = ' . sql_quote($objet);
			}
			// S'il y a une condition supplémentaire
			if (isset($methode['condition'])) {
				$where[] = $methode['condition'];
			}
			
			// On lance la requête
			if ($ids = sql_allfetsel($cle_objet_enfant, $table_enfant, $where)) {
				$ids = array_map('reset', $ids);
				$enfants[$objet_enfant] = $ids;
			}
		}
	}
	
	// On passe par un pipeline avant de retourner
	$enfants = pipeline(
		'objet_trouver_enfants',
		array(
			'args' => array(
				'objet' => $objet,
				'id_objet' => $id_objet,
			),
			'data' => $enfants,
		)
	);
	
	return $enfants;
}

/**
 * Donne les informations de parenté directe d'un type d'objet si on en trouve
 * 
 * @param $objet
 * 		Type de l'objet dont on cherche les informations de parent
 * @return array
 * 		Retourne un tableau de tableau contenant les informations de type et de champ pour trouver le parent ou false sinon
 * 
 */
function type_objet_info_parent($objet) {
	static $parents = array();
	
	// Si on ne l'a pas encore cherché pour cet objet
	if (!isset($parents[$objet])) {
		$parents[$objet] = false;
		$table = table_objet_sql($objet);
		
		// Si on trouve bien la description de cet objet
		if ($infos = lister_tables_objets_sql($table)) {
			// S'il y a une description explicite de parent, c'est prioritaire
			if (isset($infos['parent']) and is_array($infos['parent'])) {
				if (count($infos['parent']) === count($infos['parent'], COUNT_RECURSIVE)) {
					$parents[$objet] = array($infos['parent']);
				} else {
					$parents[$objet] = $infos['parent'];
				}
			}
			// Sinon on cherche des cas courants connus magiquement, à commencer par id_rubrique
			elseif (isset($infos['field']['id_rubrique'])) {
				$parents[$objet] = array(array('type' => 'rubrique', 'champ' => 'id_rubrique'));
			}
			// Sinon on cherche un champ id_parent, ce qui signifie que l'objet est parent de lui-même
			elseif (isset($infos['field']['id_parent'])) {
				$parents[$objet] = array(array('type' => $objet, 'champ' => 'id_parent'));
			}
			//~ // Sinon on cherche s'il y a objet et id_objet dans la table, ce qui signifie que le parent peut-être n'importe quel objet
			//~ // comme c'est le cas pour les forums de premier niveau
			//~ elseif (isset($infos['field']['objet']) and isset($infos['field']['id_objet'])) {
				//~ $parents[$objet] = array(array('champ_type' => 'objet', 'champ' => 'id_objet'));
			//~ }
		}
	}
	
	return $parents[$objet];
}

/**
 * Donne les informations des enfants directs d'un type d'objet si on en trouve
 * 
 * @param $objet
 * 		Type de l'objet dont on cherche les informations des enfants
 * @return array
 * 		Retourne un tableau de tableaux contenant chacun les informations d'un type d'enfant
 * 
 */
function type_objet_info_enfants($objet) {
	static $enfants = array();
	
	// Si on a déjà fait la recherche pour ce type d'objet
	if (!isset($enfants[$objet])) {
		$enfants[$objet] = array();
		$tables = lister_tables_objets_sql();
		
		// On parcourt toutes les tables d'objet, et on cherche si chacune peut être enfant
		foreach ($tables as $table => $infos) {
			$objet_enfant = objet_type($table);
			
			// On ne va pas refaire les tests des différents cas, on réutilise
			if ($parent_methodes = type_objet_info_parent($objet_enfant)) {
				// On parcourt les différents cas possible, si certains peuvent concerner l'objet demandé
				foreach ($parent_methodes as $parent_methode) {
					// Si la méthode qu'on teste n'exclut pas le parent demandé
					if (!isset($parent_methode['exclus']) or !in_array($objet, $parent_methode['exclus'])) {
						// Si le type du parent est fixe et directement l'objet demandé
						if (isset($parent_methode['type']) and isset($parent_methode['champ']) and $parent_methode['type'] == $objet) {
							$enfants[$objet][$objet_enfant] = $parent_methode;
						}
						// Si le type est variable, alors l'objet demandé peut forcément être parent
						elseif (isset($parent_methode['champ_type']) and isset($parent_methode['champ'])) {
							$enfants[$objet][$objet_enfant] = $parent_methode;
						}
					}
				}
			}
		}
	}
	
	return $enfants[$objet];
}
