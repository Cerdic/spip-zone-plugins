<?php

// exec/spiplistes_liste_edit.php

// _SPIPLISTES_EXEC_LISTE_EDIT
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_liste_edit(){

	include_spip('inc/barre');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/spiplistes_naviguer_paniers');
	
	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_ecran
		;
	
	// initialise les variables postees par le formulaire
	foreach(array(
		'new'	// nouvelle liste si 'oui'
		, 'id_liste'// si modif dans l'editeur
		, 'titre', 'texte'
		) as $key) {
		$$key = _request($key);
	}
	foreach(array('id_liste') as $key) {
		$$key = intval($$key);
	}

	$flag_editable = false;
	$clearonfocus = '';
	
	// MODE LISTE EDIT: modification ou creation
	
	if($id_liste > 0) {
	///////////////////////////////
	// Modification de la liste transmise
	//
		// les admins toutes rubriques et le moderateur seuls peuvent modifier la liste
		$flag_editable = autoriser('moderer', 'liste', $id_liste);

		$sql_select_array = array('titre', 'lang', 'pied_page', 'texte', 'date', 'statut');
	
		if($row = spiplistes_listes_liste_fetsel($id_liste, $sql_select_array)) {
			foreach($sql_select_array as $key) {
				$$key = $row[$key];
			}
			// supers-admins et moderateur seuls ont droit de modifier la liste
//			$ids_mods_array = spiplistes_mod_listes_get_id_auteur($id_liste);
//			$ids_mods_array = ($ids_mods_array && isset($ids_mods_array[$id_liste]) ? $ids_mods_array[$id_liste] : array();
//			$flag_editable = ($connect_toutes_rubriques || in_array($connect_id_auteur, $ids_mods_array));
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
		// les admins ont droit de creer une liste
		$flag_editable = ($connect_statut == "0minirezo");
	}

	$gros_bouton_retour = icone(
		_T('spiplistes:retour_link')
		, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER,"id_liste=" . ($lier_trad ? $lier_trad : $id_liste) )
		, "article-24.gif"
		, "rien.gif"
		, ""
		, false
		)
		;
		

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:edition_dune_liste');
	// Permet entre autres d'ajouter les classes a la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "liste_edit";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . " - " . $titre_page, $rubrique, $sous_rubrique));

	// la gestion des listes de courriers est reservee aux admins 
	if(!$flag_editable) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. "<br /><br /><br />\n"
		. spiplistes_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. spiplistes_boite_info_id(_T('spiplistes:liste_numero'), $id_liste, true)
		. spiplistes_naviguer_paniers_listes(_T('spiplistes:aller_aux_listes_'), true)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		//. spiplistes_boite_autocron() // ne pas gener l'edition
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. debut_droite($rubrique, true)
		;

	$titre = entites_html($titre);
	$texte = entites_html($texte);
	
	$formulaire_action = 
		($id_liste > 0)
		? generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER, "id_liste=$id_liste")
		: generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_GERER)
		;

	$page_result .= ""
		. debut_cadre_formulaire("", true)
		. "\n<table cellpadding='0' cellspacing='0' border='0' width='100%'>"
		. "<tr>"
		. "<td>"
		. $gros_bouton_retour
		. "</td>"
		. "<td><img src='"._DIR_IMG_PACK."rien.gif' alt='' width='10' /></td>\n"
		. "<td width='100%'>"
		.	(
				(!$id_liste)
				? _T('spiplistes:Creer_une_liste_')
				: _T('spiplistes:modifier_liste')
			) . ":"
		. spiplistes_gros_titre($titre, '', true)
		. "</td>"
		. "</tr></table>"
		. "<hr />"
		. "<form action='$formulaire_action' method='post' name='formulaire'>\n"
		. (
			($id_liste)
			? "<input type='hidden' name='id_liste' value='$id_liste' />" 
			: "<input type='hidden' name='new' value='oui' />"
				// une nouvelle liste est toujours privee
				. "<input type='hidden' name='statut_nouv' value='"._SPIPLISTES_LIST_PRIVATE."' />"
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
		. "<strong>"._T('spiplistes:texte_dinsctription_')."</strong>"
		. "<br />"._T('spiplistes:txt_abonnement')
		// boite edition texte
		. afficher_barre('document.formulaire.texte')
		. "<textarea id='text_area' name='texte' ".$GLOBALS['browser_caret']
			." class='formo' rows='".(($spip_ecran == "large") ? 28 : 20)."' cols='40'>\n"
		. $texte
		. "</textarea>\n"
		// pied de page
		// inutile de l'afficher, on a pas encore le contexte (lang)
		//. _T('spiplistes:texte_pied')
		//. _T('spiplistes:texte_contenu_pied')
		//. "<div style='background-color:#fff'>"
		//. $pied_page
		//. "</div>"
		. "<p align='right' class='verdana2'>"
		. "<input class='fondo' type='submit' name='btn_liste_edit' value='"._T('bouton_valider')."' />"
		. "</p>"
		. "</form>"
		. fin_cadre_formulaire(true)
		;

	echo($page_result);

	// MODE LISTE EDIT: FIN --------------------------------------------------------
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();

}
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
?>