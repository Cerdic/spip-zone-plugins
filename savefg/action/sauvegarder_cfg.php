<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_sauvegarder_cfg() {
	include_spip('inc/filtres');
	$fond = _request('arg');
	$sfg = sql_getfetsel('valeur', 'spip_meta', 'nom='.sql_quote($fond));
	if (sql_countsel('spip_savefg', 'fond='.sql_quote($fond)) == 0) {
		sql_insertq('spip_savefg', array('id_savefg' => '', 'fond' => $fond, 'valeur' => $sfg, 'commentaire' => 'Sauvegarde effectué le '.affdate(date('Y-m-d H:m:s')), 'version' => 1, 'date' => date('Y-m-d H:m:s')));
	}
}
?>