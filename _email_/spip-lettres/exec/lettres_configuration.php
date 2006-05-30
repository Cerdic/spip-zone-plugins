<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');
	include_spip('inc/config');
	include_spip('inc/meta');


	function exec_lettres_configuration() {
		global $couleur_foncee;

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
			ecrire_metas();
		}

		$fond_formulaire_lettre	= $GLOBALS['meta']['fond_formulaire_lettre'];
		$fond_message_html		= $GLOBALS['meta']['fond_message_html'];
		$fond_message_texte		= $GLOBALS['meta']['fond_message_texte'];

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

		debut_cadre_relief();
		echo '<table border="0" cellspacing="0" cellpadding="5" width="100%">';
		echo '<tr><td bgcolor="'.$couleur_foncee.'"><b>';
		echo '<font face="Verdana,Arial,Sans,sans-serif" size="3" color="#FFFFFF">';
		echo _T('lettres:squelette_formulaire_lettres').'</font></b></td></tr>';
		echo "<tr><td class='serif'>";
		echo '<p align="justify">'._T('lettres:squelette_formulaire_lettres_texte').'</p>';
		echo "<input type='text' name='fond_formulaire_lettre' value=\"".$fond_formulaire_lettre."\" size='40' CLASS='forml'>";
		echo '<div align="right"><input class="fondo" name="valider" type="submit" value="'._T('lettres:valider').'"></div>';
		echo "</td></tr>";
		echo "</table>";
		fin_cadre_relief();

		echo '<br />';
		
		debut_cadre_relief();
		echo '<table border="0" cellspacing="0" cellpadding="5" width="100%">';
		echo '<tr><td bgcolor="'.$couleur_foncee.'"><b>';
		echo '<font face="Verdana,Arial,Sans,sans-serif" size="3" color="#FFFFFF">';
		echo _T('lettres:squelette_message_html').'</font></b></td></tr>';
		echo "<tr><td class='serif'>";
		echo '<p align="justify">'._T('lettres:squelette_message_html_descriptif').'</p>';
		echo "<input type='text' name='fond_message_html' value=\"".$fond_message_html."\" size='40' CLASS='forml'>";
		echo "</td></tr>";
		echo '<tr><td bgcolor="'.$couleur_foncee.'"><b>';
		echo '<font face="Verdana,Arial,Sans,sans-serif" size="3" color="#FFFFFF">';
		echo _T('lettres:squelette_message_texte').'</font></b></td></tr>';
		echo "<tr><td class='serif'>";
		echo '<p align="justify">'._T('lettres:squelette_message_texte_descriptif').'</p>';
		echo "<input type='text' name='fond_message_texte' value=\"".$fond_message_texte."\" size='40' CLASS='forml'>";
		echo '<div align="right"><input class="fondo" name="valider" type="submit" value="'._T('lettres:valider').'"></div>';
		echo "</td></tr>";
		echo "</table>";
		fin_cadre_relief();
		
		echo '</form>';

		echo "<br />";

		fin_page();

	}


?>