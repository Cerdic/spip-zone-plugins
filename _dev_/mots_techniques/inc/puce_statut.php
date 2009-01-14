<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_once(_DIR_RESTREINT.'inc/puce_statut.php');

function puce_statut_mot($id, $statut, $id_groupe, $type, $ajax='') {
	static $icones = array();
	if (!isset($icones[$id_groupe])) {
		$t = sql_fetsel('technique', 'spip_groupes_mots', 'id_groupe='.intval($id_groupe));
		if ($t['technique'] == 'oui')
			$icones[$id_groupe] = _DIR_PLUGIN_MOTS_TECHNIQUES.'images/mot-technique-16.png';
		else
			$icones[$id_groupe] = chemin_image('petite-cle.gif');
	}

	return "<img src='" . $icones[$id_groupe] . "' alt='' />";
}
