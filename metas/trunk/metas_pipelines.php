<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Affichage des metas dans le privé.
 *
 * @param array $flux
 *
 * @return array
 */
function metas_affiche_milieu($flux) {
	$en_cours = trouver_objet_exec($flux['args']['exec']);
	$page_vue = false;
	if ($en_cours) {
		$table_objet = lister_tables_objets_sql($en_cours['table_objet_sql']);
		$page_vue = $table_objet['page'];
	}

	// Mode edition, affichage du formulaire.
	if ($page_vue and $en_cours['edition'] == true /* page visu */ and $type = $en_cours['type'] and $id_table_objet = $en_cours['id_table_objet'] and ($id = isset($flux['args'][$id_table_objet]) ? intval($flux['args'][$id_table_objet]) : 0)) {
		$texte = recuperer_fond('prive/squelettes/inclure/editer_metas', array(
				'table_source' => 'metas',
				'objet' => $type,
				'id_objet' => $id,
			));
		// on affiche le texte des metas au niveau du commentaire affiche_milieu (et pas en fin de page)
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}

function metas_afficher_complement_objet($flux) {
	include_spip('base/abstract_sql');
	$texte = '';

	if (isset($flux['args']['type']) and isset($flux['args']['id'])) {
		$meta = sql_fetsel('id_meta', 'spip_metas_liens', "id_objet=" . $flux['args']['id'] . " AND objet='" . $flux['args']['type'] . "'");
		$texte = recuperer_fond('prive/objets/contenu/meta', array(
			'id_meta' => $meta['id_meta'],
			'objet' => $flux['args']['type'],
			'id_objet' => $flux['args']['id'],
		));
	}

	if (isset($flux['data'])) {
		$flux['data'] .= $texte;
	}

	return $flux;
}
