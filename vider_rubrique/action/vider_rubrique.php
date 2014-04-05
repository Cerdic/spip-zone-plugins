<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2012                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/charsets');	# pour le nom de fichier

/**
 * Effacer le contenu d'une rubrique
 *
 * @param null $id_rubrique
 * @return void
 */
function action_vider_rubrique_dist($arguments=null) {
	if (is_null($arguments)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arguments = $securiser_action();
	}

	$message = "Suppression des articles de la rubrique ";
	$arguments = explode(':', $arguments);
	$id_rubrique = $arguments[0];
	if($arguments[1]){ $vider_arbo=$arguments[1]; $message = "Suppression de l'arborescence complète de la rubrique "; }
	spip_log($message.$id_rubrique, "vider_rubrique");
	if (intval($id_rubrique)){
		$contexte = array('id_rubrique'=>$id_rubrique,'vider_arbo'=>$vider_arbo);
		$suppression = recuperer_fond("admin/vider_rubrique", $contexte);
	}
}