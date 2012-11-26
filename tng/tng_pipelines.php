<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function tng_affiche_milieu($flux){
	if ($flux['args']['exec'] == 'configurer_avancees')
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/configurer', array('configurer' => 'configurer_tng'));
	return $flux;
}

?>
