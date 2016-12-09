<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2013                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/import_ics");

function action_synchro_almanach_dist() {

//vérification de l'auteur en cours//
$securiser_action = charger_fonction('securiser_action', 'inc');
$arg = $securiser_action();

	if (!preg_match(",^(\d+)$,", $arg, $r)) {
		 spip_log("action_synchro_almanach_dist $arg pas compris");
		 return;
	}
	$id_almanach = $r[1];
	$result = sql_fetsel("*","spip_almanachs","id_almanach=$id_almanach");
	spip_log ("Import manuel de l'almanach $id_almanach","import_ics"._LOG_INFO);
	importer_almanach(
		$id_almanach,
		$result["url"],
		$result["id_article"],
		array(
			'ete' => $result["decalage_ete"],
			'hiver' => $result["decalage_hiver"]
		)
	);
	spip_log ("Fin de l'import manuel de l'almanach $id_almanach","import_ics"._LOG_INFO);
}

?>