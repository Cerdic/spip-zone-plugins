<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information bas� sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006
	 *
	 **/

function exec_cm_post() {

	if (isset($_GET['pst_id'])) {
		if (isset($_GET['mode'])) {
			$mode = $_GET['mode'];
		} else {
			$mode = 'text';
		}
		if ($mode == 'text') {
			header('Content-type: text/plain');
		}
		$post = spip_fetch_array(spip_query("SELECT * FROM cm_posts WHERE pst_id = ".$_GET['pst_id']));
		if (is_array($post)) {
			$list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id = ".$post['lst_id']));
			if ($mode == 'text') {
				$text = $post['pst_text'];
				echo wordwrap($text, 70);
			} else {
				echo $post['pst_html'];
			}
		} else {
		    echo 'Invalid post identifier.';
		}
	}
}
?>