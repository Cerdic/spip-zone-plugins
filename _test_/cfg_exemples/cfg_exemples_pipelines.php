<?php

function cfg_exemples_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	if ($exec=='articles'){

		// compiler #FORMULAIRE_CFG{...}
		include_spip('public/assembler');

		$flux['data'] .= recuperer_fond(
				"noisettes/formulaire_cfg", 
				array(
					'cfg_fond'=>'exemple_prive_article',
					'cfg_id' => $flux['args']['id_article']
				));
	}
	
	return $flux;
}

?>
