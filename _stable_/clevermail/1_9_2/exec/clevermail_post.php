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
		if (isset($_GET['mode'])) {
			$mode = $_GET['mode'];
		} else {
			$mode = 'text';
		}
		if ($mode == 'text') {
			header('Content-type: text/plain; charset='.lire_meta('charset'));
		} else {
			header('Content-type: text/html; charset='.lire_meta('charset'));
		}
		$post = spip_fetch_array(spip_query("SELECT * FROM cm_posts WHERE pst_id = ".$_GET['pst_id']));
		if (is_array($post)) {
			$list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id = ".$post['lst_id']));
			include_spip('inc/filtres');
			if ($mode == 'text') {
				echo wordwrap(liens_absolus($post['pst_text'],dirname($list['lst_url_text'])), 70);
			} else {
				echo liens_absolus($post['pst_html'],dirname($list['lst_url_html']));
			}
		} else {
		    echo 'Invalid post identifier.';
		}
	}
}
?>