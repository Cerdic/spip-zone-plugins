<?php
/* afficher la liste des seances pour un article */
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
?>