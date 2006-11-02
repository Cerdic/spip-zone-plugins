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

function exec_cm_posts() {

	if($_GET['pst_id']>0 AND $_GET['lst_id']>0) {
		spip_query("DELETE FROM cm_posts WHERE pst_id = ".$_GET['pst_id']." AND lst_id= ".$_GET['lst_id']);
	}

	debut_page("CleverMail Administration", 'configuration', 'cm_index');
		debut_gauche();
        	include_spip("inc/cm_menu");

        	$result = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_posts p, cm_posts_queued q WHERE p.pst_id = q.pst_id AND p.lst_id=".$_GET['lst_id']));
			if ($result['nb'] > 0) {
				echo '<br />';
				debut_cadre_relief();
				echo '<a href="'.generer_url_ecrire("cm_queue_process","").'">Forcer les envois en attente</a>';
				fin_cadre_relief();
			}

		debut_droite();
			debut_cadre_relief();
				echo gros_titre('CleverMail Administration');
			fin_cadre_relief();

			$result = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_posts WHERE lst_id=".$_GET['lst_id']." AND pst_date_sent = 0"));
			if ($result['nb'] > 0) {
				debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/new.png');
					echo '<h3>'._T('cm:nouveaux_messages').' :</h3>';
					echo '<p>'._T('cm:nouveaux_messages_text').' :</p>';

				    $posts = spip_query("SELECT * FROM cm_posts WHERE lst_id=".$_GET['lst_id']." AND pst_date_sent = 0 ORDER BY pst_date_create DESC");
					while ($post = spip_fetch_array($posts)) {
						debut_cadre_formulaire();
						echo '<h4>'.$post['pst_subject'].'</h4>';

						echo '<p>';
						echo _T('cm:cree').' : '.date("m/d/Y H:i", $post['pst_date_create']).'<br />';
						if ($post['pst_date_update'] != 0) {
							echo _T('cm:modifie').' : '.date("m/d/Y H:i", $post['pst_date_update']);
						}
						echo '</p>';
				      	echo _T('cm:actions').' : ';
						echo '<a href="'.generer_url_ecrire("cm_post","pst_id=".$post['pst_id']."&mode=text").'" target="_blank">'._T('cm:apercu').' TXT</a> ';
						echo '| <a href="'.generer_url_ecrire("cm_post","pst_id=".$post['pst_id']."&mode=html").'" target="_blank">'._T('cm:apercu').' HTML</a> ';
				        echo '| <a href="'.generer_url_ecrire("cm_post_edit","pst_id=".$post['pst_id']).'">'._T('cm:modifier').'</a> ';
				        echo '| <a href="'.generer_url_ecrire("cm_posts","lst_id=".$post['lst_id']."&pst_id=".$post['pst_id']).'">'._T('cm:supprimer').'</a> ';
						echo '| <a href="'.generer_url_ecrire("cm_post_queue","pst_id=".$post['pst_id']).'">'._T('cm:envoyer').'</a>';

				   		fin_cadre_formulaire();
				   		echo '<br />';
					}
				fin_cadre_relief();
			}

			$result = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_posts p, cm_posts_queued q WHERE p.pst_id = q.pst_id AND p.lst_id=".$_GET['lst_id']));
			if ($result['nb'] > 0) {
				debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/queue.png');
					echo '<h3>'._T('cm:messages_attentes').' :</h3>';
					echo '<p>'._T('cm:messages_attentes_text').' :</p>';

				    $posts = spip_query("SELECT DISTINCT p.*, q.pst_id FROM cm_posts p, cm_posts_queued q WHERE p.pst_id = q.pst_id AND p.lst_id=".$_GET['lst_id']." ORDER BY p.pst_date_sent DESC");
					while ($post = spip_fetch_array($posts)) {
					    $postInfo = array();
					    $postInfo['sent'] = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_posts_done WHERE pst_id = ".$post['pst_id']));
					    $postInfo['sent'] = $postInfo['sent']['nb'];
					    $postInfo['queued'] = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_posts_queued WHERE pst_id = ".$post['pst_id']));
					    $postInfo['queued'] =  $postInfo['queued']['nb'];
					    debut_cadre_formulaire();
						echo '<h4>'.$post['pst_subject'].'</h4>';

						echo '<p>';
						echo _T('cm:cree').' : '.date("m/d/Y H:i", $post['pst_date_create']).'<br />';
						echo _T('cm:envoye').' : '.date("m/d/Y H:i", $post['pst_date_sent']);
						echo '</p>';
						echo '<p>';
						echo $postInfo['queued'].' message'.($postInfo['queued'] > 1 ? 's' : '')._T('cm:queue_attente');
						echo $postInfo['sent'].' message'.($postInfo['sent'] > 1 ? 's' : '')._T('cm:queue_envoye');
				        echo '</p>';

						echo _T('cm:actions').' : ';
						echo '<a href="'.generer_url_ecrire("cm_post","pst_id=".$post['pst_id']."&mode=text").'" target="_blank">'._T('cm:apercu').' TXT</a> ';
						echo '| <a href="'.generer_url_ecrire("cm_post","pst_id=".$post['pst_id']."&mode=html").'" target="_blank">'._T('cm:apercu').' HTML</a>';

						fin_cadre_formulaire();
				   		echo '<br />';
	    			}
				fin_cadre_relief();
			}

			$result = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_posts WHERE pst_date_sent!=0 AND lst_id=".$_GET['lst_id']));
			if ($result['nb'] > 0) {
				debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/sent.png');
					echo '<h3>'._T('cm:messages_envoyes').' :</h3>';
					echo '<p>'._T('cm:messages_envoyes_text').' :</p>';

				    $posts = spip_query("SELECT * FROM cm_posts WHERE pst_date_sent!=0 AND lst_id=".$_GET['lst_id']);
					while ($post = spip_fetch_array($posts)) {
					    debut_cadre_formulaire();
						echo '<h4>'.$post['pst_subject'].'</h4>';

						echo '<p>';
						echo _T('cm:cree').' : '.date("m/d/Y H:i", $post['pst_date_create']).'<br />';
						echo _T('cm:envoye').' : '.date("m/d/Y H:i", $post['pst_date_sent']);
						echo '</p>';

						echo _T('cm:actions').' : ';
						echo '<a href="'.generer_url_ecrire("cm_post","pst_id=".$post['pst_id']."&mode=text").'" target="_blank">'._T('cm:apercu').' TXT</a> ';
						echo '| <a href="'.generer_url_ecrire("cm_post","pst_id=".$post['pst_id']."&mode=html").'" target="_blank">'._T('cm:apercu').' HTML</a>';

						fin_cadre_formulaire();
				   		echo '<br />';
	    			}
				fin_cadre_relief();
			}


			icone_horizontale(_T('cm:creer_nouveau_message'), generer_url_ecrire("cm_post_edit","pst_id=-1&lst_id=".$_GET['lst_id']), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/new.png', 'creer.gif');
	fin_page();
}
?>