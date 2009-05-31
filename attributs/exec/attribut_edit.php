<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/attributs_gestion');

function exec_attribut_edit_dist(){
	global
		$connect_statut,
		$connect_toutes_rubriques,
		$new,
		$les_notes,
		$id_attribut,
		$retour;

	$id_attribut = intval($id_attribut);

//
// Recupere les donnees
//
	$row = spip_fetch_array(spip_query("SELECT * FROM spip_attributs WHERE id_attribut=$id_attribut"));
	 if ($row) {
		$id_attribut = $row['id_attribut'];
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$texte = $row['texte'];
		$art = $row['articles'];
		$rub = $row['rubriques'];
		$brv = $row['breves'];
		$sit = $row['syndic'];
		$aut = $row['auteurs'];
		$gpe = $row['groupes_mots'];
		$mot = $row['mots'];
		$redac = $row['redacteurs'];
		$onfocus ='';
	 } else {
		//Seul un admin général peut créer un attribut
		if ($new != 'oui' OR !autoriser('modifier','attribut')) {
			echo minipres(_T('attributs:pas_autorise'));
			exit;
		}
		$id_attribut = 0;

		if ($new == 'oui') {
			$titre = filtrer_entites(_T('attributs:titre_nouvel_attribut'));
			$onfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
			$descriptif = '';
			$texte = '';
			$art = 'oui';
			$rub = 'non';
			$brv = 'non';
			$sit = 'non';
			$aut = 'non';
			$gpe = 'non';
			$mot = 'non';
			$redac = 'oui';
		}
	}

	debut_page("&laquo; $titre &raquo;");
	debut_gauche();

// Boite info
	 if ($id_attribut) {
		debut_boite_info();
		echo "\n<div style='font-weight: bold; text-align: center' class='verdana1 spip_xx-small'>" ;
		echo _T('attributs:attribut_numero');
		echo "<br /><span class='spip_xx-large'>";
		echo "$id_attribut";
		echo '</span></div>';
		fin_boite_info();
	}

//Raccourcis
	$res = icone_horizontale(_T('attributs:voir_tous'), generer_url_ecrire("attributs",''),  "../"._DIR_PLUGIN_ATTRIBUTS. "img_pack/attribut-24.png", 'rien.gif',false);
	if(isset($retour)) {
		$icone_retour = "../"._DIR_PLUGIN_ATTRIBUTS. "img_pack/attribut-24.png";
		if(strstr($retour,'exec=auteur')) $icone_retour = 'auteur-24.gif';
		if(strstr($retour,'exec=articles')) $icone_retour = 'article-24.gif';
		if(strstr($retour,'exec=naviguer')) $icone_retour = 'rubrique-24.gif';
		if(strstr($retour,'exec=rubriques')) $icone_retour = 'rubrique-24.gif';
		if(strstr($retour,'exec=breves')) $icone_retour = 'breve-24.gif';
		if(strstr($retour,'exec=sites')) $icone_retour = 'site-24.gif';
		if(strstr($retour,'exec=mots_type')) $icone_retour = 'groupe-mot-24.gif';
		if(strstr($retour,'exec=mots_edit')) $icone_retour = 'mot-cle-24.gif';
		$res .= icone_horizontale(_T('attributs:retour'), $retour, $icone_retour, "../"._DIR_PLUGIN_ATTRIBUTS. "img_pack/retour.png",false);
	}
	echo bloc_des_raccourcis($res);


// Affichage du résumé mis en forme
	debut_droite();
	debut_cadre_relief("../"._DIR_PLUGIN_ATTRIBUTS. "img_pack/attribut-24.png");
	echo "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>";
	echo "<tr>";
	echo "<td style='width: 100%' valign='top'>";
	echo gros_titre($titre,'',false);

	if ($descriptif) {
		echo "<div style='border: 1px dashed #aaaaaa; ' class='verdana1 spip_small'>";
		echo "<b>" . _T('info_descriptif') . "</b> ";
		echo propre($descriptif);
		echo "&nbsp; ";
		echo "</div>";
	}

	echo "</td>";
	echo "</tr></table>\n";


	if (strlen($texte)>0){
		echo "<p class='verdana1 spip_small'>";
		echo propre($texte);
		echo "</p>";
	}

	if ($les_notes) {
		debut_cadre_relief();
		echo "<div $dir_lang class='arial11'>";
		echo justifier("<b>"._T('info_notes')."&nbsp;:</b> ".$les_notes);
		echo "</div>";
		fin_cadre_relief();
	}

//Affichage des objets liés

	if ($id_attribut) {

		if ($connect_statut == "0minirezo")
			$aff_articles = "'prepa','prop','publie','refuse'";
		else
			$aff_articles = "'prop','publie'";

		echo afficher_rubriques('<b>' . _T('attributs:info_rubriques_liees_attribut') . '</b>', array("FROM" => 'spip_rubriques AS rubrique, spip_attributs_rubriques AS lien', 'WHERE' => "lien.id_attribut='$id_attribut' AND lien.id_rubrique=rubrique.id_rubrique", 'ORDER BY' => "rubrique.titre"));

		echo afficher_articles(_T('attributs:info_articles_lies_attribut'),	array('FROM' => "spip_articles AS articles, spip_attributs_articles AS lien", 'WHERE' => "lien.id_attribut='$id_attribut' AND lien.id_article=articles.id_article AND articles.statut IN ($aff_articles)", 'ORDER BY' => "articles.date DESC"));

		echo afficher_breves('<b>' . _T('attributs:info_breves_liees_attribut') . '</b>', array("FROM" => 'spip_breves AS breves, spip_attributs_breves AS lien', 'WHERE' => "lien.id_attribut='$id_attribut' AND lien.id_breve=breves.id_breve", 'ORDER BY' => "breves.date_heure DESC"));

		include_spip('inc/sites_voir');
		echo afficher_sites('<b>' . _T('attributs:info_sites_lies_attribut') . '</b>', array("FROM" => 'spip_syndic AS syndic, spip_attributs_syndic AS lien', 'WHERE' => "lien.id_attribut='$id_attribut' AND lien.id_syndic=syndic.id_syndic", 'ORDER BY' => "syndic.nom_site DESC"));

		echo attributs_afficher_auteurs('<b>' . _T('attributs:info_auteurs_lies_attribut') . '</b>', array("FROM" => 'spip_auteurs AS auteurs, spip_attributs_auteurs AS lien', 'WHERE' => "lien.id_attribut='$id_attribut' AND lien.id_auteur=auteurs.id_auteur", 'ORDER BY' => "auteurs.nom DESC"));

		echo attributs_afficher_groupes_mots('<b>' . _T('attributs:info_groupes_mots_lies_attribut') . '</b>', array("FROM" => 'spip_groupes_mots AS groupesmots, spip_attributs_groupes_mots AS lien', 'WHERE' => "lien.id_attribut='$id_attribut' AND lien.id_groupe=groupesmots.id_groupe", 'ORDER BY' => "groupesmots.titre"));

		echo attributs_afficher_mots('<b>' . _T('attributs:info_mots_lies_attribut') . '</b>', array("FROM" => 'spip_mots AS mots, spip_attributs_mots AS lien', 'WHERE' => "lien.id_attribut='$id_attribut' AND lien.id_mot=mots.id_mot", 'ORDER BY' => "mots.titre"));
	}

	fin_cadre_relief();

//Affichage du formulaire

	if (autoriser('modifier','attribut',$id_attribut)){

		echo debut_cadre_formulaire('',true);

		$res = "<div class='serif'>";

		$titre = entites_html($titre);
		$descriptif = entites_html($descriptif);
		$texte = entites_html($texte);
		
		$res .= "<b>"._T('attributs:titre_attribut')."</b> "._T('info_obligatoire_02');

		$res .= "<br /><input type='text' name='titre' class='formo' value=\"$titre\" size='40' $onfocus /><br />";



		$res .= "<b>"._T('texte_descriptif_rapide')."</b><br />";
		$res .= "<textarea name='descriptif' class='forml' rows='4' cols='40'>";
		$res .= $descriptif;
		$res .= "</textarea><br />\n";

		$res .= "<b>"._T('info_texte_explicatif')."</b><br />";
		$res .= "<textarea name='texte' rows='8' class='forml' cols='40'>";
		$res .= $texte;
		$res .= "</textarea><br />\n";

		$res .= "<b>"._T("attributs:associer_attribut")."</b><br />";

		$checked = ($art == 'oui') ? " checked='checked'" : "";
		$res .= "<input type='checkbox' name='articles' value='oui' id='opt_art'$checked> ";
		$res .= "<label for='opt_art'>"._T("item_mots_cles_association_articles")."</label><br />";

		$checked = ($rub == 'oui') ? " checked='checked'" : "";
		$res .= "<input type='checkbox' name='rubriques' value='oui' id='opt_rub'$checked> ";
		$res .= "<label for='opt_rub'>"._T("item_mots_cles_association_rubriques")."</label><br />";

		$checked = ($brv == 'oui') ? " checked='checked'" : "";
		$res .= "<input type='checkbox' name='breves' value='oui' id='opt_brv'$checked> ";
		$res .= "<label for='opt_brv'>"._T("item_mots_cles_association_breves")."</label><br />";


		$checked = ($aut == 'oui') ? " checked='checked'" : "";
		$res .= "<input type='checkbox' name='auteurs' value='oui' id='opt_aut'$checked> ";
		$res .= "<label for='opt_aut'>"._T("attributs:attribut_association_auteurs")."</label><br />";

		$checked = ($sit == 'oui') ? " checked='checked'" : "";
		$res .= "<input type='checkbox' name='syndic' value='oui' id='opt_sit'$checked> ";
		$res .= "<label for='opt_sit'>"._T("item_mots_cles_association_sites")."</label><br />";

		//$checked = ($gpe == 'oui') ? " checked='checked'" : "";
		//$res .= "<input type='checkbox' name='groupes_mots' value='oui' id='opt_gpe'$checked> ";
		//$res .= "<label for='opt_gpe'>"._T("attributs:attribut_association_groupes")."</label><br />";
		$res .= "<input type='hidden' name='groupes_mots' value='non' id='opt_gpe'> ";

		$checked = ($mot == 'oui') ? " checked='checked'" : "";
		$res .= "<input type='checkbox' name='mots' value='oui' id='opt_mot'$checked> ";
		$res .= "<label for='opt_mot'>"._T("attributs:attribut_association_mots")."</label><p />";

		$res .= "<b>"._T("attributs:autoriser_redacteurs")."</b><br />";

		$checked = ($redac == 'oui') ? " checked='checked'" : "";
		$res .= "<input type='checkbox' name='redacteurs' value='oui' id='opt_redac'$checked> ";
		$res .= "<label for='opt_redac'>"._T("attributs:oui_ils_peuvent")."</label><br />";

		$res .= "<div align='right'><input type='submit' value='"._T('bouton_enregistrer')."' class='fondo' /></div>";
		$res .= "</div>";


		$redirect = generer_url_ecrire('attributs','');
		echo generer_action_auteur("editer_attribut", $id_attribut, $redirect, $res);

		fin_cadre_formulaire();
	}




	echo fin_gauche(), fin_page();
}

?>