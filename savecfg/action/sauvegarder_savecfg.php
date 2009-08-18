<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_sauvegarder_savecfg() {
	include_spip('inc/filtres');
	$fond = _request('arg');
	$sfg = sql_getfetsel('valeur', 'spip_meta', 'nom='.sql_quote($fond));
	if (sql_countsel('spip_savecfg', 'fond='.sql_quote($fond)) == 0) {
		sql_insertq('spip_savecfg', array('id_savecfg' => '', 'fond' => $fond, 'valeur' => $sfg, 'titre' => 'Sauvegarde effectué le '.affdate(date('Y-m-d H:m:s')), 'version' => 1, 'date' => date('Y-m-d H:m:s')));
	}
}
?>