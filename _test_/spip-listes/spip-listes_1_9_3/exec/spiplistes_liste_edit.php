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
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_spiplistes_liste_edit(){

	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/spiplistes_api');
	include_spip('base/spip-listes');
	include_spip('inc/spiplistes_naviguer_paniers');
	
	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_ecran
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

	$flag_editable = false;
	$clearonfocus = "";
	
	// MODE LISTE EDIT: modification ou creation
	
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
			// supers-admins et moderateur seuls ont droit de modifier la liste
			$id_mod_liste = spiplistes_mod_listes_get_id_auteur($id_liste);
			$flag_editable = ($connect_toutes_rubriques || ($connect_id_auteur == $id_mod_liste));
		}
		else {
			// liste perdue ?
			$id_liste = 0;
		}
	} 
	
	if(!$id_liste) {
	///////////////////////////////
	// Creation de la liste
	//
		$titre = filtrer_entites(_T('spiplistes:Nouvelle_liste_de_diffusion'));
		$texte = "";
		$clearonfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		// supers-admins seuls ont droit de créer une liste
		$flag_editable = $connect_toutes_rubriques;
	}

	// construit le pied
	$pied_page = spiplistes_pied_de_page_liste($id_liste, $lang);
	
	$gros_bouton_retour = icone(
		_T('spiplistes:retour_link')
		, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=" . ($lier_trad ? $lier_trad : $id_liste) )
		, "article-24.gif"
		, "rien.gif"
		, ""
		, false
		)
		;
		

//////////
// PAGE CONTENU
//////////

	debut_page(_T('spiplistes:spip_listes'), "redacteurs", "spiplistes");

	// la gestion des listes de courriers est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	spiplistes_onglets(_SPIPLISTES_RUBRIQUE, _T('spiplistes:spip_listes'));

	debut_gauche();
	spiplistes_boite_info_id(_T('spiplistes:liste_numero'), $id_liste, false);
	spiplistes_naviguer_paniers_listes(_T('spiplistes:Aller_aux_listes'));
	spiplistes_boite_raccourcis();
	spiplistes_boite_info_spiplistes();
	creer_colonne_droite();
	debut_droite("messagerie");
	
	$titre = entites_html($titre);
	$texte = entites_html($texte);
	
	$formulaire_action = 
		($id_liste)
		? generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER)
		: generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste")
		;

	$page_result = ""
		. debut_cadre_formulaire("", true)
		. "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>"
		. "<tr width='100%'>"
		. "<td>"
		. $gros_bouton_retour
		. "</td>"
		. "<td><img src='"._DIR_IMG_PACK."/rien.gif' width='10'></td>\n"
		. "<td width='100%'>"
		.	(
				(!$id_liste)
				? _T('spiplistes:Creer_une_liste_')
				: _T('spiplistes:modifier_liste')
			) . ":"
		. gros_titre($titre, '', false)
		. "</td>"
		. "</tr></table>"
		. "<hr />"
		. "<form action='$formulaire_action' method='post' name='formulaire'>\n"
		. (
			($id_liste)
			? "<input type='hidden' name='id_liste' value='$id_liste' />" 
			: "<input type='hidden' name='new' value='oui' />"
				// une nouvelle liste est toujours privée
				. "<input type='hidden' name='statut_nouv' value='"._SPIPLISTES_PRIVATE_LIST."' />"
			)
		.	(
			// ne sert pas pour le moment (CP-20070922)
			($lier_trad)
			? "<input type='hidden' name='lier_trad' value='$lier_trad' />"
			: ""
			)
		. _T('texte_titre_obligatoire').":"
		. "<br />"
		// champ titre
		. "<input type='text' name='titre' class='formo' value=\"$titre\" size='40' $clearonfocus />"
		. "<br />"
		. "<strong>"._T('spiplistes:txt_inscription')."</strong>"
		. "<br />"._T('spiplistes:txt_abonnement')
		// boite édition texte
		. afficher_barre('document.formulaire.texte')
		. "<textarea id='text_area' name='texte' ".$GLOBALS['browser_caret']." class='formo' rows='".(($spip_ecran == "large") ? 28 : 20)."' cols='40' wrap=soft>"
		. $texte
		. "</textarea>\n"
		// pied de page
		. _T('spiplistes:texte_pied')
		. _T('spiplistes:texte_contenu_pied')
		. "<div style='background-color:#fff'>"
		. $pied_page
		. "</div>"
		. "<p align='right' class='verdana2'>"
		. "<input class='fondo' type='submit' name='btn_liste_edit' value='"._T('bouton_valider')."' />"
		. "</p>"
		. "</form>"
		. fin_cadre_formulaire(true)
		;

	echo($page_result);

	// MODE LISTE EDIT: FIN --------------------------------------------------------
	
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
