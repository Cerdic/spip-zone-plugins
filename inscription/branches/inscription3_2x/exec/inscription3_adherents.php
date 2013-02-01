<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2010 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Page de l'espace privé listant les utilisateurs
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_inscription3_adherents() {

	if (!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
	}

	$contexte['case'] = _request('case');
	$contexte['valeur'] = _request('valeur');

	$contexte = array_merge($contexte,$_GET);

	$commencer_page = charger_fonction('commencer_page', 'inc');

	pipeline('exec_init',array('args'=>$_GET,'data'=>''));

	echo $commencer_page(_T('inscription3:gestion_adherents'), "", "", "");

	echo recuperer_fond('prive/table_adherents',$contexte);
}
?>