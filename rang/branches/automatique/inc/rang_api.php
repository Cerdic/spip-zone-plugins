<?php
/**
 * Fonctions utiles au plugin Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Remplir ou ressortir les tables ayant déjà un rang
 * 
 * Au premier appel on fournit la liste complète de toutes les tables d'objets, pour faire la recherche.
 * Ensuite on peut appeler la fonction sans rien, et elle sortira la liste des tables qui ont un rang AVANT le plugin Rang.
 * 
 * @param array $tables
 * 		Le tableau complet de toutes les tables d'objets au premier appel
 **/
function rang_lister_tables_deja_rang($tables=null) {
	static $tables_deja_rang = null;
	
	// Si on n'a pas encore fait la recherche et qu'on a fourni la liste des tables d'objets
	if (is_null($tables_deja_rang) and is_array($tables)) {
		$tables_deja_rang = array();
		foreach ($tables as $table => $description) {
			if (isset($description['field']['rang'])) {
				$tables_deja_rang[] = $table;
			}
		}
	}
	
	return $tables_deja_rang;
}

/**
 * Construire la liste des objets à exclure de la configuration
 * 
 * @return array
 *	tableau des tables SPIP à exclure (ex : spip_auteurs, spip_mots, etc.)
 */
function rang_objets_a_exclure() {
	$exclus = array();

	// on exclu toujours les objets suivants
	$liste_toujours_exclus = array('spip_auteurs', 'spip_documents', 'spip_groupes_mots', 'spip_messages');
	$exclus = array_merge($exclus, $liste_toujours_exclus);
	
	// Pour le moment, on ne gère pas les rubriques elles-memes
	array_push($exclus, 'spip_rubriques');

	// et on ne gère pas les breves et sites
	array_push($exclus, 'spip_syndic');
	array_push($exclus, 'spip_breves');

	return $exclus;
}

/**
 * Créer les champs 'rang' sur les tables des objets reçus en paramètre
 * et initialiser la valeur du rang
 * TODO : à compléter !
 *
 * @param array $objets
 *     liste d'objets
 **/
function rang_creer_champs ($objets) {
	foreach ($objets as $table) {
		if (!empty($table)) {
			$champs_table = sql_showtable($table);
			
			// si le champ 'rang' n'existe pas, le créer et le remplir
			if (!isset($champs_table['field']['rang'])) {
				// créer le champ 'rang'
				sql_alter('TABLE ' . $table . ' ADD rang SMALLINT NOT NULL');

				// remplir #1 : si aucun numero_titre n'est trouvé, on met la valeur de l'id_prefixe dans rang
				if (!rang_tester_presence_numero($table)) {
					$id = id_table_objet($table);
					$desc = lister_tables_objets_sql($table);
					if (isset($desc['field']['id_rubrique'])) {
						$quelles_rubriques = sql_allfetsel('id_rubrique', $table, '', 'id_rubrique');

						foreach ($quelles_rubriques as $key => $value) {
							$id_rub =  $value['id_rubrique'];
							$quelles_items = sql_allfetsel($id, $table, 'id_rubrique='.$id_rub);

							$i = 1;
							foreach ($quelles_items as $key => $value) {
								$id_prefixe = $value[$id];
								sql_update($table, array( 'rang' => $i ), "$id = $id_prefixe");
								$i++;
							}
						}
					}
				}

				// remplir #2 sinon , recuperer le numero_titre et l'insérer dans rang
				// à faire !!
			}
		}
	}
}

/**
 * Trouver l'objet qui correspond au squelette de liste de l'espace privé passé en argument sur le même modele que trouver_objet_exec()
 * 
 * @see trouver_objet_exec
 * @param string $squelette
 * 		Chemin d'un squelette de liste de l'admin (prive/objets/liste/patates)
 * @return array
 * 		Retourne un tableau avec les infos de l'objet trouvé, sinon false
 **/
function rang_trouver_objet_liste($squelette) {
	static $objets_listes = array();
	
	if (!isset($objets_listes[$squelette])) {
		include_spip('base/objets');
		$objets_listes[$squelette] = false;
		
		if ($squelette and strpos($squelette, 'prive/objets/liste/') === 0) {
			$exceptions = pipeline(
				'rang_trouver_objet_liste',
				array(
					'prive/objets/liste/mots-admin' => 'mot',
				)
			);
			
			// Soit on trouve l'objet dans la liste explicite
			if (isset($exceptions[$squelette])) {
				$objet = $exceptions[$squelette];
			}
			else {
				$objet = str_replace('prive/objets/liste/', '', $squelette);
			}
			
			// Si on a un objet et qu'il est dans les objets connus de SPIP
			if (
				$objet
				and $table_objet_sql = table_objet_sql($objet)
				and $objets = lister_tables_objets_sql()
				and isset($objets[$table_objet_sql])
			) {
				$objets_listes[$squelette] = array(
					'objet' => objet_type($objet),
					'table_objet_sql' => $table_objet_sql,
					'table_objet' => table_objet($objet),
					'cle_objet' => id_table_objet($objet),
				);
			}
		}
	}
	
	return $objets_listes[$squelette];
}

/**
 * Construction, a partir des objets selectionnes, des chemins de sources vers les listes correspondantes
 * Ce tableau sera ensuite comparé à la valeur $flux['data']['source'] fourni par le pipeline recuperer_fond()
 *
 * @return array
 *     les chemins sources vers les listes où activer Rang
 **/
function rang_get_sources() {
	include_spip('inc/config');
	// mettre en cache le tableau calculé
	static $sources;
	if(is_array($sources)){
		return $sources;
	}
	
	$sources = array();
	$objets = lire_config('rang/objets');

	foreach ($objets as $value) {
		$objet = table_objet($value);
		if (!empty($value)) {
			$source = 'prive/objets/liste/'.$objet;
			$sources[] = $source;
		}

		// cas objets historiques
		if ($objet == 'mots') {
			$source = 'prive/objets/liste/mots-admin';
			$sources[] = $source;
		}
	}

	// tempo : test sur des liaisons
	$sources[] = 'prive/objets/liste/mots_lies';

	return $sources;
}

/**
 * Retourne la listes des pages (exec) sur lesquelles activer Rang.
 * On prend la liste des objets cochés dans la configuration en considérant que le nom de l'objet et de l'exec sont identiques.
 * Si ce n'est pas le cas, le pipeline rang_declarer_contexte permet d'ajouter un exec spécifique.
 * On ajoute aussi les cas particuliers historiques.
 *
 * @return array
 */
function rang_get_contextes() {
	static $contextes = null;
	
	if (!is_array($contextes)) {
		include_spip('base/objets_parents');
		
		$tables = lire_config('rang/objets');
		$contextes = array();
		
		foreach ($tables as $table) {
			// le nom de l'objet au pluriel
			$contextes[] = table_objet($table);
			// si l'objet a un parent, on ajoute le nom de cet objet
			$info_parent = type_objet_info_parent(objet_type($table));
			if (isset($info_parent['type']) and $info_parent['type']) {
				$contextes[] = $info_parent['type'];
			}
			if($table == 'spip_mots'){
				$contextes[] = 'groupe_mots';
			}
		}
		// vérifier si des plugins déclarent des contextes spécifiques
		$contextes = pipeline('rang_declarer_contexte', $contextes);
	}
	
	return $contextes;
}

/**
 * Calculer le rang pour la nouvelle occurence de l’objet
 * @param string $table
 * @param int $id_objet
 * @return int
 */
 function rang_classer_dernier($table, $id_objet) {

 	$objet_type = objet_type($table);
 	include_spip('base/objets_parents');

	// si l'objet à un parent…
	if ($parent = type_objet_info_parent($objet_type)) {
		$id_table_objet = id_table_objet($table);
		$parent_champ = $parent['0']['champ'];
		$id_parent = sql_getfetsel($parent_champ, $table, "$id_table_objet = $id_objet");
		$rang = sql_getfetsel('max(rang)', $table, "$parent_champ = $id_parent");
	} else {
	// si pas de parent, c'est plus simple
		$rang = sql_getfetsel('max(rang)', $table);
	}
	 
	// todo : on classe l'objet à la fin (rang max) mais on pourrait vouloir le classer au début
	// il faudrait donc une configuration pour ça, et dans ce cas reclasser tous les autres à un rang++
	$dernier = $rang+1;

	return $dernier;
 }

function rang_tester_presence_numero($table) {
	return false;
}

