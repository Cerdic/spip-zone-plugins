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
function action_vider_rubrique_dist($id_rubrique=null) {

	if (is_null($id_rubrique)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_rubrique = $securiser_action();
	}

	if (intval($id_rubrique)){
		$contexte = array('id_rubrique'=>$id_rubrique);
		$suppression = recuperer_fond("admin/vider_rubrique", $contexte);
	}
}