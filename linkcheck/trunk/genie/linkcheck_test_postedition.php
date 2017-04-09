<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_linkcheck_test_postedition($id, $objet) {

	include_spip('inc/linkcheck_fcts');

	$sel = sql_allfetsel(
		'sl.url, sl.distant, sl.id_linkcheck, sl.essais',
		'spip_linkchecks AS sl, spip_linkchecks_liens AS sll',
		'sll.id_objet='.intval($id).' AND sll.objet='.sql_quote($objet).' AND sll.id_linkcheck=sl.id_linkcheck'
	);

	foreach ($sel as $res) {
		linkcheck_maj_etat($res);
	}

	return 1;
}
