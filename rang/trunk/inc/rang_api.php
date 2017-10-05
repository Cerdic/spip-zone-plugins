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
 * Construire la liste des objets à exclure
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
 * Tester si un mot-clé est lié
 *
 * @param array $tab
 *     tableau des items de la liste suite à une modification du classement
 * @param string $objet
 *     sur quel objet faire le classement
 * @param string $rubrique optionel
 *     dans quelle rubrique faire le classement
 * @return array
 *     note : sans ce return (a priori inutile, la fonction plante (???)
**/
function rang_objet_select($id_mot, $id_objet, $objet) {
	$id_mot = intval($id_mot);
	$id_objet = intval($id_objet);
	$res = sql_countsel('spip_mots_liens', array("id_mot=$id_mot", "id_objet=$id_objet", "objet=$objet"));
	if ($res > 0 ) return true;
	else return false;
}

/**
 * Trier les rangs de la liste suite à un nouveau classement
 *
 * @param array $tab
 *     tableau des items de la liste suite à une modification du classement
 * @param string $objet
 *     sur quel objet faire le classement
 * @param string $rubrique optionel
 *     dans quelle rubrique faire le classement
 * @return array
 *     note : sans ce return (a priori inutile, la fonction plante (???)
**/
// function rang_trier_items($tab, $objet=null, $id_rubrique=null){

// 	exit();
	
// 	foreach ($tab as $key => $value) {
// 		$rang = $key + 1; //le classement commence à 1, pas à 0
// 		$id = intval(substr($value, 7));
// 		sql_updateq('spip_mots', array('rang' => $rang), "id_mot=$id");
// 	}
// 	return $tab;
// }

/**
 * supprimer un mot, puis réordonner
 *
 * @param string $id
 *     id de la forme mot_xx
 * @return array
 *     note : sans ce return (a priori inutile, la fonction plante (???)
**/
// function rang_supprimer_item($id){
// 	// suppression du mot
// 	$new_id = intval(substr($id, 4));
// 	sql_delete('spip_mots', "id_mot=$new_id");

// 	//récupérer le tableau id/rang actuel
// 	$id_grp = sql_getfetsel('id_groupe', 'spip_groupes_mots', 'titre='.sql_quote('Actualités'));
// 	$res = sql_select('id_mot', 'spip_mots', "id_groupe=$id_grp", '', 'rang');
// 	while ($tab = sql_fetch($res)){
// 		$new_tab[] = 'id_mot_'.$tab['id_mot'];
// 	}

// 	//  réordonnement !
// 	$tab_result = rang_trier_items($new_tab);

// 	return $tab_result;
// }

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
 * construction des chemins de sources vers les listes des objets sélectionnés
 * ce tableau sera ensuite comparé à la valeur $flux['data']['source'] fourni par le pipeline recuperer_fond()
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
			$source = find_in_path('prive/objets/liste/'.$objet.'.html');
			$sources[] = $source;
		}

		// cas objets historiques
		if($objet == 'mots') {
			$source = find_in_path('prive/objets/liste/mots-admin.html');
			$sources[] = $source;
		}
	}
	return $sources;
}

/**
 * Classer l'objet à la fin de la liste quand on le publie 
 * @param string $table
 * @param int $id_objet
 * @return int
 */
 function rang_classer_dernier($table, $id_objet) {

 	// quel objet pour quel parent ?
	$definition_table	= lister_tables_objets_sql($table);
	$id_table_objet		= id_table_objet($table);
	$objet_parent 		= $definition_table['parent']['type'];
	$id_objet_parent	= $definition_table['parent']['champ'];

	// et hop, on place le nouvel objet publié à la fin
	if($id_objet_parent) {
		$id_parent = sql_getfetsel($id_objet_parent, $table, "$id_table_objet = $id_objet");
		$rang = sql_getfetsel('max(rang)', $table, "$id_objet_parent = $id_parent");
	} else {
		$rang = sql_getfetsel('max(rang)', $table);
	}
	 
	// todo : on classe l'article à la fin (rang max) mais on pourrait vouloir le classer au début
	// il faudrait donc une configuration pour ça, et dans ce cas reclasser tous les autres à un rang++
	$dernier = $rang+1;

	return $dernier;
 }

function rang_tester_presence_numero($table) {
	return false;
}

