<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function formatspip_affiche_milieu($flux){
		switch($flux['args']['exec']) {
			case 'article':
			case 'articles':
				$id_article = $flux['args']['id_article'];
				// le formulaire qu'on ajoute
				$flux['data'] .= formatspip_affiche($id_article);
				break;
			default:
				break;
		}
		return $flux;
	}

	function formatspip_affiche($id_article){
		include_spip('inc/presentation');
		
		include_spip('public/assembler');
		if(!$txt = recuperer_fond('modeles/formatspip', array('id_article'=>$id_article))) return '';
		
		$bouton = bouton_block_depliable(_T('formatspip:texte_formatspip'), 'invisible', "formatspip");
		$bloc = debut_block_depliable(false, "formatspip");
		
		return debut_cadre_enfonce("../"._DIR_PLUGIN_FORMATSPIP."/images/formatspip-24.png", true, '', $bouton)
			. $bloc	. $txt . fin_block()
			. fin_cadre_enfonce(true);
	}
