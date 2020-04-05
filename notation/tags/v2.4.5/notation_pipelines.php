<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Inserer les css de notation
 * @param string $flux
 * @return string
 */
function notation_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/notation.v2.css').'" type="text/css" media="all" />';
	return $flux;
}

/**
 * insertion des js de notation :
 * uniquement si on trouve un formulaire de notation dans la page
 * pour eviter de declencher et charger sur toutes les pages
 * Et on l'ajoute en fin de page pour la perf
 *
 * @param string $flux
 * @return string mixed
 */
function notation_affichage_final($flux){
	if (strpos($flux, "'notation_note notation_note_on_load'") === false)
		return $flux;
	$incHead = "";
	$incHead .= "<script src='".find_in_path('javascript/jquery.MetaData.js')."' type='text/javascript'></script>\n";
	$incHead .= "<script src='".find_in_path('javascript/jquery.rating.js')."' type='text/javascript'></script>\n";
	$incHead .= "<script src='".find_in_path('javascript/notation.js')."' type='text/javascript'></script>\n";
	include_spip('inc/filtres');
	if(function_exists('compacte_head')){
		$incHead = compacte_head($incHead);
	}
	if ($p = stripos($flux, '</body>'))
		return substr_replace($flux, $incHead, $p, 0);
	else
		return $flux.$incHead;
}

/**
 * Boite de configuration des objets articles
 *
 * @param array $flux
 * @return array
 */
function notation_afficher_config_objet($flux){
	if (($type = $flux['args']['type'])
		AND $id = $flux['args']['id']){
		if (autoriser('moderernote', $type, $id)) {
			$id_table_objet = id_table_objet($type);
			$flux['data'] .= recuperer_fond("prive/configurer/configurer_note",array('id_objet'=>$id,'objet'=>  objet_type(table_objet($type))));
		}
	}
	return $flux;
}

/**
 * Remplissage des champs a la creation d'objet
 *
 * @param array $flux
 * @return array
 */
function notation_pre_insertion($flux){
	if ($flux['args']['table']=='spip_articles'){
		include_spip('inc/config');
		$flux['args']['data']['accepter_note'] = substr(lire_config('notations_public'),0,3);
	}
	return $flux;
}

/**
 * Definir les meta de configuration liee aux notations
 *
 * @param array $metas
 * @return array
 */
function notation_configurer_liste_metas($metas){
	$metas['notations_publics'] = 'oui';

	return $metas;
}

/**
 * Si le plugin indexer est disponible, insérer la note dans l'indexation
 * @param string $flux
 * @return string
 */
function notation_indexer_document($flux){
	$objet = $flux['args']['objet'];
	$id_objet = $flux['args']['id_objet'];
	$notes = sql_fetsel('note,note_ponderee,nombre_votes','spip_notations_objets',"`id_objet`=$id_objet and `objet`=".sql_quote($objet));
	if (is_array($notes)){
		$flux['data']->properties['notes'] = $notes;
	}
	return $flux;
}

/**
 * Optimiser la base de donnee en supprimant les liens orphelins
 *
 * @param int $n
 * @return int
 */
function notation_optimiser_base_disparus($flux){
	$n = &$flux['data'];
	$mydate = $flux['args']['date'];


	$objets = sql_allfetsel('DISTINCT objet','spip_notations');
	$objets = array_map('reset',$objets);
	foreach($objets as $objet) {
		$spip_table_objet = table_objet_sql($objet);
		$id_table_objet = id_table_objet($objet);

		$old = sql_allfetsel("L.id_objet AS id_objet",
			// la condition de jointure inclue L.objet='xxx' pour ne joindre que les bonnes lignes
			// du coups toutes les lignes avec un autre objet ont un id_xxx=NULL puisque LEFT JOIN
			// il faut les eliminier en repetant la condition dans le where L.objet='xxx'
						"spip_notations_objets AS L
							LEFT JOIN $spip_table_objet AS O
								ON (O.$id_table_objet=L.id_objet AND L.objet=".sql_quote($objet).")",
				"L.objet=".sql_quote($objet)." AND O.$id_table_objet IS NULL");

		if (count($old)) {
			$old = array_map('reset',$old);
			spip_log("Suppression des entrees spip_notations objet $objet ids : ".implode(',',$old));
			sql_delete('spip_notations_objets', 'objet=' . sql_quote($objet) . ' AND ' . sql_in('id_objet',$old));
			$flux['data'] += count($old);
			$res = sql_select('id_notation as id', 'spip_notations', 'objet=' . sql_quote($objet) . ' AND ' . sql_in('id_objet',$old));
			$flux['data'] += optimiser_sansref('spip_notations', 'id_notation', $res);
		}

	}

	return $flux;

}