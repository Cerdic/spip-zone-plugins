<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

include_spip("inc/presentation");

function exec_clevermail_lists_remove() {

	if (isset($_GET['id'])) {
	    $count = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_lists WHERE lst_id = ".$_GET['id']));
	    $count2 = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb2 FROM cm_lists_subscribers WHERE lst_id = ".$_GET['id']));
	    if ($count['nb'] == 0) {
	        define('_ERROR', 'Impossible de supprimer une newsletter inexistante');
	    } elseif ($count2['nb2'] > 0) {
	        define('_ERROR', 'Impossible de supprimer une newsletter avec des abonn&eacute;s');
	    } else {
	        spip_query("DELETE FROM cm_lists WHERE lst_id = ".$_GET['id']);
	        header('location: '.generer_url_ecrire('clevermail_index'));
	        exit;
	    }
	}

	debut_page("CleverMail Administration", 'configuration', 'cm_index');
		echo debut_gauche('', true);
        	include_spip("inc/clevermail_menu");

		echo debut_droite('', true);
			debut_cadre_relief();
				echo gros_titre('CleverMail Administration', '', '');
			fin_cadre_relief();

			debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/lettre-24.png');
				echo '<h3>Newsletters :</h3>';
				if (defined('_ERROR')) {
    				echo '<p class="error">'._ERROR.'</p>';
				}
			fin_cadre_relief();
	fin_page();
}
?>