<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/barre');
include_spip('inc/affichage');


function exec_liste_edit()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type;
global $new;
global $id_article;
global $modifier_message;
global $titre;
global $texte;


 
$nomsite=lire_meta("nom_site"); 
$urlsite=lire_meta("adresse_site"); 

 
// Admin SPIP-Listes
debut_page("Spip listes", "redacteurs", "spiplistes");

// spip-listes bien installé ?
if (!function_exists(spip_listes_onglets)){
    echo("<h3>erreur: spip-listes est mal installé !</h3>"); 
    echo("<p>Vérifier les étapes d'installation,notamment si vous avez bien renommé <i>mes_options.txt</i> en <i>mes_options.php3</i>.</p>");    
    fin_page();
	  exit;
}

if ($connect_statut != "0minirezo" ) {
	echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
	fin_page();
	exit;
}

if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
	$statut_auteur=$statut;
	spip_listes_onglets("messagerie", "Spip listes");
}

debut_gauche();

spip_listes_raccourcis();

creer_colonne_droite();

debut_droite("messagerie");


// MODE CREER_LISTE: ajout liste------------------------------------------------

$articles_surtitre = lire_meta("articles_surtitre");
$articles_soustitre = lire_meta("articles_soustitre");
$articles_descriptif = lire_meta("articles_descriptif");
$articles_urlref = lire_meta("articles_urlref");
$articles_chapeau = lire_meta("articles_chapeau");
$articles_ps = lire_meta("articles_ps");
$articles_redac = lire_meta("articles_redac");
$articles_mots = lire_meta("articles_mots");
$articles_modif = lire_meta("articles_modif");

// securite
$id_article = intval($id_article);
$id_rubrique = intval($id_rubrique);
$lier_trad = intval($lier_trad);
unset ($flag_editable);

//
// Creation de l'objet article
//

if ($id_article) {
	spip_query("UPDATE spip_articles SET date_modif=NOW(), auteur_modif=$connect_id_auteur WHERE id_article=$id_article");
	$id_article_bloque = $id_article;	// message pour inc_presentation

	// Recuperer les donnees de l'article
	$query = "SELECT * FROM spip_articles WHERE id_article=$id_article";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		$id_article = $row["id_article"];
		$surtitre = $row["surtitre"];
		$titre = $row["titre"];
		$soustitre = $row["soustitre"];
		$id_rubrique = $row["id_rubrique"];
		$id_secteur = $row['id_secteur'];
		$descriptif = $row["descriptif"];
		$nom_site = $row["nom_site"];
		$url_site = $row["url_site"];
		$chapo = $row["chapo"];
		$texte = $row["texte"];
		$ps = $row["ps"];
		$date = $row["date"];
		$statut = $row['statut'];
		$date_redac = $row['date_redac'];
	    	if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})",$date_redac,$regs)){
		        $mois_redac = $regs[2];
		        $jour_redac = $regs[3];
		        $annee_redac = $regs[1];
		        if ($annee_redac > 4000) $annee_redac -= 9000;
		}
		$extra=$row["extra"];

		$query = "SELECT * FROM spip_auteurs_articles WHERE id_article=$id_article AND id_auteur=$connect_id_auteur";
		$result_auteur = spip_query($query);
		$flag_auteur = (spip_num_rows($result_auteur) > 0);

		$flag_editable = (acces_rubrique($id_rubrique) OR ($flag_auteur > 0 AND ($statut == 'prepa' OR $statut == 'prop' OR $new == 'oui')));
		
	}
} else if ($new=='oui') {
	if ($lier_trad) {
		// Pas de langue choisie par defaut
		$changer_lang = '';

		// Recuperer les donnees de la traduction
		$query = "SELECT * FROM spip_articles WHERE id_article=$lier_trad";
		$result = spip_query($query);

		if ($row = spip_fetch_array($result)) {
			$surtitre = $row["surtitre"];
			$titre = filtrer_entites(_T('info_nouvelle_traduction')).' '.$row["titre"];
			$soustitre = $row["soustitre"];
			$id_rubrique_trad = $row["id_rubrique"];
			$descriptif = $row["descriptif"];
			$nom_site = $row["nom_site"];
			$url_site = $row["url_site"];
			$chapo = $row["chapo"];
			$texte = $row["texte"];
			$ps = $row["ps"];
			$date = $row["date"];
			$date_redac = $row['date_redac'];
			if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})",$date_redac,$regs)) {
				$mois_redac = $regs[2];
				$jour_redac = $regs[3];
				$annee_redac = $regs[1];
				if ($annee_redac > 4000) $annee_redac -= 9000;
			}
			$extra = $row["extra"];
		}
		$langues_autorisees = lire_meta('langues_multilingue');

		// Regler la langue, si possible
		if (ereg(",$spip_lang,", ",$langues_autorisees,")) {
			if (lire_meta('multi_articles') == 'oui') {
				// Si le menu de langues est autorise sur les articles,
				// on peut changer la langue quelle que soit la rubrique
				$changer_lang = $spip_lang;
			}	else if (lire_meta('multi_rubriques') == 'oui') {
				// Chercher la rubrique la plus adaptee pour accueillir l'article
				if (lire_meta('multi_secteurs') == 'oui')
					$id_parent = 0;
				else {
					$query = "SELECT id_parent FROM spip_rubriques WHERE id_rubrique=$id_rubrique";
					$row_rub = spip_fetch_array(spip_query($query));
					$id_parent = $row_rub['id_parent'];
				}
				$query = "SELECT id_rubrique FROM spip_rubriques WHERE lang='$spip_lang' AND id_parent=$id_parent";
				if ($row_rub = spip_fetch_array(spip_query($query))) {
					$id_rubrique = $id_secteur = $row_rub['id_rubrique'];
					$changer_lang = 'herit';
				}
			}
		}
	}	else {
		// Nouvel article : titre par defaut
		$titre = filtrer_entites(_T('spiplistes:Nouvelle_liste_de_diffusion'));
		$onfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
	}
	if (!$id_secteur) {
		$row_rub = spip_fetch_array(spip_query("SELECT id_secteur FROM spip_rubriques WHERE id_rubrique=$id_rubrique"));
		$id_secteur = $row_rub['id_secteur'];
	}
	$flag_editable = true;
}



if ($id_article && $id_document) {
	$query_doc = "SELECT * FROM spip_documents_articles WHERE id_document=$id_document AND id_article=$id_article";
	$result_doc = spip_query($query_doc);
	$flag_document_editable = (spip_num_rows($result_doc) > 0);
} else {
	$flag_document_editable = false;
}

$modif_document = $GLOBALS['modif_document'];
if ($modif_document == 'oui' AND $flag_document_editable) {
	$titre_document = addslashes(corriger_caracteres($titre_document));
	$descriptif_document = addslashes(corriger_caracteres($descriptif_document));
	$query = "UPDATE spip_documents SET titre=\"$titre_document\", descriptif=\"$descriptif_document\"";
	if ($largeur_document AND $hauteur_document) $query .= ", largeur='$largeur_document', hauteur='$hauteur_document'";
	$query .= " WHERE id_document=$id_document";
	spip_query($query);
}




//
// Gestion des textes trop longs (limitation brouteurs)
//

function coupe_trop_long($texte){	// utile pour les textes > 32ko
	if (strlen($texte) > 28*1024) {
		$texte = str_replace("\r\n","\n",$texte);
		$pos = strpos($texte, "\n\n\n", 28*1024);	// coupe para > 28 ko
		if ($pos > 0 and $pos < 32 * 1024) {
			$debut = substr($texte, 0, $pos)."\n\n\n<!--SPIP-->\n";
			$suite = substr($texte, $pos + 3);
		} else {
			$pos = strpos($texte, " ", 28*1024);	// sinon coupe espace
			if (!($pos > 0 and $pos < 32 * 1024))
				$pos = 28*1024;	// au pire
			$debut = substr($texte,0,$pos);
			$suite = substr($texte,$pos + 1);
		}
		return (array($debut,$suite));
	}
	else
		return (array($texte,''));
}


debut_cadre_formulaire();




echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
echo "<tr width='100%'>";
echo "<td>";
	if ($lier_trad) icone(_T('icone_retour'), "?exec=gerer_liste&id_article=$lier_trad", "article-24.gif", "rien.gif");
	else icone(_T('icone_retour'), "?exec=gerer_liste&id_article=$id_article", "article-24.gif", "rien.gif");

echo "</td>";
	echo "<td><img src='img_pack/rien.gif' width=10></td>\n";
echo "<td width='100%'>";
echo _T('spiplistes:modifier_liste');
gros_titre($titre);
echo "</td></tr></table>";
echo "<p>";

echo "<p><HR><p>";

	$titre = entites_html($titre);
	$soustitre = entites_html($soustitre);
	$surtitre = entites_html($surtitre);

	$descriptif = entites_html($descriptif);
	$nom_site = entites_html($nom_site);
	$url_site = entites_html($url_site);
	$chapo = entites_html($chapo);
	$texte = entites_html($texte);
	$ps = entites_html($ps);

	$lien = '?exec=gerer_liste';
	if ($id_article) $lien .= "&id_article=$id_article";
	echo "<form action='$lien' method='post' name='formulaire'>\n";

	if ($id_article)
		echo "<input type='hidden' name='id_article' value='$id_article'>";
	else if ($new == 'oui')
		echo "<input type='hidden' name='new' value='oui'>";

	if ($lier_trad) {
		echo "<input type='hidden' name='lier_trad' value='$lier_trad'>";
		echo "<input type='hidden' name='changer_lang' value='$spip_lang'>";
	}

	/*
	if (($options == "avancees" AND $articles_surtitre != "non") OR $surtitre) {
		echo "<b>"._T('texte_sur_titre')."</b>";
		echo "<br /><input type='text' name='surtitre' class='forml' value=\"$surtitre\" size='40'><p>";
	}
	else {
		echo "<input type='hidden' name='surtitre' value=\"$surtitre\" >";
	}
    */
	echo _T('texte_titre_obligatoire');
	echo "<br /><input type='text' name='titre' style='font-weight: bold; font-size: 13px;' class='formo' value=\"$titre\" size='40' $onfocus><p>";

	/*
	if (($articles_soustitre != "non") OR $soustitre) {
		echo "<b>"._T('texte_sous_titre')."</b>";
		echo "<br /><input type='text' name='soustitre' class='forml' value=\"$soustitre\" size='40'><br /><br />";
	}
	else {
		echo "<input type='hidden' name='soustitre' value=\"$soustitre\">";
	}
    */




	if ($new != 'oui') echo "<input type='hidden' name='id_rubrique_old' value=\"$id_rubrique\" >";


	if (($options == "avancees" AND $articles_descriptif != "non") OR $descriptif) {
		echo "<p><b>"._T('texte_descriptif_rapide')."</b>";
		echo aide ("artdesc");
		echo "<br />"._T('texte_contenu_article')."<br />";
		echo "<textarea name='descriptif' class='forml' ROWS='2' COLS='40' wrap=soft>";
		echo $descriptif;
		echo "</textarea><p>\n";
	}
	else {
		echo "<input type='hidden' name='descriptif' value=\"$descriptif\">";
	}

        /*

	if (($options == "avancees" AND $articles_urlref != "non") OR $nom_site OR $url_site) {
		echo _T('entree_liens_sites')."<br />\n";
		echo _T('info_titre')." ";
		echo "<input type='text' name='nom_site' class='forml' width='40' value=\"$nom_site\"/><br />\n";
		echo _T('info_url')." ";
		echo "<input type='text' name='url_site' class='forml' width='40' value=\"$url_site\"/>";
	}

	if (substr($chapo, 0, 1) == '=') {
		$virtuel = substr($chapo, 1);
		$chapo = "";
	}

	if ($connect_statut=="0minirezo" AND $virtuel){
		echo "<p><div style='border: 1px dashed #666666; background-color: #f0f0f0; padding: 5px;'>";
		echo "<table width=100% cellspacing=0 cellpadding=0 border=0>";
		echo "<tr><td valign='top'>";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size=2>";
		echo "<b><label for='confirme-virtuel'>"._T('info_redirection')."&nbsp;:</label></b>";
		echo aide ("artvirt");
		echo "</font>";
		echo "</td>";
		echo "<td width=10>&nbsp;</td>";
		echo "<td valign='top' width='50%'>";
		if (!$virtuel) $virtuel = "http://";
		echo "<input type='text' name='virtuel' class='forml' style='font-size:9px;' value=\"$virtuel\" size='40'>";
		echo "<input type='hidden' name='changer_virtuel' value='oui'>";
		echo "</td></tr></table>\n";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size=2>";
		echo _T('texte_article_virtuel_reference');
		echo "</font>";
		echo "</div><p>\n";
	}

	else {
		echo "<HR>";

		if (($articles_chapeau != "non") OR $chapo) {
			if ($spip_ecran == "large") $rows = 8;
			else $rows = 5;
			echo "<b>"._T('info_chapeau')."</b>";
			echo aide ("artchap");
			echo "<br />"._T('texte_introductif_article')."<br />";
			echo "<TEXTAREA name='chapo' class='forml' ROWS='$rows' COLS='40' wrap=soft>";
			echo $chapo;
			echo "</TEXTAREA><p>\n";
		}
		else {
			echo "<input type='hidden' name='chapo' value=\"$chapo\">";
		}

	}

	*/

	if ($spip_ecran == "large") $rows = 28;
	else $rows = 20;

	if (strlen($texte)>29*1024) // texte > 32 ko -> decouper en morceaux
	{
		$textes_supplement = "<br /><font color='red'>"._T('info_texte_long')."</font>\n";
		while (strlen($texte)>29*1024)
		{
			$nombre_textes ++;
			list($texte1,$texte) = coupe_trop_long($texte);

			$textes_supplement .= "<br />";
			$textes_supplement .= afficher_barre('formulaire', 'texte'.$nombre_textes);
			$textes_supplement .= "<textarea name='texte$nombre_textes'".
				" class='formo' ".afficher_claret()." rows='$rows' cols='40' wrap=soft>" .
				$texte1 . "</textarea><p>\n";
		}
	}
	echo "<b>"._T('spiplistes:txt_inscription')."</b>";
	echo "<br />"._T('spiplistes:txt_abonnement');
	echo "<br />"._T('texte_enrichir_mise_a_jour');
	echo aide("raccourcis");

	echo $textes_supplement;

	//echo "<br />";
	echo afficher_barre('document.formulaire.texte');
    echo "<textarea id='text_area' NAME='texte' ".$GLOBALS['browser_caret']." CLASS='formo' ROWS='$rows' COLS='40' wrap=soft>";    		echo $texte;
 	echo "</textarea>\n";

/* 	// traitement automatique des sauts de ligne : pas mur
	if ($proposer_autobr AND ($options == "avancees")) {
		echo '<div class="verdana2">';
		echo '<input type="checkbox" class="checkbox" name="post_autobr" id="autobr" value="1" />';
		echo '<label for="autobr">'._L("prendre en compte les sauts de ligne simples").'</label></div>';
	}


	if (($articles_ps != "non" AND $options == "avancees") OR $ps) {
		echo "<p><b>"._T('info_post_scriptum')."</b><br />";
		echo "<textarea name='ps' class='forml' rows='5' cols='40' wrap=soft>";
		echo $ps;
		echo "</textarea><p>\n";
	}
	else {
		echo "<input type='hidden' name='ps' value=\"$ps\">";
	}
 */
	if ($champs_extra) {
		include_ecrire("inc_extra.php3");
		extra_saisie($extra, 'articles', $id_secteur);
	}

	if ($date)
		echo "<input type='Hidden' name='date' value=\"$date\" size='40'><p>";

	if ($new == "oui")
		echo "<input type='Hidden' name='statut_nouv' value=\"inact\" size='40'><p>";

	echo "<div align='right'>";
	echo "<input class='fondo' type='submit' name='Valider' value='"._T('bouton_valider')."'>";
	echo "</div></form>";
	
	

fin_cadre_formulaire();






// MODE CREER LISTE FIN --------------------------------------------------------

echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;

fin_page();

}
/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
?>
