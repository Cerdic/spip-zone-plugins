<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function ie_extras_affiche_gauche($flux){
	if ($flux['args']['exec'] == 'iextras'){
		$flux['data'] .= recuperer_fond('prive/navigation/ie_extras');
	}
	return $flux;
}
?>
