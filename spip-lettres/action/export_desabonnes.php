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
 * Exporter les desabonnes
 */
function action_export_desabonnes() {
	if (autoriser('exporterdesabonnes', 'lettres')
//		AND $id_parent = _request('id_parent')
		) {

		$exporter_csv = charger_fonction('exporter_csv','inc');

		$delim = _request('delim')?_request('delim'):"TAB";
		$res = sql_select('email', 'spip_desabonnes');
		$exporter_csv("desabonnes", $res, $delim);
	}
}


?>

?>