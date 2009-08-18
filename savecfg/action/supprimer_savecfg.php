<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_savecfg() {
	include_spip('inc/filtres');
	$fond = _request('arg');
	sql_delete('spip_savecfg', 'id_savecfg='.sql_quote(_request('id_savecfg')).' AND fond='.sql_quote($fond));
}
?>