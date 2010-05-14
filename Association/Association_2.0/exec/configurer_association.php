<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
	
function exec_configurer_association() {
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:association')) ;
		
		//		association_onglets();
		
		echo debut_gauche("",true);
		echo debut_boite_info(true);
		echo propre(_T('asso:info_doc'));  	
		echo fin_boite_info(true);
		echo debut_droite('', true);
		$liste = preg_files(_DIR_PLUGIN_ASSOCIATION . 'fonds/', $d.'cfg_[^/]*[.]html$');
		include_spip('public/assembler');
		$s = str_replace('.html','', $liste[0]);
		echo recuperer_fond($s);
		echo fin_gauche(), fin_page();
	}
}
?>
