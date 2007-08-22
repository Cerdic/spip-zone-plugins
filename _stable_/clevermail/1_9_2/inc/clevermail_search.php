<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/find.png');
	echo '<br /><span class="verdana1">';
	echo '<form action="'.generer_url_ecrire('clevermail_subscribers_search','').'" method="post">';
	echo _T('clevermail:rechercher_abonne');
	echo '<input type="text" name="email" value="" class="formo" style="font-size:9px;" size="20"  />';
	echo '<div style="text-align:right"><input type="submit" value="Ok" class="fondo" style="font-size:10px" /></div>';
	echo '</form>';
	echo '</span>';
fin_cadre_relief();
?>