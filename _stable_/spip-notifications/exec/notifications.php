<?php


	/**
	 * SPIP-Notifications : Notifications au format HTML, mixte ou texte et envoi via mail (PHP) ou SMTP
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
	include_spip('inc/presentation');
	include_spip('meteo_fonctions');
	include_spip('inc/config');
	include_spip('inc/meta');
	include_spip('inc/notifications_classes');


	function exec_notifications() {

		if (!autoriser('configurer', 'notifications')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		pipeline('exec_init',array('args'=>array('exec'=>'notifications'),'data'=>''));

		if ($_GET['purger'] == 'oui') {
			sql_query('TRUNCATE TABLE spip_notifications');
			$url = generer_url_ecrire('notifications');
			header('Location: ' . $url);
			exit();
		}

		if (!empty($_POST['valider']) or !empty($_POST['tester'])) {
			if (isset($_POST['spip_notifications_adresse_envoi'])) {
				$spip_notifications_adresse_envoi = $_POST['spip_notifications_adresse_envoi'];
				ecrire_meta('spip_notifications_adresse_envoi', $spip_notifications_adresse_envoi);
			}

			if (isset($_POST['spip_notifications_adresse_envoi_nom'])) {
				$spip_notifications_adresse_envoi_nom = addslashes($_POST['spip_notifications_adresse_envoi_nom']);
				ecrire_meta('spip_notifications_adresse_envoi_nom', $spip_notifications_adresse_envoi_nom);
			}

			if (isset($_POST['spip_notifications_adresse_envoi_email'])) {
				$spip_notifications_adresse_envoi_email = addslashes($_POST['spip_notifications_adresse_envoi_email']);
				ecrire_meta('spip_notifications_adresse_envoi_email', $spip_notifications_adresse_envoi_email);
			}

			if (isset($_POST['spip_notifications_smtp'])) {
				$spip_notifications_smtp = $_POST['spip_notifications_smtp'];
				ecrire_meta('spip_notifications_smtp', $spip_notifications_smtp);
			}

			if (isset($_POST['spip_notifications_smtp_host'])) {
				$spip_notifications_smtp_host = addslashes($_POST['spip_notifications_smtp_host']);
				ecrire_meta('spip_notifications_smtp_host', $spip_notifications_smtp_host);
			}

			if (isset($_POST['spip_notifications_smtp_port'])) {
				$spip_notifications_smtp_port = addslashes($_POST['spip_notifications_smtp_port']);
				ecrire_meta('spip_notifications_smtp_port', $spip_notifications_smtp_port);
			}

			if (isset($_POST['spip_notifications_smtp_auth'])) {
				$spip_notifications_smtp_auth = $_POST['spip_notifications_smtp_auth'];
				ecrire_meta('spip_notifications_smtp_auth', $spip_notifications_smtp_auth);
			}

			if (isset($_POST['spip_notifications_smtp_username'])) {
				$spip_notifications_smtp_username = addslashes($_POST['spip_notifications_smtp_username']);
				ecrire_meta('spip_notifications_smtp_username', $spip_notifications_smtp_username);
			}

			if (isset($_POST['spip_notifications_smtp_password'])) {
				$spip_notifications_smtp_password = addslashes($_POST['spip_notifications_smtp_password']);
				ecrire_meta('spip_notifications_smtp_password', $spip_notifications_smtp_password);
			}

			if (isset($_POST['spip_notifications_smtp_secure'])) {
				$spip_notifications_smtp_secure = $_POST['spip_notifications_smtp_secure'];
				ecrire_meta('spip_notifications_smtp_secure', $spip_notifications_smtp_secure);
			}

			if (isset($_POST['spip_notifications_smtp_sender'])) {
				$spip_notifications_smtp_sender = addslashes($_POST['spip_notifications_smtp_sender']);
				ecrire_meta('spip_notifications_smtp_sender', $spip_notifications_smtp_sender);
			}

			ecrire_meta('spip_notifications_filtre_images', intval($_POST['spip_notifications_filtre_images']));
			ecrire_meta('spip_notifications_filtre_css', intval($_POST['spip_notifications_filtre_css']));
			ecrire_meta('spip_notifications_filtre_iso_8859', intval($_POST['spip_notifications_filtre_iso_8859']));

			ecrire_metas();
		}

		$spip_notifications_adresse_envoi		= $GLOBALS['meta']['spip_notifications_adresse_envoi'];
		$spip_notifications_adresse_envoi_nom	= $GLOBALS['meta']['spip_notifications_adresse_envoi_nom'];
		$spip_notifications_adresse_envoi_email	= $GLOBALS['meta']['spip_notifications_adresse_envoi_email'];
		$spip_notifications_smtp				= $GLOBALS['meta']['spip_notifications_smtp'];
		$spip_notifications_smtp_host			= $GLOBALS['meta']['spip_notifications_smtp_host'];
		$spip_notifications_smtp_port			= $GLOBALS['meta']['spip_notifications_smtp_port'];
		$spip_notifications_smtp_auth			= $GLOBALS['meta']['spip_notifications_smtp_auth'];
		$spip_notifications_smtp_username		= $GLOBALS['meta']['spip_notifications_smtp_username'];
		$spip_notifications_smtp_password		= $GLOBALS['meta']['spip_notifications_smtp_password'];
		$spip_notifications_smtp_secure			= $GLOBALS['meta']['spip_notifications_smtp_secure'];
		$spip_notifications_smtp_sender			= $GLOBALS['meta']['spip_notifications_smtp_sender'];
		$spip_notifications_filtre_images		= $GLOBALS['meta']['spip_notifications_filtre_images'];
		$spip_notifications_filtre_css			= $GLOBALS['meta']['spip_notifications_filtre_css'];
		$spip_notifications_filtre_accents		= $GLOBALS['meta']['spip_notifications_filtre_accents'];
		$spip_notifications_filtre_iso_8859		= $GLOBALS['meta']['spip_notifications_filtre_iso_8859'];

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");

		echo "<br /><br /><br />\n";
		echo gros_titre(_T('titre_configuration'),'',false);
		echo barre_onglets("configuration", "notifications");

		echo debut_gauche('', true);

		$iconifier = charger_fonction('iconifier', 'inc');
		echo $iconifier('id_notification', 0, 'notifications');

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'notifications'),'data'=>''));

		echo bloc_des_raccourcis(icone_horizontale(_T('notifications:purger'), generer_url_ecrire("notifications", "purger=oui"), _DIR_PLUGIN_NOTIFICATIONS."prive/images/purger.png", 'rien.gif', false));

		echo debut_droite('', true);

		echo '<form method="post" action="'.generer_url_ecrire('notifications').'" >';

		echo debut_cadre_trait_couleur("", true, "", _T('notifications:configuration_adresse_envoi'));

		echo '<p>';
		echo bouton_radio("spip_notifications_adresse_envoi", "non", _T('notifications:utiliser_reglages_site'), $spip_notifications_adresse_envoi == "non", "changeVisible(this.checked, 'adresse_envoi', 'none', 'block');");
		echo "<br />";
		echo bouton_radio("spip_notifications_adresse_envoi", "oui", _T('notifications:personnaliser'), $spip_notifications_adresse_envoi == "oui", "changeVisible(this.checked, 'adresse_envoi', 'block', 'none');");
		echo '</p>';

		if ($spip_notifications_adresse_envoi == "oui") $style = "display: block;";
		else $style = "display: none;";
		echo "<ul id='adresse_envoi' style='$style'>";
		echo "<li>"._T('notifications:spip_notifications_adresse_envoi_nom')." <input type='text' name='spip_notifications_adresse_envoi_nom' value='$spip_notifications_adresse_envoi_nom' size='30' class='fondl' /></li>";
		echo "<li>"._T('notifications:spip_notifications_adresse_envoi_email')." <input type='text' name='spip_notifications_adresse_envoi_email' value='$spip_notifications_adresse_envoi_email' size='30' class='fondl' /></li>";
		echo "</ul>";

		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('notifications:valider').'" /></p>';

		echo fin_cadre_trait_couleur(true);

		echo debut_cadre_trait_couleur("", true, "", _T('notifications:configuration_mailer'));

		echo debut_cadre_relief("", false, "", _T('notifications:configuration_smtp'));
		echo '<p>'._T('notifications:configuration_smtp_descriptif').'</p>';
		echo '<p>';
		echo bouton_radio("spip_notifications_smtp", "non", _T('notifications:utiliser_mail'), $spip_notifications_smtp == "non", "changeVisible(this.checked, 'smtp', 'none', 'block');");
		echo "<br />";
		echo bouton_radio("spip_notifications_smtp", "oui", _T('notifications:utiliser_smtp'), $spip_notifications_smtp == "oui", "changeVisible(this.checked, 'smtp', 'block', 'none');");
		echo '</p>';

		if ($spip_notifications_smtp == "oui") $style = "display: block;";
		else $style = "display: none;";
		echo "<ul id='smtp' style='$style'>";
		echo "<li>"._T('notifications:spip_notifications_smtp_host')." <input type='text' name='spip_notifications_smtp_host' value='$spip_notifications_smtp_host' size='30' class='fondl' /></li>";
		echo "<li>"._T('notifications:spip_notifications_smtp_port')." <input type='text' name='spip_notifications_smtp_port' value='$spip_notifications_smtp_port' size='4' class='fondl' /></li>";
		echo "<li>"._T('notifications:spip_notifications_smtp_auth');
		echo bouton_radio("spip_notifications_smtp_auth", "oui", _T('notifications:spip_notifications_smtp_auth_oui'), $spip_notifications_smtp_auth == "oui", "changeVisible(this.checked, 'smtp-auth', 'block', 'none');");
		echo "&nbsp;";
		echo bouton_radio("spip_notifications_smtp_auth", "non", _T('notifications:spip_notifications_smtp_auth_non'), $spip_notifications_smtp_auth == "non", "changeVisible(this.checked, 'smtp-auth', 'none', 'block');");

		if ($spip_notifications_smtp_auth == "oui") $style = "display: block;";
		else $style = "display: none;";
		echo "<ul id='smtp-auth' style='$style'>";
		echo "<li>"._T('notifications:spip_notifications_smtp_username')." <input type='text' name='spip_notifications_smtp_username' value='$spip_notifications_smtp_username' size='30' class='fondl' /></li>";
		echo "<li>"._T('notifications:spip_notifications_smtp_password')." <input type='password' name='spip_notifications_smtp_password' value='$spip_notifications_smtp_password' size='30' class='fondl' /></li>";
		echo "</ul>";

		echo '</li>';
		
		echo "<li>"._T('notifications:spip_notifications_smtp_secure');
		echo bouton_radio("spip_notifications_smtp_secure", "non", _T('notifications:spip_notifications_smtp_secure_non'), $spip_notifications_smtp_secure == "non");
		echo "&nbsp;";
		echo bouton_radio("spip_notifications_smtp_secure", "ssl", _T('notifications:spip_notifications_smtp_secure_ssl'), $spip_notifications_smtp_secure == "ssl");
		echo "&nbsp;";
		echo bouton_radio("spip_notifications_smtp_secure", "tls", _T('notifications:spip_notifications_smtp_secure_tls'), $spip_notifications_smtp_secure == "tls");
		echo '</li>';

		echo "</ul>";

		echo fin_cadre_relief(true);

		echo debut_cadre_relief("", true, "", _T('notifications:spip_notifications_smtp_sender'));
		echo '<p>'._T('notifications:spip_notifications_smtp_sender_descriptif').'</p>';
		echo "<p><input type='text' name='spip_notifications_smtp_sender' value=\"".$spip_notifications_smtp_sender."\" size='40' class='forml' /></p>";
		echo fin_cadre_relief(true);

		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('notifications:valider').'" /></p>';

		echo fin_cadre_trait_couleur(true);
		
		echo debut_cadre_trait_couleur("", true, "", _T('notifications:spip_notifications_filtres'));
		echo '<p>'._T('notifications:spip_notifications_filtres_descriptif').'</p>';
		echo '<p>';
		echo "<input type='checkbox' id='spip_notifications_filtre_images' name='spip_notifications_filtre_images' value=\"1\" ".($spip_notifications_filtre_images ? "checked='checked'" : '')." />";
		echo '<label for="spip_notifications_filtre_images">'._T('notifications:spip_notifications_filtre_images').'</label>';
		echo '</p>';
		echo '<p>';
		echo "<input type='checkbox' id='spip_notifications_filtre_css' name='spip_notifications_filtre_css' value=\"1\" ".($spip_notifications_filtre_css ? "checked='checked'" : '')." />";
		echo '<label for="spip_notifications_filtre_css">'._T('notifications:spip_notifications_filtre_css').'</label>';
		echo '</p>';
		if ($GLOBALS['meta']['charset'] == 'utf-8') {
			echo '<p>';
			echo "<input type='checkbox' id='spip_notifications_filtre_iso_8859' name='spip_notifications_filtre_iso_8859' value=\"1\" ".($spip_notifications_filtre_iso_8859 ? "checked='checked'" : '')." />";
			echo '<label for="spip_notifications_filtre_iso_8859">'._T('notifications:spip_notifications_filtre_iso_8859').'</label>';
			echo '</p>';
		}
		echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('notifications:valider').'" /></p>';

		echo fin_cadre_trait_couleur(true);
		
		echo debut_cadre_trait_couleur("", true, "", _T('notifications:tester_la_configuration'));
		if (!empty($_POST['tester'])) {
			if ($GLOBALS['meta']['spip_notifications_adresse_envoi'] == 'oui')
				$destinataire = $GLOBALS['meta']['spip_notifications_adresse_envoi_email'];
			else
				$destinataire = $GLOBALS['meta']['email_webmaster'];
			$message_html	= recuperer_fond('notifications/notification_test_html', array());
			$message_texte	= recuperer_fond('notifications/notification_test_texte', array());
			$test = new Notification($destinataire, _T('notifications:notification_de_test'), $message_html, $message_texte);
			if (!$test->Send()) {
  			  echo '<p>'._T('notifications:erreur').' : '.$test->ErrorInfo.'</p>';
			} else {
			  echo '<p>'._T('notifications:notification_envoyee').'</p>';
			}
		} else {
			echo '<p>'._T('notifications:note_test_configuration').'</p>';
			echo '<p style="text-align: right;"><input class="fondo" name="tester" type="submit" value="'._T('notifications:tester').'" /></p>';
		}
		echo fin_cadre_trait_couleur(true);

		echo '</form>';



		echo pipeline('affiche_milieu', array('args'=>array('exec'=>'config_lettres_formulaire'),'data'=>''));
		
		echo fin_gauche();

		echo fin_page();

	}


?>