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

// $Revision$

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_liste_edit(){

	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/affichage');
	include_spip('base/spip-listes');
	
	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;
	
	// initialise les variables postées par le formulaire
	foreach(array(
		'new'	// nouvelle liste si 'oui'
		, 'id_liste'// si modif dans l'éditeur
		, 'titre', 'texte'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_liste') as $key) {
		$$key = intval($$key);
	}

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// la gestion des listes de courriers est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_authorisee() . fin_page());
	}
	
	spip_listes_onglets("messagerie", _T('spiplistes:spip_listes'));

	debut_gauche();
	spip_listes_raccourcis();
	creer_colonne_droite();
	debut_droite("messagerie");
	
	// MODE CREER_LISTE: ajout liste------------------------------------------------
	
	// securite
	$lier_trad = intval($lier_trad); // ?? semble attaché à rien
	unset ($flag_editable);
	
	if($id_liste > 0) {
	///////////////////////////////
	// Modification de la liste transmise
	//
		$sql_select = "titre,lang,pied_page,texte,date,statut";
		$sql_result = spip_query("SELECT ".$sql_select." FROM spip_listes WHERE id_liste=$id_liste LIMIT 1");
	
		if ($row = spip_fetch_array($sql_result)) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = $row[$key];
			}
			$id_mod_liste = spiplistes_mod_listes_get_id_auteur($id_liste);
			// supers-adins et moderateur seuls ont droit de modfier la liste
			$flag_editable = ($connect_toutes_rubriques || ($connect_id_auteur == $id_mod_liste));
		}
	} 
	elseif ($new=='oui') {
	///////////////////////////////
	// Creation de la liste
	//
		$titre = filtrer_entites(_T('spiplistes:Nouvelle_liste_de_diffusion'));
		$texte = "";
		$onfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		$flag_editable = true;
	}
		
	echo debut_cadre_formulaire();

	
	echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
	echo "<tr width='100%'>";
	echo "<td>";
	if ($lier_trad) 
		icone(_T('icone_retour'), generer_url_ecrire("listes","id_liste=$lier_trad"), "article-24.gif", "rien.gif");
	else 
		icone(_T('icone_retour'), generer_url_ecrire("listes","id_liste=$id_liste"), "article-24.gif", "rien.gif");
	
	echo "</td>";
	echo "<td><img src='"._DIR_IMG_PACK."/rien.gif' width=10></td>\n";
	echo "<td width='100%'>";
	echo _T('spiplistes:modifier_liste');
	gros_titre($titre);
	echo "</td></tr></table>";
	echo "<p>";
	
	echo "<p><HR><p>";
	
	$titre = entites_html($titre);
	$descriptif = entites_html($descriptif);
	$texte = entites_html($texte);
	
	$lien = generer_url_ecrire('listes');
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
	echo "<textarea id='text_area' NAME='texte' ".$GLOBALS['browser_caret']." CLASS='formo' ROWS='5' COLS='40' wrap=soft>";
	echo $texte;
 	echo "</textarea>\n";
	
	$pied = $pied_page ;

	if($pied =='')
	{
		include_spip('public/assembler');
		$contexte_pied = array('lang'=>$lang);
		$pied = recuperer_fond('modeles/piedmail', $contexte_pied);
	}	

	echo _T('spiplistes:texte_pied');
	//echo aide ("artdesc");
	//echo "<br />"._T('texte_contenu_article')."<br />";
	echo _T('spiplistes:texte_contenu_pied');
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
	
	echo __plugin_html_signature(true), fin_gauche(), fin_page();

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
