<?php

function cfg_exemples_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	if ($exec=='articles'){

		// compiler #FORMULAIRE_CFG{...}
		include_spip('balises/formulaire_cfg');
		$flux['data'] .= 
			calculer_balise_formulaire_cfg(
				$cfg_vue		= 'exemple_prive_article',
				$cfg_id			= $flux['args']['id_article'],
				//$cfg_form		= 'formulaires/formulaire_cfg_vue',
				$cfg_form		= 'formulaires/cfg_exemple_prive_article',
				$cfg_form_ajax 	= 'oui',
				$cfg_afficher_messages 	= 'non'	
			);
	}
	
	return $flux;
}

?>
