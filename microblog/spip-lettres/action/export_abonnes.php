<?php
/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *
 **/


include_spip('base/abstract_sql');

/**
 * Exporter les abonnes
 */
function action_export_abonnes() {
	if (autoriser('exporterabonnes', 'lettres')
		AND $id_parent = _request('id_parent')) {

		$exporter_csv = charger_fonction('exporter_csv','inc');

		$delim = _request('delim')?_request('delim'):";";
		$res = sql_select('A.email,A.nom', 'spip_abonnes as A JOIN spip_abonnes_rubriques AS AR ON A.id_abonne=AR.id_abonne', 'AR.statut="valide" AND AR.id_rubrique='.intval($id_parent));
		$exporter_csv("abonnes-$id_parent", $res, $delim);

	}
}


?>