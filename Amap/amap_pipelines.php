<?php
function amap_affiche_gauche($flux){
	include_spip('inc/presentation');
	if ($flux['args']['exec'] == 'auteur_infos'){
		$flux['data'] .= recuperer_fond('prive/inclure/affiche_boite', array('id_auteur'=>$flux['args']['id_auteur']));
	}
return $flux;
}
?>
