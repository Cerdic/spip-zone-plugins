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

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * chercher les tables SPIP qui gèrent ou non des rubriques
 * gestion des tables historiques également : annuaire de site et brèves activés ?
 * 
 * @param string $quoi
 *	oui : les tables qui gèrent des rubriques
 *	non : les tables qui ne gèrent pas de rubriques
 * @return array
 *	tableau des nom de tables SPIP à exclure (ex : spip_auteurs, spip_mots, etc.) ou à inclure 
 */
function rang_objets_gere_rubrique($quoi) {
	$tables = array();
	$liste = lister_tables_objets_sql();

	foreach ($liste as $key => $value) {
		if ($quoi == 'oui' AND $value['editable'] == 'oui' AND isset($value['field']['id_rubrique']))
			array_push($tables,$key);
		if ($quoi == 'non' AND $value['editable'] == 'oui' AND !isset($value['field']['id_rubrique']))
			array_push($tables,$key);
	}
	
	// Pour le moment, on ne gère pas ces objets à rubrique
	$liste_gere_rub_exclus = array(0=>'spip_rubriques', 1 => 'spip_breves', 2 => 'spip_syndic');
	if ($quoi == 'non') {
		$tables = array_merge($tables, $liste_gere_rub_exclus);
	}

	return $tables;
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
function rang_objet_select($id_mot, $id_objet, $objet){
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

function rang_tester_presence_numero($table) {
	return false;
}