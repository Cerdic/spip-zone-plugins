<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_savefg() {
	include_spip('inc/filtres');
	$fond = _request('arg');
	sql_delete('spip_savefg', 'fond='.sql_quote($fond));
}
?>