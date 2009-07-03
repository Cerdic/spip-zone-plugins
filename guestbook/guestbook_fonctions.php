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
function guestbook_insert_head($flux){
	$flux .= '<link media="all" type="text/css" href="'._DIR_PLUGIN_GUESTBOOK.'prive/formulaire_guestbook.css" rel="stylesheet" />';
	return $flux;
}
function captch_it ($lettres) {
	$rand_letter = '';
	for ($i=0 ; $i<8 ; $i++) {
		$choix = rand(0,1);
		if ($choix == 0){
		$int = rand(0,51);
   		$rand_letter .= $lettres[$int];
   		}
   		else if ($choix == 1) {
   		$rand_letter .= rand(1,9);
   		}
    }
    return $rand_letter;
}
?>