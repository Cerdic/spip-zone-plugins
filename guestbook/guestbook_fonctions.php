<?php
	/**
	 * GuestBook
	 *
	 * Copyright (c) 2008 - 2010
	 * Yohann Prigent (potter64)
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/
	 
$GLOBALS['formulaires_no_spam'][] = 'guestbook';

function guestbook_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link media="all" type="text/css" href="'.find_in_path('prive/css/formulaire_guestbook.css').'" rel="stylesheet" />';
	}
	return $flux;
}

function guestbook_insert_head($flux){
	$flux = guestbook_insert_head_css($flux); // au cas ou il n'est pas implemente
	return $flux;
}
?>