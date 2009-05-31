<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

include_spip('inc/presentation');

function exec_clevermail_subscribers_detail() {

	if(isset($_GET['sub_id'])) {
		$sub_id = $_GET['sub_id'];
		$count = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_subscribers WHERE sub_id = '".$sub_id."'"));
		if($count['nb']==1) {
			if(isset($_GET['lst_id']) AND $_GET['lst_id'] > 0) {
				// Unsubscribe
				spip_query("DELETE FROM cm_lists_subscribers WHERE lst_id = ".$_GET['lst_id']." AND sub_id = ".$sub_id);
			} else if(isset($_GET['lst_id']) AND $_GET['lst_id'] == 0) {
				// Delete
				spip_query("DELETE FROM cm_lists_subscribers WHERE sub_id = ".$sub_id);
	        	spip_query("DELETE FROM cm_subscribers WHERE sub_id = ".$sub_id);
	        	spip_query("DELETE FROM cm_pending WHERE sub_id = ".$sub_id);
	        	spip_query("DELETE FROM cm_posts_queued WHERE sub_id = ".$sub_id);
	        	spip_query("DELETE FROM cm_posts_done WHERE sub_id = ".$sub_id);

	        	header('location: '.generer_url_ecrire('clevermail_index'));
			}
			$abonne = spip_fetch_array(spip_query("SELECT sub_email FROM cm_subscribers WHERE sub_id = '".$sub_id."'"));
			$list = spip_query("SELECT ls.lsr_mode, l.lst_id, l.lst_name FROM cm_lists_subscribers AS ls, cm_lists AS l WHERE ls.sub_id = '".$sub_id."' AND ls.lst_id = l.lst_id");
		} else {
			define('_ERROR', _T('clevermail:abonne_inconnu'));
		}
	} else {
		header('location: '.generer_url_ecrire('clevermail_index'));
	    exit;
	}

	debut_page("CleverMail Administration", 'configuration', 'cm_index');

	echo debut_gauche('', true);
		include_spip("inc/clevermail_menu");

	echo debut_droite('', true);

	debut_cadre_relief();
		echo gros_titre('CleverMail Administration', '', '');
	fin_cadre_relief();

	debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonne.png');

		echo '<h3>'._T('clevermail:modifier_abonne').' : '.$abonne['sub_email'].'</h3>';

		if (defined('_ERROR')) {
		    echo '<p class="error">'._ERROR.'</p>';
		}

		if(spip_num_rows($list)>0) {
			echo '<table border="0" cellpadding="2" cellspacing="0" width="100%" class="arial2" style="border: 1px solid #aaaaaa;">';
			echo '<tr style="background-color:#DBE1C5">';
			echo '<th>'._T('clevermail:lettres_information').'</th>';
			echo '<th>'._T('clevermail:mode').'</th>';
			echo '<th>'._T('clevermail:actions').'</th>';
			echo '</tr>';
			$nbrow = 0;
			while($row = spip_fetch_array($list)) {
				echo '<tr style="background-color: '.($nbrow++%2 ? '#EEE' : '#FFF').';">';
				echo '<td class="arial1" style="border-top: 1px solid #CCC;">';
				echo $row['lst_name'];
				echo '</td>';
				echo '<td class="arial1" style="border-top: 1px solid #CCC;">';
				echo ($row['lsr_mode']==1 ? 'HTML' : 'Text');
				echo '</td>';
				echo '<td class="arial1" style="border-top: 1px solid #CCC;">';
				echo '<a href="'.generer_url_ecrire("clevermail_subscribers_detail","lst_id=".$row['lst_id']."&sub_id=".$sub_id).'">'._T('clevermail:desabonner').'</a>';
				echo '</td>';
				echo '<tr>';
			}
			echo '</table>';
		} else {
			echo _T('clevermail:abonne_aucune_lettre');
		}

	fin_cadre_relief();
		echo icone_horizontale(_T('clevermail:supprimer_abonne_base'), generer_url_ecrire("clevermail_subscribers_detail","lst_id=0&sub_id=".$sub_id), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonne.png', 'supprimer.gif', '', true);

	fin_page();
}
?>
