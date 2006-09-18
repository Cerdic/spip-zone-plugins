<?php


	/**
	 * SPIP-Météo : prévisions météo dans vos squelettes
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


 	include_spip('inc/presentation');
 	include_spip('meteo_fonctions');


	/**
	 * exec_meteo_visualisation
	 *
	 * @author Pierre Basson
	 **/
	function exec_meteo_visualisation() {
  		global $connect_statut, $connect_toutes_rubriques;

		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}

		if (empty($_GET['id_meteo'])) {
			$url = generer_url_ecrire('meteo', '', '&');
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		$id_meteo	= $_GET['id_meteo'];

		if (!empty($_POST['changer_action'])) {
			$action	= $_POST['action'];
			if ($action == 'poubelle') {
				spip_query('DELETE FROM spip_previsions WHERE id_meteo="'.$id_meteo.'"');
				spip_query('DELETE FROM spip_meteo WHERE id_meteo="'.$id_meteo.'" LIMIT 1');
				$url = generer_url_ecrire('meteo', '', '&');
				echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
				exit();
			}
		}

		if (!empty($_POST['reload'])) {
			cron_previsions_meteo($dummy);
		}

		$requete_meteo = 'SELECT id_meteo, ville, code, statut, maj FROM spip_meteo WHERE id_meteo="'.$id_meteo.'" LIMIT 1';
		$resultat_meteo = spip_query($requete_meteo);
		list($id_meteo, $ville, $code, $statut, $maj) = spip_fetch_array($resultat_meteo,SPIP_NUM);
		
		
		debut_page(_T('meteo:meteo'), "naviguer", "meteo");

		debut_gauche();

		debut_raccourcis();
		icone_horizontale(_T('meteo:ajouter_une_meteo'), generer_url_ecrire("meteo_edition","new=oui"), '../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png', 'creer.gif');
		icone_horizontale(_T('meteo:retour_liste_meteo'), generer_url_ecrire("meteo"), '../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png', '');
		fin_raccourcis();


    	debut_droite();
		debut_cadre_relief('../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png');

		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'><td width='100%' valign='top'>";
		switch ($statut) {
			case 'publie':
				$logo_statut = "puce-verte.gif";
				break;
			case 'en_erreur':
				$logo_statut = "puce-orange-anim.gif";
				break;
		}
		gros_titre($ville, $logo_statut);
		echo "</td>";
		echo "<td>", http_img_pack("rien.gif", ' ', "width='5'") ."</td>\n";
		echo "<td  align='$spip_lang_right' valign='top'>";
		icone(_T('meteo:modifier_meteo'), generer_url_ecrire("meteo_edition","id_meteo=$id_meteo"), '../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png', "edit.gif");
		echo "</td>";
		echo "</tr>\n";
		echo "<tr><td>\n";
		echo "<div align='$spip_lang_left' style='padding: 5px; border: 1px dashed #aaaaaa;'>";
		echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
		echo _T('meteo:code')." : <B>".$code."</B><br />";
		echo "</font>";
		echo "</div>";
		echo "</td></tr>\n";
		echo "</table>\n";

		echo "<div>&nbsp;</div>";


		echo generer_url_post_ecrire("meteo_visualisation", "id_meteo=$id_meteo", 'formulaire');

		debut_cadre_relief();
		echo "<center><B>"._T('meteo:action')."</B>&nbsp;";
		echo "<SELECT NAME='action' SIZE='1' CLASS='fondl'>\n";
		echo '	<OPTION VALUE="aucune">'._T('meteo:action_aucune').'</OPTION>'."\n";
		echo '	<OPTION VALUE="poubelle">'._T('meteo:action_poubelle').'</OPTION>'."\n";
		echo "</SELECT>";
		echo "&nbsp;&nbsp;<INPUT TYPE='submit' NAME='changer_action' CLASS='fondo' VALUE='"._T('meteo:changer')."' STYLE='font-size:10px'>";
		echo '</center>';
		fin_cadre_relief();
		echo '</form>';

		if ($statut == 'en_erreur') {
			debut_boite_info();
			echo _T('meteo:texte_probleme_recuperation_flux');
			fin_boite_info();
		}

		$date_aujourdhui = date("%Y-%m-%d 12:00:00");
		meteo_afficher_previsions(_T('meteo:previsions_meteo'), array("FROM" => 'spip_previsions', "WHERE" => 'id_meteo="'.$id_meteo.'" AND date>="'.$date_aujourdhui.'"', 'ORDER BY' => "date DESC"));

		echo "<p><div align='left'>"._T('meteo:date_derniere_maj').' : '.affdate_heure($maj).".</div>\n";
		echo "<div align='right'>\n",
			  generer_url_post_ecrire("meteo_visualisation","id_meteo=$id_meteo"),
			  "<input type='submit' name='reload' value=\"",
			  attribut_html(_T('lien_mise_a_jour_syndication')),
			  "\" class='fondo' style='font-size:9px;' /></form></div>\n";

		fin_cadre_relief();

		fin_page();

	}


?>