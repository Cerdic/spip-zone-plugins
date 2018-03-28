<?php
/**
 * Utilisations de pipelines
 * @plugin     Xiti
 * @copyright  2014-2018
 * @author     France diplomatie - Vincent
 * @licence    GNU/GPL
 * @package SPIP\Xiti\Pipelines
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajoute le formulaire de lien à un niveau deux sur les objets configurés
 *
 * @pipeline affiche_milieu
 * @param array $flux
 * @return array
 */
function xiti_affiche_milieu($flux) {
	if ($en_cours = trouver_objet_exec($flux['args']['exec'])
		and $en_cours['edition'] !== true // page visu
		and $type = $en_cours['type']
		and $id_table_objet = $en_cours['id_table_objet']
		and isset($flux['args'][$id_table_objet])
		and ($id = intval($flux['args'][$id_table_objet]))
		and (autoriser('lierxitiniveau', $type, $id))
	) {
		$texte = recuperer_fond('prive/squelettes/inclure/xiti_lier', array('id_objet' => $id, 'objet' => $type));
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}
	return $flux;
}

function xiti_optimiser_base_disparus($flux){
	$n = &$flux['data'];
	$mydate = $flux['args']['date'];
	/**
	 * les niveaux 2 lies a une id_objet inexistant
	 */
	$r = sql_select("DISTINCT objet",'spip_xiti_niveaux_liens');
	while ($t = sql_fetch($r)){
		if ($type = $t['objet']) {
			$spip_table_objet = table_objet_sql($type);
			$id_table_objet = id_table_objet($type);
			$res = sql_allfetsel('*', 'spip_xiti_niveaux_liens', 'objet='.sql_quote($type));
			$res = sql_select('xiti.id_objet AS id',
						"spip_xiti_niveaux_liens AS xiti
							LEFT JOIN $spip_table_objet AS O
								ON O.$id_table_objet=xiti.id_objet AND xiti.objet=".sql_quote($type),
							"xiti.objet=".sql_quote($type)." AND O.$id_table_objet IS NULL AND xiti.id_objet>0");

			while ($row = sql_fetch($res)) {
				sql_delete('spip_xiti_niveaux_liens', 'objet='.sql_quote($type).' AND id_objet='.intval($row['id']));
			}
			sql_delete('spip_xiti_niveaux_liens', 'id_xiti_niveau=0');
		}
	}
	return $flux;
}

