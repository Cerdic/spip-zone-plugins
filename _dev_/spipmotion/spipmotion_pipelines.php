<?php

include_spip("inc/spipmotion");

function spipmotion_affiche_droite($flux) {
	if ($flux['args']['exec'] =='articles_edit'){
		$spipmotion = charger_fonction('spipmotion', 'inc');
		$flux['data'] .= $spipmotion($flux['arg']['id_article']);
	}
	return $flux;
}

?>