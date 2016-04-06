<?php
/**
 * fonctions utiles au plugin Identifiants
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     Tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Retourne l'identifiant d'un objet
 *
 * On cherche dans la table de l'objet si une colonne `identifiant` existe,
 * sinon on va voir dans la table `spip_identifiants`.
 *
 * @param string $objet
 *     Le type de l'objet
 * @param int $id_objet
 *     L'identifiant numérique de l'objet
 * @return string | null
 */
function identifiant_objet($objet, $id_objet) {

	include_spip('base/connect_sql');

	// Cherchons d'abord si l'objet existe bien
	if (
		$objet
		and $id_objet  = intval($id_objet)
		and $objet     = objet_type($objet)
		and $table_sql = table_objet_sql($objet)
		and $cle_objet = id_table_objet($objet)
		and $ligne     = sql_fetsel('*', $table_sql, "$cle_objet = $id_objet")
	){
		// soit c'est un champ normalisé de la table de l'objet
		if (isset($ligne['identifiant'])) {
			$identifiant = $ligne['identifiant'];
		}
		// sinon on cherche dans la table spip_identifiants
		else {
			$identifiant = sql_getfetsel('identifiant', 'spip_identifiants', 'objet='.sql_quote($objet).' AND id_objet='.$id_objet);
		}
	}

	return $identifiant;
}


/**
 * Manipuler l'identifiant d'un objet : créer, mettre à jour, ou supprimer.
 *
 * Pour supprimer l'identifiant actuel de l'objet, appeler la fonction sans 3ème paramètre (ou vide).
 *
 * @param string $objet
 *     Type d'objet
 * @param int $id_objet
 *     Identifiant numérique de l'objet
 * @return bool | string
 *     False si problème, sinon retour des fonctions sql_insertq, sql_updateq ou sql_delete.
 */
function maj_identifiant_objet($objet='', $id_objet='', $new_identifiant=''){

	if (
		$objet
		and $id_objet = intval($id_objet)
	) {

		// on récupère l'ancien identifiant
		$old_identifiant = sql_getfetsel(
			'identifiant',
			'spip_identifiants',
			'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet)
		);

		// regardons si le nouvel identifiant est déjà utilisé ailleurs
		$deja_utilise = (sql_countsel('spip_identifiants', 'objet='.sql_quote($objet).' AND id_objet !='.intval($id_objet).' AND identifiant='.sql_quote($new_identifiant).' AND identifiant != \'\'') > 0 ? true : false);

		// on définit ce qu'on doit faire
		$action =
			(!$old_identifiant and $new_identifiant)  ? 'creer' :
			(($old_identifiant and $new_identifiant)  ? 'maj' :
			(($old_identifiant and !$new_identifiant) ? 'supprimer' : ''));

		switch ($action) {

			case 'creer' :
				if (!$deja_utilise) {
					return sql_insertq(
						'spip_identifiants',
						array('objet' => $objet, 'id_objet' => $id_objet, 'identifiant' => $new_identifiant)
					);
				} else {

					return false;
				}

			case 'maj' :
				if (!$deja_utilise) {
					return sql_updateq(
						'spip_identifiants',
						array('identifiant' => $new_identifiant),
						'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND identifiant='.sql_quote($old_identifiant)
					);
				} else {
					return false;
				}

			case 'supprimer' :
				return sql_delete(
					'spip_identifiants',
					'objet='.sql_quote($objet).' AND id_objet='.intval($id_objet).' AND identifiant='.sql_quote($old_identifiant)
				);

			default :
				return false;
		}

	} else {
		return false;
	}

}


/**
 * Retourne une liste de tables possédant une colonne « identifiant »
 *
 * @return Array
 */
function tables_avec_identifiant() {

	include_spip('base/objets');

	$tables_avec_identifiant = array();

	if ($tables = lister_tables_objets_sql()) {
		foreach($tables as $table=>$infos) {
			if (array_key_exists('identifiant', $infos['field'])) {
				$tables_avec_identifiant[] = $table;
			}
		}
	}
	return $tables_avec_identifiant;
}


/**
 * Lister les identifiants utiles qui ne sont pas encore créés 
 **/  
function identifiants_utiles() {

	if (
		$identifiants_utiles = pipeline('identifiants_utiles', array())
		and is_array($identifiants_utiles)
	) {
		foreach ($identifiants_utiles as $objet => $identifiants) {
			// on retire de la liste les identifiants existants
			foreach ($identifiants as $identifiant) {
				if (sql_countsel('spip_identifiants', 'objet = '.sql_quote($objet).' AND identifiant='.sql_quote($identifiant))) { 
					unset($identifiants_utiles[$objet][array_search($identifiant,$identifiants_utiles[$objet])]);
				}
			}
			// on retire l'objet de la liste s'il ne reste plus d'identifiant
			if (!count($identifiants_utiles[$objet])) {
				unset($identifiants_utiles[$objet]);
			}
		}
	}

	return $identifiants_utiles;
}