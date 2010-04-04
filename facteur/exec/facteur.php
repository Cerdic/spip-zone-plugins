<?php
/*
 * Plugin Facteur
 * (c) 2009-2010 Collectif SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/meta');
include_spip('inc/facteur_classes');


function exec_facteur() {

	if (!autoriser('configurer', 'facteur')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	pipeline('exec_init',array('args'=>array('exec'=>'facteur'),'data'=>''));

	if (!is_null(_request('valider')) or !is_null(_request('tester'))) {
		if (!is_null(_request('facteur_adresse_envoi'))) {
			$facteur_adresse_envoi = _request('facteur_adresse_envoi');
			ecrire_meta('facteur_adresse_envoi', $facteur_adresse_envoi);
		}

		if (!is_null(_request('facteur_adresse_envoi_nom'))) {
			$facteur_adresse_envoi_nom = addslashes(_request('facteur_adresse_envoi_nom'));
			ecrire_meta('facteur_adresse_envoi_nom', $facteur_adresse_envoi_nom);
		}

		if (!is_null(_request('facteur_adresse_envoi_email'))) {
			$facteur_adresse_envoi_email = addslashes(_request('facteur_adresse_envoi_email'));
			ecrire_meta('facteur_adresse_envoi_email', $facteur_adresse_envoi_email);
		}

		if (!is_null(_request('facteur_smtp'))) {
			$facteur_smtp = _request('facteur_smtp');
			ecrire_meta('facteur_smtp', $facteur_smtp);
		}

		if (!is_null(_request('facteur_smtp_host'))) {
			$facteur_smtp_host = addslashes(_request('facteur_smtp_host'));
			ecrire_meta('facteur_smtp_host', $facteur_smtp_host);
		}

		if (!is_null(_request('facteur_smtp_port'))) {
			$facteur_smtp_port = addslashes(_request('facteur_smtp_port'));
			ecrire_meta('facteur_smtp_port', $facteur_smtp_port);
		}

		if (!is_null(_request('facteur_smtp_auth'))) {
			$facteur_smtp_auth = _request('facteur_smtp_auth');
			ecrire_meta('facteur_smtp_auth', $facteur_smtp_auth);
		}

		if (!is_null(_request('facteur_smtp_username'))) {
			$facteur_smtp_username = addslashes(_request('facteur_smtp_username'));
			ecrire_meta('facteur_smtp_username', $facteur_smtp_username);
		}

		if (!is_null(_request('facteur_smtp_password'))) {
			$facteur_smtp_password = addslashes(_request('facteur_smtp_password'));
			ecrire_meta('facteur_smtp_password', $facteur_smtp_password);
		}

		if (intval(phpversion()) == 5) {
			if (!is_null(_request('facteur_smtp_secure'))) {
				$facteur_smtp_secure = _request('facteur_smtp_secure');
				ecrire_meta('facteur_smtp_secure', $facteur_smtp_secure);
			}
		}

		if (!is_null(_request('facteur_smtp_sender'))) {
			$facteur_smtp_sender = addslashes(_request('facteur_smtp_sender'));
			ecrire_meta('facteur_smtp_sender', $facteur_smtp_sender);
		}

		ecrire_meta('facteur_filtre_images', intval(_request('facteur_filtre_images')));
		ecrire_meta('facteur_filtre_css', intval(_request('facteur_filtre_css')));
		ecrire_meta('facteur_filtre_iso_8859', intval(_request('facteur_filtre_iso_8859')));

		ecrire_metas();
	}

	$facteur_adresse_envoi			= $GLOBALS['meta']['facteur_adresse_envoi'];
	$facteur_adresse_envoi_nom		= $GLOBALS['meta']['facteur_adresse_envoi_nom'];
	$facteur_adresse_envoi_email	= $GLOBALS['meta']['facteur_adresse_envoi_email'];
	$facteur_smtp					= $GLOBALS['meta']['facteur_smtp'];
	$facteur_smtp_host				= $GLOBALS['meta']['facteur_smtp_host'];
	$facteur_smtp_port				= $GLOBALS['meta']['facteur_smtp_port'];
	$facteur_smtp_auth				= $GLOBALS['meta']['facteur_smtp_auth'];
	$facteur_smtp_username			= $GLOBALS['meta']['facteur_smtp_username'];
	$facteur_smtp_password			= $GLOBALS['meta']['facteur_smtp_password'];
	if (intval(phpversion()) == 5)
		$facteur_smtp_secure		= $GLOBALS['meta']['facteur_smtp_secure'];
	$facteur_smtp_sender			= $GLOBALS['meta']['facteur_smtp_sender'];
	$facteur_filtre_images			= $GLOBALS['meta']['facteur_filtre_images'];
	$facteur_filtre_css				= $GLOBALS['meta']['facteur_filtre_css'];
	$facteur_filtre_iso_8859		= $GLOBALS['meta']['facteur_filtre_iso_8859'];

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");

	echo "<br /><br /><br />\n";
	echo gros_titre(_T('titre_configuration'),'',false);
	echo barre_onglets("configuration", "facteur");

	echo debut_gauche('', true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'facteur'),'data'=>''));

	echo debut_droite('', true);

	echo '<form method="post" action="'.generer_url_ecrire('facteur').'" >';

	echo debut_cadre_trait_couleur("", true, "", _T('facteur:configuration_adresse_envoi'));

	echo '<p>';
	echo bouton_radio("facteur_adresse_envoi", "non", _T('facteur:utiliser_reglages_site'), $facteur_adresse_envoi == "non", "changeVisible(this.checked, 'adresse_envoi', 'none', 'block');");
	echo "<br />";
	echo bouton_radio("facteur_adresse_envoi", "oui", _T('facteur:personnaliser'), $facteur_adresse_envoi == "oui", "changeVisible(this.checked, 'adresse_envoi', 'block', 'none');");
	echo '</p>';

	if ($facteur_adresse_envoi == "oui") $style = "display: block;";
	else $style = "display: none;";
	echo "<ul id='adresse_envoi' style='$style'>";
	echo "<li>"._T('facteur:facteur_adresse_envoi_nom')." <input type='text' name='facteur_adresse_envoi_nom' value='$facteur_adresse_envoi_nom' size='30' class='fondl' /></li>";
	echo "<li>"._T('facteur:facteur_adresse_envoi_email')." <input type='text' name='facteur_adresse_envoi_email' value='$facteur_adresse_envoi_email' size='30' class='fondl' /></li>";
	echo "</ul>";

	echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('facteur:valider').'" /></p>';

	echo fin_cadre_trait_couleur(true);

	echo debut_cadre_trait_couleur("", true, "", _T('facteur:configuration_mailer'));

	echo debut_cadre_relief("", false, "", _T('facteur:configuration_smtp'));
	echo '<p>'._T('facteur:configuration_smtp_descriptif').'</p>';
	echo '<p>';
	echo bouton_radio("facteur_smtp", "non", _T('facteur:utiliser_mail'), $facteur_smtp == "non", "changeVisible(this.checked, 'smtp', 'none', 'block');");
	echo "<br />";
	echo bouton_radio("facteur_smtp", "oui", _T('facteur:utiliser_smtp'), $facteur_smtp == "oui", "changeVisible(this.checked, 'smtp', 'block', 'none');");
	echo '</p>';

	if ($facteur_smtp == "oui") $style = "display: block;";
	else $style = "display: none;";
	echo "<ul id='smtp' style='$style'>";
	echo "<li>"._T('facteur:facteur_smtp_host')." <input type='text' name='facteur_smtp_host' value='$facteur_smtp_host' size='30' class='fondl' /></li>";
	echo "<li>"._T('facteur:facteur_smtp_port')." <input type='text' name='facteur_smtp_port' value='$facteur_smtp_port' size='4' class='fondl' /></li>";
	echo "<li>"._T('facteur:facteur_smtp_auth');
	echo bouton_radio("facteur_smtp_auth", "oui", _T('facteur:facteur_smtp_auth_oui'), $facteur_smtp_auth == "oui", "changeVisible(this.checked, 'smtp-auth', 'block', 'none');");
	echo "&nbsp;";
	echo bouton_radio("facteur_smtp_auth", "non", _T('facteur:facteur_smtp_auth_non'), $facteur_smtp_auth == "non", "changeVisible(this.checked, 'smtp-auth', 'none', 'block');");

	if ($facteur_smtp_auth == "oui") $style = "display: block;";
	else $style = "display: none;";
	echo "<ul id='smtp-auth' style='$style'>";
	echo "<li>"._T('facteur:facteur_smtp_username')." <input type='text' name='facteur_smtp_username' value='$facteur_smtp_username' size='30' class='fondl' /></li>";
	echo "<li>"._T('facteur:facteur_smtp_password')." <input type='password' name='facteur_smtp_password' value='$facteur_smtp_password' size='30' class='fondl' /></li>";
	echo "</ul>";

	echo '</li>';

	if (intval(phpversion()) == 5) {
		echo "<li>"._T('facteur:facteur_smtp_secure');
		echo bouton_radio("facteur_smtp_secure", "non", _T('facteur:facteur_smtp_secure_non'), $facteur_smtp_secure == "non");
		echo "&nbsp;";
		echo bouton_radio("facteur_smtp_secure", "ssl", _T('facteur:facteur_smtp_secure_ssl'), $facteur_smtp_secure == "ssl");
		echo "&nbsp;";
		echo bouton_radio("facteur_smtp_secure", "tls", _T('facteur:facteur_smtp_secure_tls'), $facteur_smtp_secure == "tls");
		echo '</li>';
	}

	echo "</ul>";

	echo fin_cadre_relief(true);

	echo debut_cadre_relief("", true, "", _T('facteur:facteur_smtp_sender'));
	echo '<p>'._T('facteur:facteur_smtp_sender_descriptif').'</p>';
	echo "<p><input type='text' name='facteur_smtp_sender' value=\"".$facteur_smtp_sender."\" size='40' class='forml' /></p>";
	echo fin_cadre_relief(true);

	echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('facteur:valider').'" /></p>';

	echo fin_cadre_trait_couleur(true);

	echo debut_cadre_trait_couleur("", true, "", _T('facteur:facteur_filtres'));
	echo '<p>'._T('facteur:facteur_filtres_descriptif').'</p>';
	echo '<p>';
	echo "<input type='checkbox' id='facteur_filtre_images' name='facteur_filtre_images' value=\"1\" ".($facteur_filtre_images ? "checked='checked'" : '')." />";
	echo '<label for="facteur_filtre_images">'._T('facteur:facteur_filtre_images').'</label>';
	echo '</p>';
	echo '<p>';
	echo "<input type='checkbox' id='facteur_filtre_css' name='facteur_filtre_css' value=\"1\" ".($facteur_filtre_css ? "checked='checked'" : '')." />";
	echo '<label for="facteur_filtre_css">'._T('facteur:facteur_filtre_css').'</label>';
	echo '</p>';
	echo '<p>';
	echo "<input type='checkbox' id='facteur_filtre_iso_8859' name='facteur_filtre_iso_8859' value=\"1\" ".($facteur_filtre_iso_8859 ? "checked='checked'" : '')." />";
	echo '<label for="facteur_filtre_iso_8859">'._T('facteur:facteur_filtre_iso_8859').'</label>';
	echo '</p>';
	echo '<p style="text-align: right;"><input class="fondo" name="valider" type="submit" value="'._T('facteur:valider').'" /></p>';

	echo fin_cadre_trait_couleur(true);

	echo debut_cadre_trait_couleur("", true, "", _T('facteur:tester_la_configuration'));
	if (!is_null(_request('tester'))) {
		if ($GLOBALS['meta']['facteur_adresse_envoi'] == 'oui')
			$destinataire = $GLOBALS['meta']['facteur_adresse_envoi_email'];
		else
			$destinataire = $GLOBALS['meta']['email_webmaster'];
		$message_html	= recuperer_fond('test_email/test_email_html', array());
		$message_texte	= recuperer_fond('test_email/test_email_texte', array());

		$test = new Facteur($destinataire, _T('facteur:corps_email_de_test'), $message_html, $message_texte);
		if (!$test->Send()) {
				echo '<p>'._T('facteur:erreur').' : '.$test->ErrorInfo.'</p>';
		} else {
			echo '<p>'._T('facteur:email_test_envoye').'</p>';
		}
	} else {
		echo '<p>'._T('facteur:note_test_configuration').'</p>';
		echo '<p style="text-align: right;"><input class="fondo" name="tester" type="submit" value="'._T('facteur:tester').'" /></p>';
	}
	echo fin_cadre_trait_couleur(true);

	echo '</form>';

	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'facteur'),'data'=>''));

	echo fin_gauche();

	echo fin_page();

}


?>