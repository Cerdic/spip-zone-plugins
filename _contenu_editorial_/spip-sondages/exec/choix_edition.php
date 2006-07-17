<?php


	/**
	 * SPIP-Sondages : plugin de gestion de sondages
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('inc/sondages_fonctions');
	include_spip('inc/sondages_admin');
 	include_spip('inc/presentation');


	function exec_choix_edition() {

		sondages_verifier_droits();

		if (empty($_GET['id_sondage'])) {
			$url = generer_url_ecrire('sondages', '', '&');
			sondages_rediriger_javascript($url);
		}

		$id_sondage = intval($_GET['id_sondage']);
		
		if (!empty($_GET['id_choix'])) {
			$id_choix = intval($_GET['id_choix']);
			$result = spip_query('SELECT * FROM spip_choix WHERE id_choix="'.$id_choix.'"');
			if (!$choix = spip_fetch_array($result)) die('erreur');	
			$titre	= $choix['titre'];
			$ordre	= $choix['ordre'];
		} else {
			$new				= true;
			$id_choix			= -1;
			$resultat_nb_choix	= spip_query('SELECT id_choix FROM spip_choix WHERE id_sondage="'.$id_sondage.'"');
			$ordre 				= intval(spip_num_rows($resultat_nb_choix));
			$titre				= _T('sondages:nouveau_choix');
			$onfocus			= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}

	 	debut_page(_T('sondages:choix'), "naviguer", "sondages");

	 	debut_gauche();
		

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		icone(_T('icone_retour'), generer_url_ecrire("sondages_visualisation", "id_sondage=$id_sondage"), '../'._DIR_PLUGIN_SONDAGES.'/img_pack/sondages-24.png', "rien.gif");

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('sondages:editer_choix');
		gros_titre($titre);
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("sondages_visualisation", "id_sondage=$id_sondage&id_choix=$id_choix", 'formulaire');

		echo "<P><B>"._T('sondages:titre')."</B>";
		echo "<BR><INPUT TYPE='text' NAME='titre' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE=\"$titre\" SIZE='40' $onfocus>";

		echo "<P>"._T('sondages:position')."<br />";
		echo "<select name='position' CLASS='fondl'>";		
		$i = 0;
		echo '<option value="'.$i++.'" ';
		if ($ordre == 0) echo 'selected';
		echo '>'._T('sondages:en_premier').'</option>';
		$requete_autres_choix = 'SELECT * FROM spip_choix WHERE id_sondage="'.$id_sondage.'" AND id_choix!="'.$id_choix.'" ORDER BY ordre';
		$resultat_autres_choix = spip_query($requete_autres_choix);
		while ($arr = spip_fetch_array($resultat_autres_choix)) {
			echo '<option value="'.$i.'" ';
			if ($ordre == $i) echo 'selected';
			echo '>'._T('sondages:apres').'&nbsp;'.$arr['titre'].'</option>';
			$i++;
		}
		echo "</select></P>\n";

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer_choix' VALUE='"._T('sondages:enregistrer')."'>";
		echo "</DIV></FORM>";	 	
	 		 	
	 	fin_cadre_formulaire();
	 	
	 	fin_page();
	 	
	}
	
	
	
?>