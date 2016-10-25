<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function gabarits_editer_contenu_objet($flux) { 
	if (in_array($flux['args']['type'], lire_config('gabarits/objets',array('article')))) {
		$flux['args']['contexte']['objet'] = $flux['args']['type'];
		$regex = '#(<li class="editer_texte[^>].*>.*?<\/li>)#s';
		$gabarits_select = recuperer_fond('formulaires/inc-gabarits_select', $flux['args']['contexte']);
		$flux['data'] = preg_replace($regex,"$gabarits_select\n$1",$flux['data']);
	}
	return $flux;
}
