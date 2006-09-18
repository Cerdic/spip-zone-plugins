<?php


	/**
	 * SPIP-Lettres : plugin de gestion de lettres d'information
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');
	include_spip('inc/config');
	include_spip('inc/meta');


	function exec_lettres_configuration() {
		global $couleur_foncee, $spip_lang_right;

		lettres_verifier_droits();

		if (!empty($_POST['valider'])) {
			if (!empty($_POST['fond_formulaire_lettre'])) {
				$fond_formulaire_lettre = addslashes($_POST['fond_formulaire_lettre']);
				ecrire_meta('fond_formulaire_lettre', $fond_formulaire_lettre);
			}

			if (!empty($_POST['fond_message_html'])) {
				$fond_message_html = addslashes($_POST['fond_message_html']);
				ecrire_meta('fond_message_html', $fond_message_html);
			}

			if (!empty($_POST['fond_message_texte'])) {
				$fond_message_texte = addslashes($_POST['fond_message_texte']);
				ecrire_meta('fond_message_texte', $fond_message_texte);
			}

			if (!empty($_POST['spip_lettres_smtp'])) {
				$spip_lettres_smtp = $_POST['spip_lettres_smtp'];
				ecrire_meta('spip_lettres_smtp', $spip_lettres_smtp);
			}

			if (!empty($_POST['spip_lettres_smtp_host'])) {
				$spip_lettres_smtp_host = addslashes($_POST['spip_lettres_smtp_host']);
				ecrire_meta('spip_lettres_smtp_host', $spip_lettres_smtp_host);
			}

			if (!empty($_POST['spip_lettres_smtp_port'])) {
				$spip_lettres_smtp_port = addslashes($_POST['spip_lettres_smtp_port']);
				ecrire_meta('spip_lettres_smtp_port', $spip_lettres_smtp_port);
			}

			if (!empty($_POST['spip_lettres_smtp_auth'])) {
				$spip_lettres_smtp_auth = $_POST['spip_lettres_smtp_auth'];
				ecrire_meta('spip_lettres_smtp_auth', $spip_lettres_smtp_auth);
			}

			if (!empty($_POST['spip_lettres_smtp_username'])) {
				$spip_lettres_smtp_username = addslashes($_POST['spip_lettres_smtp_username']);
				ecrire_meta('spip_lettres_smtp_username', $spip_lettres_smtp_username);
			}

			if (!empty($_POST['spip_lettres_smtp_password'])) {
				$spip_lettres_smtp_password = addslashes($_POST['spip_lettres_smtp_password']);
				ecrire_meta('spip_lettres_smtp_password', $spip_lettres_smtp_password);
			}

			$spip_lettres_smtp_sender = addslashes($_POST['spip_lettres_smtp_sender']);
			ecrire_meta('spip_lettres_smtp_sender', $spip_lettres_smtp_sender);

			ecrire_metas();
		}

		$fond_formulaire_lettre			= $GLOBALS['meta']['fond_formulaire_lettre'];
		$fond_message_html				= $GLOBALS['meta']['fond_message_html'];
		$fond_message_texte				= $GLOBALS['meta']['fond_message_texte'];
		$spip_lettres_smtp				= $GLOBALS['meta']['spip_lettres_smtp'];
		$spip_lettres_smtp_host			= $GLOBALS['meta']['spip_lettres_smtp_host'];
		$spip_lettres_smtp_port			= $GLOBALS['meta']['spip_lettres_smtp_port'];
		$spip_lettres_smtp_auth			= $GLOBALS['meta']['spip_lettres_smtp_auth'];
		$spip_lettres_smtp_username		= $GLOBALS['meta']['spip_lettres_smtp_username'];
		$spip_lettres_smtp_password		= $GLOBALS['meta']['spip_lettres_smtp_password'];
		$spip_lettres_smtp_sender		= $GLOBALS['meta']['spip_lettres_smtp_sender'];

		debut_page(_T('lettres:configuration'), "administration", "lettres_configuration");
		echo "<br><br>";
		gros_titre(_T('lettres:configuration'));

		debut_gauche();
/*		debut_boite_info();
		echo _T('lettres:configuration_note');
		fin_boite_info();
*/
    	debut_droite();


		echo generer_url_post_ecrire("lettres_configuration");

		debut_cadre_trait_couleur("", false, "", _T('lettres:configuration_squelettes'));

		debut_cadre_relief("", false, "", _T('lettres:squelette_formulaire_lettres'));
		echo "<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3 WIDTH=\"100%\">";
		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' class='verdana2'>";
		echo _T('lettres:squelette_formulaire_lettres_texte');
		echo "</TD></TR>";
		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' ALIGN='$spip_lang_left' class='verdana2'>";
		echo "<input type='text' name='fond_formulaire_lettre' value=\"".$fond_formulaire_lettre."\" size='40' CLASS='forml'>";
		echo "</TD></TR></table>\n";
		fin_cadre_relief();

		debut_cadre_relief("", false, "", _T('lettres:squelette_message_html'));
		echo "<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3 WIDTH=\"100%\">";
		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' class='verdana2'>";
		echo _T('lettres:squelette_message_html_descriptif');
		echo "</TD></TR>";
		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' ALIGN='$spip_lang_left' class='verdana2'>";
		echo "<input type='text' name='fond_message_html' value=\"".$fond_message_html."\" size='40' CLASS='forml'>";
		echo "</TD></TR></table>\n";
		fin_cadre_relief();

		debut_cadre_relief("", false, "", _T('lettres:squelette_message_texte'));
		echo "<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3 WIDTH=\"100%\">";
		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' class='verdana2'>";
		echo _T('lettres:squelette_message_texte_descriptif');
		echo "</TD></TR>";
		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' ALIGN='$spip_lang_left' class='verdana2'>";
		echo "<input type='text' name='fond_message_texte' value=\"".$fond_message_texte."\" size='40' CLASS='forml'>";
		echo "</TD></TR></table>\n";
		fin_cadre_relief();

		echo "<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3 WIDTH=\"100%\">";
		echo "<TR><td style='text-align:$spip_lang_right;'>";
		echo '<input class="fondo" name="valider" type="submit" value="'._T('lettres:valider').'">';
		echo "</TD></TR>";
		echo "</TABLE>\n";
		fin_cadre_trait_couleur();
		
		echo '<br />';
		debut_cadre_trait_couleur("", false, "", _T('lettres:configuration_mailer'));

		debut_cadre_relief("", false, "", _T('lettres:configuration_smtp'));
		echo "<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3 WIDTH=\"100%\">";

		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' class='verdana2'>";
		echo _T('lettres:configuration_smtp_descriptif');
		echo "</TD></TR>";

		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' ALIGN='$spip_lang_left' class='verdana2'>";

		echo bouton_radio("spip_lettres_smtp", "non", _T('lettres:utiliser_mail'), $spip_lettres_smtp == "non", "changeVisible(this.checked, 'smtp', 'none', 'block');");
		echo "<br />";
		echo bouton_radio("spip_lettres_smtp", "oui", _T('lettres:utiliser_smtp'), $spip_lettres_smtp == "oui", "changeVisible(this.checked, 'smtp', 'block', 'none');");

		if ($spip_lettres_smtp == "oui") $style = "display: block;";
		else $style = "display: none;";
		echo "<div id='smtp' style='$style'>";
		echo "<UL>";
		echo "<LI>"._T('lettres:spip_lettres_smtp_host')." <input type='text' name='spip_lettres_smtp_host' value='$spip_lettres_smtp_host' size='30' CLASS='fondl'>";
		echo "<LI>"._T('lettres:spip_lettres_smtp_port')." <input type='text' name='spip_lettres_smtp_port' value='$spip_lettres_smtp_port' size='4' CLASS='fondl'>";
		echo "<LI>"._T('lettres:spip_lettres_smtp_auth');
		echo bouton_radio("spip_lettres_smtp_auth", "oui", _T('lettres:spip_lettres_smtp_auth_oui'), $spip_lettres_smtp_auth == "oui", "changeVisible(this.checked, 'smtp-auth', 'block', 'none');");
		echo "&nbsp;";
		echo bouton_radio("spip_lettres_smtp_auth", "non", _T('lettres:spip_lettres_smtp_auth_non'), $spip_lettres_smtp_auth == "non", "changeVisible(this.checked, 'smtp-auth', 'none', 'block');");

		if ($spip_lettres_smtp_auth == "oui") $style = "display: block;";
		else $style = "display: none;";
		echo "<div id='smtp-auth' style='$style'>";
		echo "<UL>";
		echo "<LI>"._T('lettres:spip_lettres_smtp_username')." <input type='text' name='spip_lettres_smtp_username' value='$spip_lettres_smtp_username' size='30' CLASS='fondl'>";
		echo "<LI>"._T('lettres:spip_lettres_smtp_password')." <input type='password' name='spip_lettres_smtp_password' value='$spip_lettres_smtp_password' size='30' CLASS='fondl'>";
		echo "</UL>";
		echo "</div>";

		echo "</UL>";
		echo "</div>";

		echo "</TD></TR></table>\n";
		fin_cadre_relief();

		debut_cadre_relief("", false, "", _T('lettres:spip_lettres_smtp_sender'));
		echo "<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3 WIDTH=\"100%\">";
		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' class='verdana2'>";
		echo _T('lettres:spip_lettres_smtp_sender_descriptif');
		echo "</TD></TR>";
		echo "<TR><TD BACKGROUND='" . _DIR_IMG_PACK . "rien.gif' ALIGN='$spip_lang_left' class='verdana2'>";
		echo "<input type='text' name='spip_lettres_smtp_sender' value=\"".$spip_lettres_smtp_sender."\" size='40' CLASS='forml'>";
		echo "</TD></TR></table>\n";
		fin_cadre_relief();

		echo "<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=3 WIDTH=\"100%\">";
		echo "<TR><td style='text-align:$spip_lang_right;'>";
		echo '<input class="fondo" name="valider" type="submit" value="'._T('lettres:valider').'">';
		echo "</TD></TR>";
		echo "</TABLE>\n";

		fin_cadre_trait_couleur();
		
		echo '</form>';

		echo "<br />";

		fin_page();

	}


?>