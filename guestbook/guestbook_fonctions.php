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
?>