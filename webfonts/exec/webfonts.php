<?php
/**
 * Plugin webfonts
 * Licence GPL (c) 2010 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_webfonts(){
	if (!autoriser('administrer','webfonts',0)) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('webfonts:titre_page_webfonts'));
	
	echo gros_titre(_T('webfonts:titre_page_webfonts'),'',false);
	echo debut_grand_cadre(true);
	
	echo recuperer_fond('prive/webfonts',$_GET);

	echo fin_grand_cadre(true),fin_page();
}

?>