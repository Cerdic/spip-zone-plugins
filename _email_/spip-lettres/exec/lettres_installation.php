<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');
	include_spip('inc/config');
	include_spip('base/create');


	function exec_lettres_installation() {
		global $couleur_foncee;
		
		lettres_verifier_droits();

		if (!lettres_verifier_existence_tables() AND !empty($_POST['creer_tables'])) {
			creer_base();
			$url = generer_url_ecrire('lettres_configuration');
			lettres_rediriger_javascript($url);
		}
	
		if (!lettres_verifier_existence_tables()) {

			debut_page(_T('lettres:installation'), "administration", "lettres");
			echo "<br><br>";
			gros_titre(_T('lettres:installation'));

			debut_gauche();
			debut_boite_info();
			echo _T('lettres:installation_note');
			fin_boite_info();

	    	debut_droite();

			debut_cadre_relief();

			echo '<table border="0" cellspacing="0" cellpadding="5" width="100%">';
			echo '<tr><td bgcolor="'.$couleur_foncee.'"><b>';
			echo '<font face="Verdana,Arial,Sans,sans-serif" size="3" color="#FFFFFF">';
			echo _T('lettres:creation_des_tables_mysql').'</font></b></td></tr>';
			echo "<tr><td class='serif'>";
			echo generer_url_post_ecrire("lettres_installation").'<p align="justify">'._T('lettres:installation_texte').'</p>';
			echo '<div align="right"><input class="fondo" name="creer_tables" type="submit" value="'._T('lettres:creer_tables_mysql').'"></div></form>';

			echo "</td></tr>";
			echo "</table>";

			fin_cadre_relief();

			echo "<br />";


		}

		fin_page();
	}


?>