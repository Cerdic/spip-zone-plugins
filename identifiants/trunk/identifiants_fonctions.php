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
 * Balise IDENTIFIANT
 *
 * Retourne l'identifiant de l'objet du contexte
 * ou l'identifiant de l'objets,id_objet passé en paramètre
 *
 */
function balise_IDENTIFIANT_dist($p) {
	if (!$_objet = interprete_argument_balise(1, $p)) {
		$_objet = objet_type($p->type_requete);
		$_id = champ_sql($p->boucles[$p->id_boucle]->primary, $p);
	} else {
		$_objet = interprete_argument_balise(1, $p);
		$_id = interprete_argument_balise(2, $p);
	}

	$p->code =  'identifiant_objet("' . $_objet . '",' . $_id . ')';
	$p->interdire_scripts = false;

	return $p;
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
	$identifiant = null;

	if ($objet
		and $id_objet  = intval($id_objet)
		and $objet     = objet_type($objet)
		and $table_sql = table_objet_sql($objet)
		and $cle_objet = id_table_objet($objet)
	) {
		// soit c'est un champ normalisé de la table de l'objet
		$trouver_table = charger_fonction('trouver_table', 'base');
		if ($desc = $trouver_table($table_sql)
			and isset($desc['field']['identifiant'])
		) {
			$identifiant = sql_getfetsel('identifiant', $table_sql, $cle_objet.' = '.intval($id_objet));
		// sinon on cherche dans la table spip_identifiants
		} else {
			$identifiant = sql_getfetsel(
				'identifiant',
				'spip_identifiants',
				'objet = '.sql_quote($objet).' AND id_objet = '.intval($id_objet)
			);
		}
	}

	return $identifiant;
}

/**
 * Manipuler l'identifiant d'un objet : créer, mettre à jour, ou supprimer.
 *
 * Pour supprimer l'identifiant actuel, appeler la fonction sans 3ème paramètre (ou lui donner valeur vide).
 *
 * @param string $objet
 *     Type d'objet
 * @param int $id_objet
 *     Identifiant numérique de l'objet
 * @return bool | string
 *     Retour des fonctions sql_insertq(), sql_updateq() ou sql_delete().
 *     False en cas de problème.
 */
function maj_identifiant_objet($objet = '', $id_objet = '', $new_identifiant = '') {

	// valeur retournée par défaut
	$resultat = false;

	if (
		$objet
		and $id_objet = $where_id_objet = intval($id_objet)
	) {

		// On récupère l'ancien identifiant éventuel
		$old_identifiant = sql_getfetsel(
			'identifiant',
			'spip_identifiants',
			array(
				'objet = ' . sql_quote($objet),
				'id_objet = ' . intval($id_objet),
			)
		);

		// Regardons si le nouvel identifiant est déjà utilisé pour un même type d'objet
		// Si l'objet qui a déjà l'identifiant n'existe plus, on l'attribue au nouvel objet.
		$deja_utilise = false;
		$identifiant_doublon = sql_fetsel(
			'objet, id_objet, identifiant',
			'spip_identifiants',
			array(
				'objet = ' . sql_quote($objet),
				'id_objet != ' . intval($id_objet),
				'identifiant = ' . sql_quote($new_identifiant),
				'identifiant != \'\'',
			)
		);
		if ($identifiant_doublon) {
			$table_objet_sql_doublon = table_objet_sql($identifiant_doublon['objet']);
			$id_table_objet_doublon  = id_table_objet($identifiant_doublon['objet']);
			if (sql_countsel($table_objet_sql_doublon, $id_table_objet_doublon.' = '.$identifiant_doublon['id_objet'])) {
				$deja_utilise = true;
			} else {
				// réattribuer l'identifiant si l'objet en doublon n'existe pas
				$where_id_objet  = $identifiant_doublon['id_objet'];
				$old_identifiant = $identifiant_doublon['identifiant'];
				$deja_utilise    = false;
			}
		}

		// On définit l'action à effectuer : créer, mettre à jour, ou supprimer
		$action =
			(!$old_identifiant and $new_identifiant)  ? 'creer' :
			(($old_identifiant and $new_identifiant)  ? 'maj' :
			(($old_identifiant and !$new_identifiant) ? 'supprimer' :
			''));

		switch ($action) {

			case 'creer':
				if (
					!$deja_utilise) {
					$resultat = sql_insertq(
						'spip_identifiants',
						array(
							'objet'       => $objet,
							'id_objet'    => $id_objet,
							'identifiant' => $new_identifiant,
						)
					);
				}
				break;

			case 'maj':
				if (!$deja_utilise) {
					$resultat = sql_updateq(
						'spip_identifiants',
						array(
							'identifiant' => $new_identifiant,
							'id_objet'    => intval($id_objet),
						),
						array(
							'objet = ' . sql_quote($objet),
							'id_objet = ' . intval($where_id_objet),
							'identifiant = ' . sql_quote($old_identifiant),
						)
					);
				}
				break;

			case 'supprimer':
				$resultat = sql_delete(
					'spip_identifiants',
					array(
						'objet = ' . sql_quote($objet),
						'id_objet = ' . intval($where_id_objet),
						'identifiant = ' . sql_quote($old_identifiant),
					)
				);
				break;

			default:
				$resultat = false;
				break;
		}

	}

	return $resultat;
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
		foreach ($tables as $table => $infos) {
			if (isset($infos['field']['identifiant'])) {
				$tables_avec_identifiant[] = $table;
			}
		}
	}
	return $tables_avec_identifiant;
}


/**
 * Lister les identifiants utiles qui ne sont pas encore créés
 */
function identifiants_utiles() {

	if (
		$identifiants_utiles = pipeline('identifiants_utiles', array())
		and is_array($identifiants_utiles)
	) {
		foreach ($identifiants_utiles as $objet => $identifiants) {
			// on retire les identifiants existants de la liste
			foreach ($identifiants as $identifiant) {
				if (sql_countsel(
					'spip_identifiants',
					'objet = '.sql_quote($objet).' AND identifiant = '.sql_quote($identifiant)
				)
				) {
					unset($identifiants_utiles[$objet][array_search($identifiant, $identifiants_utiles[$objet])]);
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
