<?php

function cfg_exemples_affiche_milieu($flux) {
	$exec =  $flux['args']['exec'];
	if ($exec=='articles'){

		// compiler #FORMULAIRE_CFG{...}
		$cfg_vue		= 'exemple_prive_article';
		$cfg_id			= $flux['args']['id_article'];
		//$cfg_form		= 'formulaires/formulaire_cfg_vue';
		$cfg_form		= 'formulaires/cfg_exemple_prive_article';
		$cfg_form_ajax 	= 'oui';
		$cfg_afficher_messages 	= 'non';
		$cfg_hash = substr(md5($cfg_vue . $cfg_id . $cfg_form . $cfg_form_ajax),0,6);

	
		include_spip('public/assembler');
		$contexte = array(
				'cfg_nom' => $cfg_vue,
				'cfg_vue' => $cfg_vue,
				'cfg_hash' => $cfg_hash,
				'cfg_form' => $cfg_form,
				'cfg_form_ajax' => $cfg_form_ajax,
				'cfg_id' => $cfg_id,
				'cfg_afficher_messages' => $cfg_afficher_messages
		);
		$form = recuperer_fond('formulaires/formulaire_cfg', $contexte);
		$flux['data'] .= $form;
	}
	
	return $flux;
}

?>
