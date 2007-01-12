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
include_spip('base/spip-listes');


function exec_liste_edit()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type;
global $new;
global $id_liste;
global $modifier_message;
global $titre;
global $texte;


 
$nomsite=lire_meta("nom_site"); 
$urlsite=lire_meta("adresse_site"); 

 
// Admin SPIP-Listes
echo debut_page("Spip listes", "redacteurs", "spiplistes");



if ($connect_statut != "0minirezo" ) {
	echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
	echo fin_page();
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

$articles_descriptif = lire_meta("articles_descriptif");

$articles_redac = lire_meta("articles_redac");
$articles_mots = lire_meta("articles_mots");
$articles_modif = lire_meta("articles_modif");

// securite
$id_liste = intval($id_liste);
$id_rubrique = intval($id_rubrique);
$lier_trad = intval($lier_trad);
unset ($flag_editable);

//
// Creation de l'objet article
//

if ($id_liste) {
	//spip_query("UPDATE spip_articles SET date_modif=NOW(), auteur_modif=$connect_id_auteur WHERE id_liste=$id_liste");

	// Recuperer les donnees de l'article
	$query = "SELECT * FROM spip_listes WHERE id_liste=$id_liste";
	$result = spip_query($query);

	if ($row = spip_fetch_array($result)) {
		$titre = $row["titre"];
		$lang = $row["lang"];
		$pied_page = $row["pied_page"];
		$texte = $row["texte"];
		$date = $row["date"];
		$statut = $row['statut'];
		
		$extra=$row["extra"];

		$query = "SELECT * FROM spip_abonnes_listes WHERE id_liste=$id_liste AND id_auteur=$connect_id_auteur";
		$result_auteur = spip_query($query);
		$flag_auteur = (spip_num_rows($result_auteur) > 0);

		$flag_editable = (acces_rubrique($id_rubrique) OR ($flag_auteur > 0 AND ($statut == 'prepa' OR $statut == 'prop' OR $new == 'oui')));
		
	}
} else if ($new=='oui') {

		// titre par defaut
		$titre = filtrer_entites(_T('spiplistes:Nouvelle_liste_de_diffusion'));
		$onfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
	
	$flag_editable = true;
}



if ($id_liste && $id_document) {
	$query_doc = "SELECT * FROM spip_documents_articles WHERE id_document=$id_document AND id_liste=$id_liste";
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



echo debut_cadre_formulaire();




echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
echo "<tr width='100%'>";
echo "<td>";
	if ($lier_trad) icone(_T('icone_retour'), "?exec=gerer_liste&id_liste=$lier_trad", "article-24.gif", "rien.gif");
	else icone(_T('icone_retour'), "?exec=gerer_liste&id_liste=$id_liste", "article-24.gif", "rien.gif");

echo "</td>";
	echo "<td><img src='img_pack/rien.gif' width=10></td>\n";
echo "<td width='100%'>";
echo _T('spiplistes:modifier_liste');
gros_titre($titre);
echo "</td></tr></table>";
echo "<p>";

echo "<p><HR><p>";

	$titre = entites_html($titre);
	
	$descriptif = entites_html($descriptif);
	
	$texte = entites_html($texte);

	$lien = '?exec=gerer_liste';
	if ($id_liste) $lien .= "&id_liste=$id_liste";
	echo "<form action='$lien' method='post' name='formulaire'>\n";

	if ($id_liste)
		echo "<input type='hidden' name='id_liste' value='$id_liste'>";
	else if ($new == 'oui')
		echo "<input type='hidden' name='new' value='oui'>";

	if ($lier_trad) {
		echo "<input type='hidden' name='lier_trad' value='$lier_trad'>";
		echo "<input type='hidden' name='changer_lang' value='$spip_lang'>";
	}

	
	echo _T('texte_titre_obligatoire');
	echo "<br /><input type='text' name='titre' style='font-weight: bold; font-size: 13px;' class='formo' value=\"$titre\" size='40' $onfocus><p>";

	

	if ($new != 'oui') echo "<input type='hidden' name='id_rubrique_old' value=\"$id_rubrique\" >";
       

	if ($spip_ecran == "large") $rows = 28;
	else $rows = 20;

	echo "<b>"._T('spiplistes:txt_inscription')."</b>";
	echo "<br />"._T('spiplistes:txt_abonnement');
	
	//echo "<br />";
	echo afficher_barre('document.formulaire.texte');
    echo "<textarea id='text_area' NAME='texte' ".$GLOBALS['browser_caret']." CLASS='formo' ROWS='5' COLS='40' wrap=soft>";    		echo $texte;
 	echo "</textarea>\n";

	if ($champs_extra) {
		include_spip('inc/extra');
		extra_saisie($extra, 'articles', $id_secteur);
	}
	
		$pied = $pied_page ;
	
		if($pied =='')
		{
		include_spip('public/assembler');
		$contexte_pied = array('lang'=>$lang);
		$pied = recuperer_fond('modeles/piedmail', $contexte_pied);
		}	
	
		echo "<p><b>Texte du pied de page</b>";
		//echo aide ("artdesc");
		//echo "<br />"._T('texte_contenu_article')."<br />";
		echo "<br />(Message ajout&eacute; en bas de chaque email au moment de l'envoi)<br />";
		echo "<input type='hidden' name='pied_page' value='$pied'>";
		

		echo "<div style='background-color:#ffffff'>";
		echo ($pied_page!='')? $pied_page : $pied ;
		echo "</div>";
	

	
	
	

	if ($date)
		echo "<input type='Hidden' name='date' value=\"$date\" size='40'><p>";

	if ($new == "oui")
		echo "<input type='Hidden' name='statut_nouv' value=\"inact\" size='40'><p>";

	echo "<div align='right'>";
	echo "<input class='fondo' type='submit' name='Valider' value='"._T('bouton_valider')."'>";
	echo "</div></form>";
	
	

echo fin_cadre_formulaire();






// MODE CREER LISTE FIN --------------------------------------------------------

echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;

    echo fin_gauche(), fin_page();

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
