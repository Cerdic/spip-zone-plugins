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
include_spip("inc/distant");

function exec_clevermail_post_edit() {

	if (isset($_POST['pst_subject'])) {
		$post = $_POST;
		if ($post['pst_subject'] != '') {
			$post['pst_subject'] = addslashes($post['pst_subject']);

			$list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id = ".$post['lst_id']));
			$post['pst_html'] = addslashes(recuperer_page($list['lst_url_html']));
			$post['pst_text'] = addslashes(recuperer_page($list['lst_url_text']));

			if ($post['pst_id'] == -1) {
				spip_query("INSERT INTO cm_posts
					(pst_id, lst_id, pst_date_create, pst_subject, pst_html, pst_text)
					VALUES
					('', ".$post['lst_id'].", ".time().", '".$post['pst_subject']."', '".$post['pst_html']."', '".$post['pst_text']."')");
			} else {
				spip_query("UPDATE cm_posts
					SET
					pst_date_update = ".time().",
					pst_subject = '".$post['pst_subject']."',
					pst_html = '".$post['pst_html']."',
					pst_text = '".$post['pst_text']."'
					WHERE
					pst_id = ".$post['pst_id']);
			}
		} else {
			define('_ERROR', _T('clevermail:sujet_vide'));
		}
		if (defined('_ERROR')) {
			$post = $_POST;
		} else {
			header('Location: '.generer_url_ecrire('clevermail_posts','').'&lst_id='.$post['lst_id']);
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

			debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/new.png');

				if (isset($_GET['pst_id'])) {
				    if ((int)$_GET['pst_id'] == -1) {
				        $post = array(
				            'pst_id' => -1,
				            'lst_id' => $_GET['lst_id'],
				            'pst_subject' => '');
				    } else {
				        $post = spip_fetch_array(spip_query("SELECT * FROM cm_posts WHERE pst_id = ".$_GET['pst_id']));
				    }
				}

				if ($post['pst_id'] == -1) {
				    echo '<h3>'._T('clevermail:creer_message').' :</h3>';
				} else {
				    echo '<h3>'._T('clevermail:modifier_message').' :</h3>';
				}

				if (defined('_ERROR')) {
				    echo '<p class="error">'._ERROR.'</p>';
				}
?>
				<form action="<?php echo generer_url_ecrire('clevermail_post_edit',''); ?>" method="post">
				<input type="hidden" name="pst_id" value="<?=$post['pst_id']?>" />
        		<input type="hidden" name="lst_id" value="<?=$post['lst_id']?>" />
				<?php echo debut_cadre_formulaire('', true); ?>
					<label><?php echo _T('clevermail:sujet_message') ?> :</label>
					<input type="text" name="pst_subject" value="<?=$post['pst_subject']?>" size="50" maxlength="255" class="formo" />
				<?php echo fin_cadre_formulaire(true); ?>
				<br />
				<div style="text-align: right">
					<input type="submit" value="<?=($post['pst_id'] == -1 ? _T('clevermail:creer') : _T('clevermail:modifier_submit'))?>" class="fondo"  />
				</div>
				</form>
<?php
			fin_cadre_relief();
	fin_page();
}
?>