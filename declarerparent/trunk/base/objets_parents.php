<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette API a vocation à migrer dans ce fichier à terme
include_spip('base/objets');

/**
 * Donne les informations de parenté directe d'un type d'objet si on en trouve
 * 
 * @param $objet
 * 		Type de l'objet dont on cherche les informations de parent
 * @return array
 * 		Retourne un tableau contenant les informations de type et de champ pour trouver le parent ou false sinon
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
			if (isset($infos['parent'])) {
				$parents[$objet] = $infos['parent'];
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

/**
 * Cherche le contenu parent d'un contenu précis
 * 
 * @param $objet
 * 		Type de l'objet dont on cherche le parent
 * @param $id_objet
 * 		Identifiant de l'objet dont on cherche le parent
 * @return
 * 	Retourne un tableau avec la clé "objet" et la clé "id_objet" décrivant le parent trouvé, ou false sinon
 * 
 */
function objet_trouver_parent($objet, $id_objet) {
	
}

/**
 * Cherche tous les contenus enfants d'un contenu précis
 * 
 * @param $objet
 * 		Type de l'objet dont on cherche les enfants
 * @param $id_objet
 * 		Identifiant de l'objet dont on cherche les enfants
 * @return
 * 	Retourne un tableau de tableaux, avec comme clés les types des objets, et dans chacun un tableau des identifiants trouvés
 * 
 */
function objet_trouver_enfants($objet, $id_objet) {
	
}
