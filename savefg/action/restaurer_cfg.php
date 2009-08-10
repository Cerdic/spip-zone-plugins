<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_restaurer_cfg() {
	include_spip('inc/filtres');
	$fond = _request('arg');
	$sfg = sql_getfetsel('valeur', 'spip_savefg', 'fond='.sql_quote($fond));
	if (sql_countsel('spip_meta', 'nom='.sql_quote($fond)) == 0) {
		sql_insertq('spip_meta', array('nom' => $fond, 'valeur' => $sfg))
	}
	else {
		sql_updateq('spip_meta', array('valeur' => $sfg), 'nom='.sql_quote($fond));
	}
}
?>