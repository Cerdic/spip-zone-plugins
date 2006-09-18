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
	 * exec_meteo_edition
	 *
	 * @author Pierre Basson
	 **/
	function exec_meteo_edition() {
  		global $connect_statut, $connect_toutes_rubriques;

		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}

		if (!empty($_POST['enregistrer'])) {
			$id_meteo	= $_GET['id_meteo'];
			$ville		= rawurlencode($_POST['ville']);
			$code		= $_POST['code'];

			if ($id_meteo == -1) {
				$insertion = 'INSERT INTO spip_meteo (ville, 
														code,
														maj,
														statut) 
												VALUES ("'.$ville.'", 
														"'.$code.'", 
														NOW(),
														"hors_ligne")';
				if (spip_query($insertion)) {
					$id_meteo = spip_insert_id();
				}
			} else {
				$modification = 'UPDATE spip_meteo SET ville="'.$ville.'", 
														code="'.$code.'", 
														maj=NOW()
													WHERE id_meteo="'.$id_meteo.'"';
				spip_query($modification);
			}
			
			cron_previsions_meteo($dummy);

			$url = generer_url_ecrire('meteo_visualisation', 'id_meteo='.$id_meteo, '&');
			echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
			exit();
		}

		$id_meteo = intval($_GET['id_meteo']);
		
		if (!empty($_GET['id_meteo']) AND $_GET['id_meteo'] != -1) {
			$result = spip_query('SELECT * FROM spip_meteo WHERE id_meteo="'.$id_meteo.'"');
			if (!$meteo = spip_fetch_array($result)) die('erreur');	
			$id_meteo	= $meteo['id_meteo'];
			$ville		= $meteo['ville'];
			$code		= $meteo['code'];
		} else {
			$new			= true;
			$id_meteo		= -1;
			$ville			= _T('meteo:nouvelle_ville');
			$onfocus		= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}

		if (!empty($_POST['chercher'])) {
			$ville = rawurlencode($_POST['ville']);
			$onfocus = '';
		}
		
		debut_page(_T('meteo:meteo'), "naviguer", "meteo");

	 	debut_gauche();
		

	 	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		if ($new)
			icone(_T('icone_retour'), generer_url_ecrire("meteo"), '../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png', "rien.gif");
		else
			icone(_T('icone_retour'), generer_url_ecrire("meteo_visualisation", "id_meteo=$id_meteo"), '../'._DIR_PLUGIN_METEO.'/img_pack/meteo.png', "rien.gif");

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('meteo:editer_meteo');
		gros_titre(rawurldecode($ville));
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("meteo_edition", ($id_meteo ? "id_meteo=$id_meteo" : ""), 'formulaire');

		echo "<P><B>"._T('meteo:ville')."</B><br/>"._T('meteo:ville_note');
		echo "<INPUT TYPE='text' NAME='ville' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE=\"".rawurldecode($ville)."\" SIZE='40' $onfocus>";

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='chercher' VALUE='"._T('meteo:chercher')."'>";
		echo "</DIV>";	 	

		$url_recherche = 'http://xoap.weather.com/search/search?where='.$ville;
		$chaine = @file_get_contents($url_recherche);
		if ($chaine) preg_match_all("@<loc id=\"(.*?)\" type=(.*?)>(.*?)</loc>@s", $chaine, $regs, PREG_SET_ORDER);
		if (sizeof($regs)) {
			echo "<P>"._T('meteo:code_ville');
			echo "<P><select name='code' CLASS='fondl'>";		
			foreach ($regs as $tableau) {
				$code_recherche = $tableau[1];
				$nom_recherche = $tableau[3];
				echo '<option value="'.$code_recherche.'" ';
				if ($code == $code_recherche) echo 'selected';
				echo '>'.$code_recherche.' - '.$nom_recherche.'</option>';
			}
			echo "</select></P>\n";

			echo "<DIV ALIGN='right'>";
			echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('meteo:enregistrer')."'>";
			echo "</DIV>";	 		 	
		}

		echo "</FORM>";
	 	fin_cadre_formulaire();
	 	
	 	fin_page();

	}


?>