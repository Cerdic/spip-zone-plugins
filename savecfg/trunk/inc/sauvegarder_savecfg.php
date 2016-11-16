<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function sauvegarder_savecfg($fond, $titre, $sfg) {
	// Insert ou Update ?
	$id_savecfg = sql_getfetsel('id_savecfg', 'spip_savecfg',
		'titre=' . sql_quote($titre) . ' AND fond=' . sql_quote($fond));
	if ($id_savecfg > 0) { // Update
		sql_updateq('spip_savecfg', array('valeur' => $sfg, 'date' => date('Y-m-d H:m:s')),
			'id_savecfg=' . $id_savecfg);

		return _T('savecfg:miseajour_ok', array('titre' => $titre));
	} else { // Insert
		sql_insertq('spip_savecfg', array(
			'id_savecfg' => '',
			'fond' => $fond,
			'valeur' => $sfg,
			'titre' => $titre,
			'date' => date('Y-m-d H:m:s')
		));

		return _T('savecfg:sauvegarde_ok', array('titre' => $titre));
	}
}
