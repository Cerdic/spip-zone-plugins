<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
*/


include_spip('inc/abomailmans');


function abomailmans_update(){
	include_spip('base/abstract_sql');
	$id_abomailman = intval(_request('id_abomailman'));
	$new = _request('new');
	$supp_abomailman = intval(_request('supp_abomailman'));
	$retour = _request('retour');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$email = _request('email');
	$supp_confirme = _request('supp_confirme');
	$supp_rejet = _request('supp_rejet');

	//
	// Modifications des donnees de base du formulaire
	//
	if (abomailmans_abomailman_administrable($id_abomailman)) {
		if ($supp_abomailman = intval($supp_abomailman) AND $supp_confirme AND !$supp_rejet) {
			$query = "DELETE FROM spip_abomailmans WHERE id_abomailman=$supp_abomailman";
			$result = spip_query($query);
			if ($retour) {
				$retour = urldecode($retour);
				Header("Location: $retour");
				exit;
			}
		}
	}
	
	if (abomailmans_abomailman_editable($id_abomailman)) {
		// creation
		if ($new == 'oui' && $titre) {
			$id_abomailman = spip_abstract_insert(
			"spip_abomailmans","(titre,descriptif,email)",
			"(".
				spip_abstract_quote($titre).", ".
				spip_abstract_quote($descriptif).", ".
				spip_abstract_quote($email).")");
		}
		// maj
		else if ($id_abomailman && $titre) {
			spip_query("UPDATE spip_abomailmans SET ".
				"titre=".spip_abstract_quote($titre).", ".
				"descriptif=".spip_abstract_quote($descriptif).", ".
				"email=".spip_abstract_quote($email).
				"WHERE id_abomailman=$id_abomailman");
		}
		// lecture
		$result = spip_query("SELECT * FROM spip_abomailmans WHERE id_abomailman=".spip_abstract_quote($id_abomailman));
		if ($row = spip_fetch_array($result)) {
			$id_abomailman = $row['id_abomailman'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$email = $row['email'];
		}
	}	
	
	return $id_abomailman;
}

function exec_abomailmans_edit(){
	global $spip_lang_right;
	$id_abomailman = intval(_request('id_abomailman'));
	$new = _request('new');
	$supp_abomailman = intval(_request('supp_abomailman'));
	$retour = _request('retour');
	$titre = _request('titre');
	$descriptif = _request('descriptif');
	$email = _request('email');
	$supp_confirme = _request('supp_confirme');
	$supp_rejet = _request('supp_rejet');

	
  _abomailmans_install();

	if ($retour)
		$retour = urldecode($retour);
		  include_spip("inc/presentation");
			include_spip("inc/config");

	$clean_link = parametre_url(self(),'new','');
	$abomailman_link = generer_url_ecrire('abomailmans_edit');
	if ($new == 'oui' && !$titre)
		$abomailman_link = parametre_url($abomailman_link,"new",$new);
	if ($retour) 
		$abomailman_link = parametre_url($abomailman_link,"retour",urlencode($retour));

		
	//
	// Recupere les donnees
	//
	if ($new == 'oui' && !$titre) {
		$titre = _T("abomailmans:nouvelle_liste");
		$descriptif = "";
		$email = "";
		$js_titre = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
	}
	else {
		//
		// Modifications au structure du formulaire
		//
		$id_abomailman = abomailmans_update();
	
		$result = spip_query("SELECT * FROM spip_abomailmans WHERE id_abomailman=".spip_abstract_quote($id_abomailman));
		if ($row = spip_fetch_array($result)) {
			$id_abomailman = $row['id_abomailman'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$email = $row['email'];

		}
		$js_titre = "";
	}
	$abomailman_link = parametre_url($abomailman_link,"id_abomailman",$id_abomailman);
	$clean_link = parametre_url($clean_link,"id_abomailman",$id_abomailman);

	//
	// Affichage de la page
	//

	debut_page("&laquo; $titre &raquo;", "documents", "abomailmans","");

	debut_gauche();
	echo "<br /><br />\n";

	debut_droite();

	if ($supp_abomailman && $supp_confirme==NULL && $supp_rejet==NULL) {
		echo "<p>";
		echo _T('charts:confirmer_supression')."</p>\n";
		$link = parametre_url($clean_link,'supp_abomailman', $supp_abomailman);
		echo "<form method='POST' action='"
			. $link
			. "' style='border: 0px; margin: 0px;'>";
		echo "<div style='text-align:$spip_lang_right'>";
		echo "<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondo'>";
		echo " &nbsp; ";
		echo "<input type='submit' name='supp_rejet' value=\""._T('item_non')."\" class='fondo'>";
		echo "</div>";
		echo "</form><br />\n";
	}


	if ($id_abomailman && $supp_confirme==NULL) {
		debut_cadre_relief("../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/mailman.gif");

		gros_titre($titre);

		if ($descriptif) {
			echo "<p /><div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._T('info_descriptif')."</strong> ";
			echo propre($descriptif);
			echo "</div>\n";
		}
		
		if ($email) {
			echo "<p /><div align='left' border: 1px dashed #aaaaaa;'>";
			echo "<strong class='verdana2'>"._T('abomailmans:emailliste_abomailman')."</strong> ";
			echo propre($email);
			echo "</div>\n";
		}

		

	

		fin_cadre_relief();
	}


	//
	// Icones retour et suppression
	//
	echo "<div style='text-align:$spip_lang_right'>";
	if ($retour) {
		icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/mailman.gif", "rien.gif",'right');
	}
	if ($id_abomailman && abomailmans_abomailman_administrable($id_abomailman)) {
		echo "<div style='float:$spip_lang_left'>";
		$link = parametre_url($clean_link,'supp_abomailman', $id_abomailman);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('abomailmans_tous')));
		}
		icone(_T("abomailmans:supprimer"), $link, "../"._DIR_PLUGIN_ABOMAILMANS."/img_pack/mailman.gif", "supprimer.gif");
		echo "</div>";
	}
	echo "<div style='clear:both;'></div>";
	echo "</div>";

	//
	// Edition des donnees du formulaire
	//
	if (abomailmans_abomailman_editable($id_abomailman)) {
		echo "<p>";
		debut_cadre_formulaire();

		echo "<div class='verdana2'>";
		echo "<form method='POST' action='"
			. $abomailman_link
			. "' style='border: 0px; margin: 0px;'>";

		echo "<strong><label for='titre_abomailman'>"._T("abomailmans:titre_abomailman")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='titre' id='titre_abomailman' CLASS='formo' ".
			 "value=\"".entites_html($titre)."\" size='40'$js_titre /><br />\n";

		echo "<strong><label for='desc_abomailman'>"._T('info_descriptif')."</label></strong>";
		echo "<br />";
		echo "<textarea name='descriptif' id='desc_abomailman' class='forml' rows='4' cols='40' wrap='soft'>";
		echo entites_html($descriptif);
		echo "</textarea><br />\n";

					
		echo "<strong><label for='email_abomailman'>"._T("abomailmans:emailliste_abomailman")."</label></strong> "._T('info_obligatoire_02');
		echo "<br />";
		echo "<input type='text' name='email' id='email_abomailman' CLASS='formo' ".
			 "value=\"".$email."\" size='40' /><br />\n";
 	
		echo "<div align='right'>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";

		echo "</form>";

		echo "</div>\n";
		echo "<div id=\"abomailmain_templaate\"></div>";

		fin_cadre_formulaire();
	}


	fin_page();
}
?>
