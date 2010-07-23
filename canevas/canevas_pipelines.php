<?php

// Ajout du bouton permettant de se rendre sur la page de gestion des canevas
function canevas_ajouter_boutons($boutons_admin) {
	// uniquement si le plugin bandeau n'est pas la (ou SPIP 2.1)
	if(!$boutons_admin['bando_edition']){
		$boutons_admin['naviguer']->sousmenu['canevas'] = new Bouton(
			 _DIR_PLUGIN_CANEVAS.'/prive/themes/spip/images/canevas-24.png',
			_T('canevas:canevas'),
			generer_url_ecrire('canevas_tous')
		);
	}
	return ($boutons_admin);
}

function canevas_editer_contenu_objet($flux){
	if ($flux['args']['type']=='article'){
		$regex = '#(<li class="editer_texte[^>].*>.*?<\/li>)#s';
		$canevas_select = recuperer_fond('formulaires/inc-canevas_select', $flux['args']['contexte']);
		$flux['data'] = preg_replace($regex,"$canevas_select\n$1",$flux['data']);
	}
	return $flux;
}

?>
