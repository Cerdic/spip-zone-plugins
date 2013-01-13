<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008 - 2009
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
function exec_controle_guestbook() {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('guestbook:titre'), "", "");
	echo recuperer_fond('prive/controle_guestbook','', array('ajax'=>true));
	echo fin_page();
}
?>