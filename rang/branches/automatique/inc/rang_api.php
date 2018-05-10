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
	foreach ($objets as $key => $table) {
		if (!empty($table)) {
			// si le champ 'rang' n'existe pas, le créer et le remplir
			$champs_table = sql_showtable($table);
			if (!isset($champs_table['field']['rang'])) {

				// créer le champ 'rang'
				sql_alter('TABLE '.$table.' ADD rang SMALLINT NOT NULL');

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
	$objets_selectionnes = lire_config('rang/rang_objets');
	$objets=explode(',',$objets_selectionnes);

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
	static $contextes;
	if(is_array($contextes)){
		return $contextes;
	}
	include_spip('base/objets_parents');
	$tables = explode(',', lire_config('rang/rang_objets'));
	$contextes = array();
	foreach ($tables as $table) {
		// le nom de l'objet au pluriel
		$contextes[] = table_objet($table);
		// si l'objet a un parent, on ajoute le nom de cet objet
		$info_parent = type_objet_info_parent(objet_type($table));
		if (isset($info_parent['type']) && $info_parent['type']) {
			$contextes[] = $info_parent['type'];
		}
		if($table=='spip_mots'){
			$contextes[] = 'groupe_mots';
		}
	}
	// vérifier si des plugins déclarent des contextes spécifiques
	$contextes = pipeline('rang_declarer_contexte',$contextes);
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

