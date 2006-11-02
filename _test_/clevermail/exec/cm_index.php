<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006
	 *
	 **/

include_spip("inc/presentation");

function exec_cm_index() {

	debut_page("CleverMail Administration", 'configuration', 'cm_index');
		debut_gauche();
        	include_spip("inc/cm_menu");

		debut_droite();
			debut_cadre_relief();
				echo gros_titre('CleverMail Administration');
			fin_cadre_relief();

			debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/lettre-24.png');
				echo '<h3>'._T('cm:liste_lettres').' :</h3>';

    			$result = spip_query("SELECT lst_id, lst_name, lst_comment FROM cm_lists ORDER BY lst_name");
				while ($list = spip_fetch_array($result)) {
					$listInfo = array();
	    			$listInfo['subscribers'] = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_lists_subscribers WHERE lst_id = ".$list['lst_id']));
	    			$listInfo['subscribers'] = $listInfo['subscribers']['nb'];
	    			$listInfo['posts'] = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_posts WHERE lst_id = ".$list['lst_id']));
	    			$listInfo['posts'] = $listInfo['posts']['nb'];

					debut_cadre_formulaire();
						echo '<strong>'.$list['lst_name'].'</strong><br />';
						echo _T('cm:statistiques').' : ';
						if ($listInfo['subscribers'] > 0) {
				            echo '<a href="'.generer_url_ecrire("cm_lists_subscribers","id=".$list['lst_id']).'">'.$listInfo['subscribers'].' ';
				            echo ($listInfo['subscribers'] > 1 ? _T('cm:abonnes') : _T('cm:abonne'));
				            echo '</a>';
				        } else {
				            echo _T('cm:aucun_abonne');
				        }
						echo ' | ';
						if ($listInfo['posts'] > 0) {
				            echo '<a href="'.generer_url_ecrire("cm_posts","lst_id=".$list['lst_id']).'">'. $listInfo['posts'].' ';
				            echo ($listInfo['posts'] > 1 ? _T('cm:messages') : _T('cm:message'));
				            echo '</a>';
				        } else {
				            echo _T('cm:aucun_message');
				        }
				        echo '<br />';
						echo _T('cm:actions').' : ';
						echo '<a href="'.generer_url_ecrire("cm_lists_edit","id=".$list['lst_id']).'">'._T('cm:modifier').'</a> | ';
						if ($listInfo['subscribers'] == 0) {
							echo '<a href="'.generer_url_ecrire("cm_lists_remove","id=".$list['lst_id']).'">'._T('cm:supprimer').'</a> |';
						} else {
							echo _T('cm:supprimer').' |';
						}
						echo ' <a href="'.generer_url_ecrire("cm_post_edit","pst_id=-1&lst_id=".$list['lst_id']).'">'._T('cm:nouveau_message').'</a>';
					fin_cadre_formulaire();
						echo '<br />';
				}

			fin_cadre_relief();
				icone_horizontale(_T('cm:creer_lettre'), generer_url_ecrire("cm_lists_edit","id=-1"), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/lettre-24.png', 'creer.gif');
	fin_page();
}
?>