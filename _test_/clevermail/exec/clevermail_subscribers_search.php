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

function exec_clevermail_subscribers_search() {

	if (isset($_POST['remove']) && strlen($_POST['sub_ids']) > 0) {
        foreach(explode(';', $_POST['sub_ids']) as $subId) {
			if ($subId != '') {
				spip_query("DELETE FROM cm_lists_subscribers WHERE sub_id = ".$subId);
	        	spip_query("DELETE FROM cm_subscribers WHERE sub_id = ".$subId);
	        	spip_query("DELETE FROM cm_pending WHERE sub_id = ".$subId);
	        	spip_query("DELETE FROM cm_posts_queued WHERE sub_id = ".$subId);
	        	spip_query("DELETE FROM cm_posts_done WHERE sub_id = ".$subId);
			}
        }
    }

	debut_page("CleverMail Administration", 'configuration', 'cm_index');

	echo debut_gauche('', true);
		include_spip("inc/clevermail_menu");
		echo '<br />';
		include_spip("inc/clevermail_search");
	echo debut_droite('', true);

	debut_cadre_relief();
		echo gros_titre('CleverMail Administration', '', '');
	fin_cadre_relief();

	echo '<form name="subscribers" action="'.generer_url_ecrire('clevermail_subscribers_search','').'" method="post">'."\n";
	echo '<input type="hidden" name="id" value="0" />'."\n";
	echo '<input type="hidden" name="sub_ids" value="" />'."\n";

	debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonnes.png');

	echo "<h3>\"".addslashes($_POST['email'])."\"</h3>";

	if(strlen(addslashes($_POST['email']))>2) {
		$result = spip_query("SELECT * FROM cm_subscribers WHERE sub_email like '%".addslashes($_POST['email'])."%'  ORDER BY sub_email");
		$total = spip_num_rows($result);
		if($total) {
			echo $total.' '._T('clevermail:resultats').'<br /><br />';
			echo "\n<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
			echo '<tr style="background-color:#DBE1C5">'."\n";
			echo '<th><input type="checkbox" onclick="toggle(document.subscribers, \'sub_id\')" /></th>'."\n";
			echo '<th>'._T('clevermail:emails').'</th>'."\n";
			echo '<th>'._T('clevermail:actions').'</th>'."\n";
			echo '</tr>'."\n";
			$nbrow = 0;
			while ($row = spip_fetch_array($result)) {
				echo '<tr style="background-color: '.($nbrow++%2 ? '#EEE' : '#FFF').';">'."\n";
				echo '<td class="arial1" style="border-top: 1px solid #CCC;">'."\n";
				echo '<input type="checkbox" name="sub_id" value="'.$row['sub_id'].'" />'."\n";
				echo '</td>'."\n";
				echo '<td class="verdana1" style="border-top: 1px solid #CCC;">'."\n";
				echo $row['sub_email'];
				echo '</td>'."\n";
				echo '<td class="arial1" style="border-top: 1px solid #CCC;">'."\n";
				echo '<a href="'.generer_url_ecrire("clevermail_subscribers_detail","sub_id=".$row['sub_id']).'">'._T('clevermail:modifier').'</a>';
				echo '</td>'."\n";
				echo '</tr>'."\n";
			}
			echo "</table>\n";
			echo "<br />";
		} else {
			echo _T('clevermail:aucun_resultat');
		}
	}
	fin_cadre_relief();

	echo '<input type="hidden" name="email" value="'.$_POST['email'].'" />'."\n";
	echo '<input type="hidden" name="remove" value="1" />'."\n";
	echo icone_horizontale(_T('clevermail:supprimer_abonnes'), 'javascript:if(confirm("'._T('clevermail:confirme_suppression_multiple_base').'")){checkbox2input(document.subscribers,"sub_id",document.subscribers.sub_ids);document.subscribers.submit();}', '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonnes.png', 'supprimer.gif', '', true);
	echo '</form>'."\n";
	fin_page();
}
?>
