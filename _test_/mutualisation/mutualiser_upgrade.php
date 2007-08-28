<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function mutualiser_upgrade() {
	include_spip('inc/minipres');

	// verif securite
	if (_request('secret')
	!= md5(
	$GLOBALS['meta']['version_installee'].'-'.$GLOBALS['meta']['alea_ephemere']
	)) {
		echo minipres();
		exit;
	}

	if ($GLOBALS['spip_version']
	== str_replace(',','.',$GLOBALS['meta']['version_installee'])) {
		echo minipres('Rien &#224; faire');
		exit;
	}

	// faire l'upgrade
	$old = $GLOBALS['meta']['version_installee'];
	$base = charger_fonction('upgrade', 'base');
	$base('upgrade',false);
	lire_metas();
	$new = $GLOBALS['meta']['version_installee'];
	if ($old == $new
	OR $new != $GLOBALS['spip_version']) {
		echo minipres(_T('titre_page_upgrade'),
			_L('Erreur de mise &#224; jour de @old@ vers @new@',
				array('old' => $old, 'new' => $new))
		);
	} else {
		echo minipres(_T('titre_page_upgrade'),
			_L('La base de donn&#233;es a &#233;t&#233; mise &#224; jour de @old@ vers @new@',
				array('old' => $old, 'new' => $new))
		);
		// TODO : vider tmp
	exit;
}

?>
