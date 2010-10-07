<?php
function seances_affiche_gauche($flux){
	$args =  $flux['args'];
 	if ($args['exec'] == 'naviguer' and $args['id_rubrique']){
 		$html = recuperer_fond('inclure/seances_activer_rubrique',$args);
 		$flux['data'] .= $html;
 	}
 	return $flux;
}
?>