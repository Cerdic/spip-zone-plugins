<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Ajout du bouton permettant de se rendre sur la page de gestion des gabarits
function gabarits_ajouter_boutons($boutons_admin) {
	// uniquement si le plugin bandeau n'est pas la (ou SPIP 2.1)
	if(!$boutons_admin['bando_edition']){
		$boutons_admin['naviguer']->sousmenu['gabarits'] = new Bouton(
			 _DIR_PLUGIN_GABARITS.'/prive/themes/spip/images/gabarits-24.png',
			_T('gabarits:gabarits'),
			generer_url_ecrire('gabarits_tous')
		);
	}
	return ($boutons_admin);
}

function gabarits_editer_contenu_objet($flux){
	if (in_array($flux['args']['type'], lire_config('gabarits/objets',array('article')))){
		$flux['args']['contexte']['objet'] = $flux['args']['type'];
		$regex = '#(<li class="editer_texte[^>].*>.*?<\/li>)#s';
		$gabarits_select = recuperer_fond('formulaires/inc-gabarits_select', $flux['args']['contexte']);
		$flux['data'] = preg_replace($regex,"$gabarits_select\n$1",$flux['data']);
	}
	return $flux;
}
