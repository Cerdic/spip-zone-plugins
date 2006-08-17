<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_instituer_carto_carte_dist
function action_instituer_carto_carte_dist() {

	include_spip('inc/actions');
	$var_f = charger_fonction('controler_action_auteur', 'inc');
	$var_f();

	$arg = _request('arg');

	list($id_carto_carte, $statut) = preg_split('/\W/', $arg);
	if (!$statut) $statut = _request('statut_nouv'); // cas POST
	if (!$statut) return; // impossible mais sait-on jamais

	$id_carto_carte = intval($id_carto_carte);
	$result = spip_query("SELECT statut FROM spip_carto_cartes WHERE id_carto_carte=$id_carto_carte");

	if ($row = spip_fetch_array($result)) {
		$statut_ancien = $row['statut'];
		}

	if ($statut != $statut_ancien) {
		spip_query("UPDATE spip_carto_cartes SET statut='$statut' WHERE id_carto_carte=$id_carto_carte");

/*		if ($statut == 'publie') {
			if ($GLOBALS['meta']['activer_moteur'] == 'oui') {
			include_spip("inc/indexation");
			marquer_indexer('carto_carte', $id_carto_carte);
			}
		}

		if ($statut_ancien == 'publie') {
			include_spip('inc/invalideur');
			suivre_invalideur("id='id_carto_carte/$id_carto_carte'");
		}
*/
	}
}
?>
