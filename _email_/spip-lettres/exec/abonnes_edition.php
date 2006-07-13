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


	function exec_abonnes_edition() {
		global $champs_extra, $couleur_foncee, $tri, $debut;
	 	
		lettres_verifier_droits();

		$id_abonne = intval($_GET['id_abonne']);
		$id_lettre = intval($_GET['id_lettre']);
		
		if (!empty($_GET['id_abonne'])) {
			$result = spip_query("SELECT * FROM spip_abonnes WHERE id_abonne=" . $id_abonne);		
			if (!$abonne = spip_fetch_array($result)) die('erreur');	
			$email	= $abonne['email'];
			$format	= $abonne['format'];
			$extra	= $abonne['extra'];
		} else if (!empty($_GET['email']) AND !empty($_GET['format']) AND !empty($_GET['erreur'])) {
			$new		= true;
			$email		= $_GET['email'];
			$format		= $_GET['format'];
			$erreur		= $_GET['erreur'];
		} else {
			$new		= true;
			$email		= _T('lettres:nouvel_abonne');
			$onfocus	= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}

	 	debut_page(_T('lettres:abonnes'), "lettres", "abonnes");

	 	debut_gauche();

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		if ($new AND $id_lettre)
			icone(_T('icone_retour'), generer_url_ecrire("lettres_visualisation", "id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', "rien.gif");
		else if ($new)
			icone(_T('icone_retour'), generer_url_ecrire("abonnes"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', "rien.gif");
		else
			icone(_T('icone_retour'), generer_url_ecrire("abonnes_visualisation", "id_abonne=$id_abonne"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', "rien.gif");

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('lettres:editer_abo');
		gros_titre($email);
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("abonnes_visualisation", ($id_abonne ? "id_abonne=$id_abonne" : ""), 'formulaire');

		// Abonner directement à une lettre...
		echo "<INPUT TYPE='hidden' NAME='id_lettre' VALUE='$id_lettre'>";

		echo _T('lettres:email_o');
		if ($erreur)
			echo ' <b>'._T('lettres:email_non_valide').'</b>';
		echo "<BR><INPUT TYPE='text' NAME='email' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE=\"$email\" SIZE='40' $onfocus><P>";

		echo "<P>"._T('lettres:format_o')."<br />";
		echo "<select name='format' CLASS='fondl'>";		
		echo '<option value="mixte" '; if ($format == 'mixte') echo 'selected'; echo '>'._T('lettres:format_mixte').'</option>';
		echo '<option value="html" '; if ($format == 'html') echo 'selected'; echo '>'._T('lettres:format_html').'</option>';
		echo '<option value="texte" '; if ($format == 'texte') echo 'selected'; echo '>'._T('lettres:format_texte').'</option>';
		echo "</select></P>\n";

		if ($champs_extra) {
			include_spip('inc/extra');
			extra_saisie($extra, 'abonnes');
		}

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('lettres:enregistrer')."'>";
		echo "</DIV></FORM>";	 	
	 		 	
	 	fin_cadre_formulaire();
	 	
	 	
	 	
	 	
	 	
	 	fin_page();
	 	
	}
	
	
	
?>