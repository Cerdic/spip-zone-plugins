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
include_spip("inc/utils");

function exec_clevermail_lists_edit() {

	if (isset($_POST['lst_name'])) {
		$list = $_POST;

		// Handle checkbox value
		if (isset($list['lst_subject_tag']) && ($list['lst_subject_tag'] == 'on' || $list['lst_subject_tag'] == 1)) {
		    $list['lst_subject_tag'] = 1;
		} else {
		    $list['lst_subject_tag'] = 0;
		}

		if ($list['lst_name'] != '') {
			$count = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_lists WHERE lst_id != ".$_POST['lst_id']." AND lst_name = '".$_POST['lst_name']."'"));
		    if ($count['nb'] == 0) {
				spip_desinfecte($list);

		        if ($list['lst_id'] == -1) {
		            spip_query("INSERT INTO cm_lists
		                (lst_id, lst_name, lst_comment, lst_moderation, lst_moderator_email, lst_subscribe_subject, lst_subscribe_text, lst_subject, lst_unsubscribe_subject, lst_unsubscribe_text, lst_subject_tag, lst_url_html, lst_url_text)
		                VALUES
		                ('', "._q($list['lst_name']).", "._q($list['lst_comment']).", "._q($list['lst_moderation']).", "._q($list['lst_moderator_email']).", "._q($list['lst_subscribe_subject']).", "._q($list['lst_subscribe_text']).", "._q($list['lst_subject']).", "._q($list['lst_unsubscribe_subject']).", "._q($list['lst_unsubscribe_text']).", "._q($list['lst_subject_tag']).", "._q($list['lst_url_html']).", "._q($list['lst_url_text']).")");
		        } else {
		            spip_query("UPDATE cm_lists
		                SET
		                lst_name = "._q($list['lst_name']).",
		                lst_comment = "._q($list['lst_comment']).",
		                lst_moderation = "._q($list['lst_moderation']).",
						lst_moderator_email = "._q($list['lst_moderator_email']).",
		                lst_subscribe_subject = "._q($list['lst_subscribe_subject']).",
		                lst_subscribe_text = "._q($list['lst_subscribe_text']).",
		                lst_subject = "._q($list['lst_subject']).",
		                lst_unsubscribe_subject = "._q($list['lst_unsubscribe_subject']).",
		                lst_unsubscribe_text = "._q($list['lst_unsubscribe_text']).",
		                lst_subject_tag = "._q($list['lst_subject_tag']).",
		                lst_url_html = "._q($list['lst_url_html']).",
		                lst_url_text = "._q($list['lst_url_text'])."
		                WHERE
		                lst_id = "._q($list['lst_id']));
		        }
		    } else {
		        define('_ERROR', _T('clevermail:lettre_meme_nom'));
		    }
		} else {
		    define('_ERROR', _T('clevermail:lettre_sans_nom'));
		}
		if (defined('_ERROR')) {
	        $list = $_POST;
	    } else {
	    	header('location: '.generer_url_ecrire('clevermail_index'));
	    	exit;
	    }
	}

	debut_page("CleverMail Administration", 'configuration', 'cm_index');
		echo debut_gauche('', true);
        	include_spip("inc/clevermail_menu");
			echo '<br />';
			debut_cadre_relief();
				echo '<strong>'._T('clevermail:tags_specifiques').' :</strong><br />';
				echo '@@NOM_LETTRE@@<br />';
				echo '@@DESCRIPTION@@<br />';
				echo '@@FORMAT_INSCRIPTION@@<br />';
				echo '@@EMAIL@@<br />';
				echo '@@URL_CONFIRMATION@@<br />';
				echo '@@URL_DESINSCRIPTION@@<br />';
			fin_cadre_relief();
		echo debut_droite('', true);
			debut_cadre_relief();
				echo gros_titre('CleverMail Administration', '', '');
			fin_cadre_relief();

			debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/lettre-24.png');
				if (isset($_GET['id'])) {
					if ((int)$_GET['id'] == -1) {
					    $list = array(
					        'lst_id' => -1,
					        'lst_name' => '',
					        'lst_comment' => '',
					        'lst_moderation' => 'closed',
							'lst_moderator_email' => '',
					        'lst_subscribe_subject' => _T('clevermail:confirmation_votre_inscription'),
					        'lst_subscribe_text' => _T('clevermail:confirmation_votre_inscription_text'),
					        'lst_subject' => '',
					        'lst_unsubscribe_subject' => _T('clevermail:confirmation_votre_desinscription'),
					        'lst_unsubscribe_text' => _T('clevermail:confirmation_votre_desinscription_text'),
					        'lst_subject_tag' => 1,
					        'lst_url_html' => "http://",
					        'lst_url_text' => "http://");
					    $cm_mail_admin = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_MAIL_ADMIN'"));
					    $list['lst_moderator_email'] = $cm_mail_admin['set_value'];
					} else {
					    $list = spip_fetch_array(spip_query("SELECT * FROM cm_lists WHERE lst_id = ".$_GET['id']));
				    }
				}
				if ($list['lst_id'] == -1) {
				   	echo '<h3>'._T('clevermail:creer_lettre').' :</h3>';
				} else {
				    echo '<h3>'._T('clevermail:editer_lettre').' : '.$list['lst_name'].'</h3>';
				}

				if (defined('_ERROR')) {
				    echo '<p class="error">'._ERROR.'</p>';
				}

				if (is_array($list)) {
?>
					<form action="<?php echo generer_url_ecrire('clevermail_lists_edit',''); ?>" method="post">
				        <?php echo debut_cadre_formulaire('', true) ?>
				        	<h4><?php echo _T('clevermail:configuration_generale') ?></h4>
				       		<input type="hidden" name="lst_id" value="<?=$list['lst_id']?>" />

					        <label><?php echo _T('clevermail:nom') ?> :</label>
				            <input type="text" name="lst_name" value="<?=$list['lst_name']?>" size="50" maxlength="255" class="formo" /><br />

				            <label><?php echo _T('clevermail:description') ?> :</label>
				            <textarea name="lst_comment" cols="50" rows="5" wrap="virtual" class="formo"><?=$list['lst_comment']?></textarea><br />

				            <label><?php echo _T('clevermail:moderation') ?> :</label>
			                <select name="lst_moderation" class="formo">
			                <?php
			                $modes = array(
			                    'open' => _T('clevermail:mod_open'),
			                    'email' => _T('clevermail:mod_email'),
			                    'mod' => _T('clevermail:mod_mod'),
			                    'closed' => _T('clevermail:mod_closed'));
			                while (list($value, $label) = each($modes)) {
			                    echo '<option value="'.$value.'"';
			                    if ($value == $list['lst_moderation']) echo ' selected="selected"';
			                    echo '>'.$label.'</option>';
			                }
			                ?>
			                </select><br />

				            <label><?php echo _T('clevermail:email_moderateur') ?> :</label>
				            <input type="text" name="lst_moderator_email" value="<?=$list['lst_moderator_email']?>" size="50" maxlength="255" class="formo" /><br />

				            <input type="checkbox" value="1" name="lst_subject_tag" <?=($list['lst_subject_tag'] == 1 ? 'checked="checked"' : '')?>/>
				            <label><?php echo _T('clevermail:prefixer_messages') ?></label>
				        <?php echo fin_cadre_formulaire(true) ?>
						<br />
						<?php echo debut_cadre_formulaire('', true) ?>
					        <h4><?php echo _T('clevermail:confirmation_inscription') ?></h4>

					        <label><?php echo _T('clevermail:sujet') ?> :</label>
					        <input type="text" name="lst_subscribe_subject" value="<?=$list['lst_subscribe_subject']?>" size="50" maxlength="255" class="formo" /><br />

					        <label><?php echo _T('clevermail:description') ?> :</label>
					        <textarea name="lst_subscribe_text" cols="50" rows="10" wrap="virtual" class="formo"><?=$list['lst_subscribe_text']?></textarea>
						<?php echo fin_cadre_formulaire(true) ?>
						<br />
						<?php echo debut_cadre_formulaire('', true) ?>
					        <h4><?php echo _T('clevermail:confirmation_desinscription') ?></h4>

					        <label><?php echo _T('clevermail:sujet') ?> :</label>
					        <input type="text" name="lst_unsubscribe_subject" value="<?=$list['lst_unsubscribe_subject']?>" size="50" maxlength="255" class="formo" /><br />

					        <label><?php echo _T('clevermail:description') ?> :</label>
							<textarea name="lst_unsubscribe_text" cols="50" rows="10" wrap="virtual" class="formo"><?=$list['lst_unsubscribe_text']?></textarea>
						<?php echo fin_cadre_formulaire(true) ?>
						<br />
						<?php echo debut_cadre_formulaire('', true) ?>
							<h4><?php echo _T('clevermail:url_templates') ?></strong></h4>
							<label><?php echo _T('clevermail:version_html') ?> :</label>
					        <input type="text" name="lst_url_html" value="<?=$list['lst_url_html']?>" size="50" maxlength="255" class="formo" /><br />
					        <label><?php echo _T('clevermail:version_txt') ?> :</label>
					        <input type="text" name="lst_url_text" value="<?=$list['lst_url_text']?>" size="50" maxlength="255" class="formo" />
						<?php echo fin_cadre_formulaire(true) ?>
						<br />
						<div style="text-align: right">
							<input type="reset" value="<?php echo _T('clevermail:annuler') ?>" class="fondo" />
							<input type="submit" value="<?=($list['lst_id'] == -1 ? _T('clevermail:creer') : _T('clevermail:modifier'))?>" class="fondo" />
						</div>
					</form>
<?php
				} else {
				    echo '<p class="error">'._T('clevermail:mauvais_identifiant_lettre').'</p>';
				}
			fin_cadre_relief();
	fin_page();
}
?>