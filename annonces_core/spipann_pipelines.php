<?php

function spipann_affiche_gauche($flux){
	if ($flux['args']['exec'] == 'accueil'){
		$flux['data'] .= recuperer_fond('prive/contenu/spipann_accueil');
	}
	return $flux;
}

?>
