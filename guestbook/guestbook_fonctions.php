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
	$flux .= '<link media="all" type="text/css" href="'.find_in_path('prive/css/formulaire_guestbook.css').'" rel="stylesheet" />';
	return $flux;
}

?>