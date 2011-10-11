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

function exec_clevermail_settings() {
	if($_POST) {
		spip_query("UPDATE cm_settings SET set_value = '".$_POST['CM_MAIL_ADMIN']."' WHERE set_name='CM_MAIL_ADMIN'");
		spip_query("UPDATE cm_settings SET set_value = '".$_POST['CM_MAIL_FROM']."' WHERE set_name='CM_MAIL_FROM'");
		spip_query("UPDATE cm_settings SET set_value = '".$_POST['CM_SEND_NUMBER']."' WHERE set_name='CM_SEND_NUMBER'");
	}
	debut_page("CleverMail Administration", 'configuration', 'cm_index');

	echo debut_gauche('', true);
		include_spip("inc/clevermail_menu");
		echo '<br />';
		debut_cadre_relief();
			echo _T('clevermail:info_parametres');
		fin_cadre_relief();
	echo debut_droite('', true);

	debut_cadre_relief();
		echo gros_titre('CleverMail Administration', '', '');
	fin_cadre_relief();

	debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/configuration.png');

		$cm_mail_admin = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_MAIL_ADMIN'"));
		$cm_mail_from = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_MAIL_FROM'"));
		$cm_send_number = spip_fetch_array(spip_query("SELECT set_value FROM cm_settings WHERE set_name='CM_SEND_NUMBER'"));

		echo '<h3>'._T('clevermail:parametres').' :</h3>';
?>
		<form action="<?php echo generer_url_ecrire('clevermail_settings',''); ?>" method="post">
			<?php echo debut_cadre_formulaire('', true) ?>
			<label><?php echo _T('clevermail:email_administrateur') ?> :</label><br />
			<input type="text" name="CM_MAIL_ADMIN" value="<?php echo $cm_mail_admin['set_value'] ?>" class="formo" /><br />
			<label><?php echo _T('clevermail:email_expediteur') ?> :</label><br />
			<input type="text" name="CM_MAIL_FROM" value="<?php echo $cm_mail_from['set_value'] ?>" class="formo" /><br />
			<label><?php echo _T('clevermail:nombre_messages') ?> :</label><br />
			<input type="text" name="CM_SEND_NUMBER" value="<?php echo $cm_send_number['set_value'] ?>" class="formo" /><br />
			<?php echo fin_cadre_formulaire(true) ?>
			<br />
			<div style="text-align: right">
				<input type="submit" value="<?php echo _T('clevermail:modifier') ?>" class="fondo" />
			</div>
		</form>
<?php
	fin_cadre_relief();

	fin_page();
}
?>
