<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information bas� sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

function exec_clevermail_post() {
	if (isset($_GET['pst_id'])) {
		$post = spip_fetch_array(spip_query("SELECT * FROM cm_posts WHERE pst_id = ".$_GET['pst_id']));
		if (is_array($post)) {
			$list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id = ".$post['lst_id']));
			if (isset($_GET['mode']) && $_GET['mode']=='html') {
				header('Content-type: text/html; charset='.lire_meta('charset'));
				echo $post['pst_html'];
			} else {
				header('Content-type: text/plain; charset='.lire_meta('charset'));
				echo $post['pst_text'];
			}
		} else {
		    echo 'Invalid post identifier.';
		}
	}
}
?>