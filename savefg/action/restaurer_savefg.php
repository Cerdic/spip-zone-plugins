<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_restaurer_savefg() {
	include_spip('inc/meta');
	include_spip('inc/filtres');
	$fond = _request('arg');
	$sfg = sql_getfetsel('valeur', 'spip_savefg', 'fond='.sql_quote($fond));
	ecrire_meta($fond, $sfg);
	ecrire_metas();
}
?>