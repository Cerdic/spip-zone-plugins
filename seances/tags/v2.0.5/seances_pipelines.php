<?php
// pour header_prive
function seances_header_prive_css($flux) {
	$css = find_in_path('prive/seances_styles.css');
	if ($css) {
		$flux .= '<!-- plugin seances -->'."\n";
		$flux .= '<link href="'.$css.'" rel="stylesheet" type="text/css" />'."\n";
	}
	return $flux;
}

// pipeline affiche milieu
// affichage saisie des séances pour les articles
function seances_affiche_milieu($flux){
	$e = trouver_objet_exec($flux['args']['exec']);
	if ($e['type'] == 'article' and $e['id_table_objet']){
 		// est-ce activé pour la rubrique de cet article ?
		$actif = sql_getfetsel('seance','spip_rubriques AS r LEFT JOIN spip_articles AS a on a.id_rubrique=r.id_rubrique','a.id_article='.intval($flux['args']['id_article']));
 		if ($actif) {
 			$html = recuperer_fond('inclure/liste_seances',$flux['args']);
 			$flux['data'] = $html.$flux['data'];
 		}
 	}
 	return $flux;
}


// pipeline affiche_gauche
// activer / désactiver le mode séance pour les rubriques
function seances_affiche_gauche($flux){
	$e = trouver_objet_exec($flux['args']['exec']);
 	if ($e['type'] == 'rubrique' and $e['id_table_objet']){
 		$html = recuperer_fond('inclure/seances_activer_rubrique',$flux['args']);
 		$flux['data'] .= $html;
 	}
 	return $flux;
}


function seances_optimiser_base_disparus($flux){
	# les seances liees a un article supprimé ou à la poubelle
	$res = sql_select('s.id_seance,s.id_article',
		'spip_seances AS s LEFT JOIN spip_articles AS a ON s.id_article=a.id_article',
		'a.id_article IS NULL');
	while ($row = sql_fetch($res))
		sql_delete('spip_seances','id_article='.$row['id_article'].' AND id_seance='.$row['id_seance']);

	return $flux;
}


?>