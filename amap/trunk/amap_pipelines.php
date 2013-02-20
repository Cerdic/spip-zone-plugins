<?php
/**
* Plugin Amap
*
* @author: Stephane Moulinet
* @author: E-cosystems
* @author: Pierre KUHN 
*
* Copyright (c) 2010-2013
* Logiciel distribue sous licence GPL.
*
**/

function amap_affiche_gauche($flux){
	include_spip('inc/presentation');
	if ($flux['args']['exec'] == 'infos_perso'){
		$flux['data'] .= recuperer_fond('prive/inclure/affiche_boite', array('id_auteur'=>$flux['args']['id_auteur']));
	}
return $flux;
}
?>
