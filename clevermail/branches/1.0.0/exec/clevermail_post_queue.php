<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

function exec_clevermail_post_queue() {

	$result = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_posts WHERE pst_id = ".$_GET['pst_id']." AND pst_date_sent = 0"));
	if (isset($_GET['pst_id']) && $result['nb'] == 1) {
		$lst_id = spip_fetch_array(spip_query("SELECT lst_id FROM cm_posts WHERE pst_id = ".$_GET['pst_id']));
		$lst_id = $lst_id['lst_id'];
		$subscribers = spip_query("SELECT sub_id FROM cm_lists_subscribers WHERE lst_id = ".$lst_id);
		while ($sub = spip_fetch_array($subscribers)) {
			spip_query("INSERT INTO cm_posts_queued (pst_id, sub_id, psq_date) VALUES (".$_GET['pst_id'].", ".$sub['sub_id'].", ".time().")");
		}
		spip_query("UPDATE cm_posts SET pst_date_sent = ".time()." WHERE pst_id = ".$_GET['pst_id']);
	}
	header('Location: '.generer_url_ecrire('clevermail_posts','').'&lst_id='.$lst_id);
}
?>