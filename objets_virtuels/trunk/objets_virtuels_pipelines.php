<?php
/**
 * Utilisations de pipelines par Objets virtuels
 *
 * @plugin     Objets virtuels
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Objets_virtuels\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclare les champs virtuels utilisés
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 * @return array
 */
function objets_virtuels_declarer_tables_objets_sql($tables) {
	include_spip('objets_virtuels_fonctions');
	$tables_actives = objets_virtuels_tables_actives();
	foreach ($tables_actives as $table) {
		if (isset($tables[$table])) {
			if (empty($tables[$table]['field']['virtuel'])) {
				$tables[$table]['field']['virtuel'] = 'VARCHAR(255) DEFAULT \'\' NOT NULL';
			}
		}
	}
	return $tables;
}


/**
 * Ajoute le formulaire de redirection sur les objets activés
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $flux
 * @return array
 */
function objets_virtuels_afficher_config_objet($flux) {
	include_spip('objets_virtuels_fonctions');
	$tables = objets_virtuels_tables_actives();
	$type = $flux['args']['type'];
	// ! Articles déjà gérés par le Core
	if ($type != 'article' and in_array(table_objet_sql($type), $tables)) {
		$flux['data'] .= recuperer_fond('prive/objets/editer/redirection_objet_virtuel', [
			'objet' => $type,
			'id_objet' => $flux['args']['id']
		]);
	}
	return $flux;
}


/**
 * Utilisation du pipeline affiche milieu
 *
 *  Ajoute un bloc montrant que l'objet a une redirection, si tel est le cas.
 *
 * @pipeline affiche_milieu
 *
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
 */
function objets_virtuels_affiche_milieu($flux) {

	// si on est sur une page ou il faut inserer les mots cles...
	if ($desc = trouver_objet_exec($flux['args']['exec'])
		and $desc['edition'] !== true // page visu
		and $type = $desc['type']
		and $type != 'article' // ! Articles déjà gérés par le Core
		and $id_table_objet = $desc['id_table_objet']
		and isset($flux['args'][$id_table_objet])
		and ($id = intval($flux['args'][$id_table_objet]))
		and (in_array($desc['table_objet_sql'], objets_virtuels_tables_actives()))
		and $virtuel = sql_getfetsel('virtuel', $desc['table_objet_sql'], $id_table_objet . '=' . $id)
	) {
		$texte = recuperer_fond(
			'prive/squelettes/inclure/redirection_objet_virtuel',
			array(
				'virtuel' => $virtuel,
			)
		);
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}