<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 **/

 if (!defined("_ECRIRE_INC_VERSION")) return;
 
/**
 *  #EM_MENU_TYPE affiche le menu de changement de types et présélectionne celui de l'environnement
 *  ou de l'argument fourni: #EM_MENU_TYPE{#ENV{type}}
 */
function balise_EM_MENU_TYPE ($p) {
	return calculer_balise_dynamique($p,'EM_MENU_TYPE', array('em_type'));
}

/**
 * S'il n'y a pas de gestion de types on ne l'affiche pas
 *
 * @param object $args
 * @param object $filtres
 * @return
 */
function balise_EM_MENU_TYPE_stat ($args, $filtres) {
	if ((lire_config('emballe_medias/fichiers/gerer_types')!= 'on')
		OR (!is_array(lire_config('emballe_medias/types/types_dispos'))
			&& count(lire_config('emballe_medias/types/types_dispos')>1)
		)
	) return '';

	return $args;
}

/**
 * @param string $opt est le type passé dans l'environnement
 * @return
 */
function balise_EM_MENU_TYPE_dyn($opt) {

	$cible = parametre_url(parametre_url(self(),'id_article',''), 'em_type' , '', '&');
	$post = generer_url_action('emballe_medias_changer_url_type', 'redirect='. rawurlencode($cible), '&');
	
	$nom = 'em_changer_type';
	return array('formulaires/em_menu_type',
		0,
		array('nom' => $nom,
			'url' => $post,
			'em_type' => $opt
		)
	);
}
?>