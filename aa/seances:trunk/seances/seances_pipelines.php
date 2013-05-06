<?php
// pour header_prive
function seances_header_prive($flux) {
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
	$args =  $flux['args'];
	if ($args['exec'] == 'articles' and $args['id_article']){
 		// est-ce activé pour la rubrique de cet article ?
		$actif = sql_getfetsel('seance','spip_rubriques AS r LEFT JOIN spip_articles AS a on a.id_rubrique=r.id_rubrique','a.id_article='.$args['id_article']);
 		if ($actif) {
 			$html = recuperer_fond('inclure/liste_seances',$args);
 			$flux['data'] .= $html;
 		}
 	}
 	return $flux;
}


// pipeline affiche_gauche
// activer / désactiver le mode séance pour les rubriques
function seances_affiche_gauche($flux){
	$args =  $flux['args'];
 	if ($args['exec'] == 'naviguer' and $args['id_rubrique']){
 		$html = recuperer_fond('inclure/seances_activer_rubrique',$args);
 		$flux['data'] .= $html;
 	}
 	return $flux;
}

// pour champ extra
function seances_objets_extensibles($objets){
	return array_merge($objets, array('seance' => _T('seances:seances_extra2'),'seances_endroit' => _T('seances:seances_endroits_extra2')));
}

?>