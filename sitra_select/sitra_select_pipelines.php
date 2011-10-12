<?php

// pipeline affiche milieu
// affichage saisie des selections pour les articles
function sitra_select_affiche_milieu($flux){
	$args =  $flux['args'];
	if ($args['exec'] == 'articles' and $args['id_article']){
			$html = recuperer_fond('inclure/selections',$args);
 			$flux['data'] .= $html;
			
			// $flux['data'] .= 'OK';
 	}
 	return $flux;
}

?>