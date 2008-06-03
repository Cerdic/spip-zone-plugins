<?php

// inc/spiplistes_abonnes_tous.php

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_listes_selectionner_auteur');

function exec_spiplistes_abonnes_tous () {

	include_spip('inc/presentation');
	include_spip('inc/mots');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/spiplistes_afficher_auteurs');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	$flag_autorise = ($connect_statut == "0minirezo");
	
	if($flag_autorise) {
	
		$total_auteurs_elligibles = spiplistes_auteurs_elligibles_compter();
		
		//Total des auteurs qui ne sont pas abonnes a une liste
		$nb_abonnes_a_rien = spiplistes_auteurs_non_abonnes_compter();

		//evaluer les formats de tous les auteurs + compter tous les auteurs
		$sql_result = sql_select(
			"`spip_listes_format` AS format, COUNT(`spip_listes_format`) AS nb"
			, "spip_auteurs_elargis", "", "`spip_listes_format`"
		);
		//repartition des formats
		$total_abonnes_format = 0;
		$nb_abonnes_par_format = array(
			'texte' => 0	// abonnés au format texte
			, 'html' => 0	// au format html
			, 'non' => 0	// qui a été désabonné
			);
		while($row = sql_fetch($sql_result)) {
			$nb_abonnes_par_format[$row['format']] = $row['nb'];
			$total_abonnes_format += $row['nb'];
		}
	
		//Compter tous les abonnes a des listes 
		$sql_result = sql_select(
			"listes.statut AS statut, COUNT(abonnements.id_auteur) AS nb"
			, "spip_listes AS listes LEFT JOIN spip_auteurs_listes AS abonnements USING (id_liste)"
			, "", "listes.statut"
		);
		$nb_abonnes_listes = array();
		while ($row = sql_fetch($sql_result)) {
			$nb_abonnes_listes[$row['statut']] = intval($row['nb']);
		}
	}
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:spip_listes');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "abonnes_tous";

	if(!defined("_AJAX") || !_AJAX) {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo($commencer_page($titre_page, $rubrique, $sous_rubrique));
	}
	
	// la gestion des abonnés est réservée aux admins 
	if(!$flag_autorise) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	$page_result = ""
		. spiplistes_onglets(_SPIPLISTES_RUBRIQUE, $titre_page, true)
		. debut_gauche($rubrique, true)
		;

	// formulaire de recherche 
	if ($total_auteurs_elligibles > 1) {
		$page_result .= ""
			. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."contact_loupe-24.png", true, "", _T('spiplistes:chercher_un_auteur'))
			. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE)."' method='post' class='verdana2'>"
			. "<div align=center>\n"
			. "<input type='text' name='cherche_auteur' class='fondl' value='' size='20' />"
			. "<div style='text-align:right;margin-top:0.5em;'><input type='submit' name='Chercher' value='"._T('bouton_chercher')."' class='fondo' /></div>"
			. "</div></form>"
			. fin_cadre_relief(true)
			;
	}

	$page_result .= ""
		. creer_colonne_droite($rubrique, true)
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron()
		. spiplistes_boite_info_spiplistes(true)
		. debut_droite($rubrique, true)
		// boite résultat Recherche d'auteur
		. spiplistes_cherche_auteur()
		;
	
	// première boite des stats
	$page_result .= ""
		. debut_cadre_trait_couleur("forum-interne-24.gif", true)
		. spiplistes_titre_boite_info(_T('spiplistes:abonnes_titre'))
		. "<div class='verdana2' style='position:relative;margin:1ex;height:8em;'>"
		// bloc de gauche. Répartition des abonnés.
		. "<div style='position:absolute;top:0;left:0;width:250px;' id='info_abo'>"
		. "<p style='margin:0;'>"._T('spiplistes:repartition_abonnes')." : </p>"
		. "<ul style='margin:0;padding:0 1ex;list-style: none;'>"

		// Total des abonnés listes privées (internes)
		. "<li>- "._T('spiplistes:Listes_diffusion_prive') . ": "
			. (0 + $nb_abonnes_listes[_SPIPLISTES_PRIVATE_LIST])
			. "</li>"
		// Total des abonnés listes périodiques (hebdomadaires)
	 	. "<li>- ". _T('spiplistes:Listes_diffusion_hebdo') . ": "
			. (0 + $nb_abonnes_listes[_SPIPLISTES_HEBDO_LIST] 
				+ $nb_abonnes_listes[_SPIPLISTES_WEEKLY_LIST])
			. "</li>"
		// Total des abonnés listes périodiques (mensuels)
	 	. "<li>- ". _T('spiplistes:Listes_diffusion_mensuelle') . ": "
			. (0 + $nb_abonnes_listes[_SPIPLISTES_MENSUEL_LIST] 
				+ $nb_abonnes_listes[_SPIPLISTES_MONTHLY_LIST])
			. "</li>"
		// Total des abonnés listes périodiques (annuelles)
	 	. "<li>- ". _T('spiplistes:Listes_diffusion_annuelle') . ": "
			. (0 + $nb_abonnes_listes[_SPIPLISTES_YEARLY_LIST])
			. "</li>"
		// Total des abonnés listes périodiques (periode ou envoi manuel)
	 	. "<li>- ". _T('spiplistes:Listes_autre_periode') . ": "
			. (0 + $nb_abonnes_listes[_SPIPLISTES_PUBLIC_LIST] 
				+ $nb_abonnes_listes[_SPIPLISTES_DAILY_LIST])
			. "</li>"
		// Total des non abonnés
	 	. "<li>- ". _T('spiplistes:abonne_aucune_liste') . ": ".$nb_abonnes_a_rien. "</li>"
		. "</ul>"
		. "</div>\n"

		// bloc de droite. Répartition des formats.
		. "<div style='position:absolute;top:0;right:0;width:180px;' id='info_fmt'>\n"
		. "<p style='margin:0;'>"._T('spiplistes:repartition_formats')." : </p>\n"
		. "<ul style='margin:0;padding:0 1ex;list-style: none;'>"
		. "<li>- "._T('spiplistes:html')." : {$nb_abonnes_par_format['html']}</li>"
		. "<li>- "._T('spiplistes:texte')." : {$nb_abonnes_par_format['texte']}</li>"
		. "<li>- "._T('spiplistes:format_aucun')." : {$nb_abonnes_par_format['non']}</li>"
		. "</ul>"
		. "</div>\n"
		// fin des infos
		. "</div>\n"
		;


	$page_result .= ""
		. fin_cadre_trait_couleur(true)
		;
		
	////////////////////////////
	// Liste des auteurs
	
	$tri = _request('tri') ? _request('tri') : 'nom';

	$boite_liste_abonnes = spiplistes_listes_boite_abonnes(0, $tri, _SPIPLISTES_EXEC_ABONNES_LISTE);
	
	if(defined("_AJAX") && _AJAX) {
		echo($boite_liste_abonnes);
	} 
	else {
		$page_result .= ""
			. debut_cadre_relief('redacteurs-24.gif', true)
			. "<div id='auteurs'>\n"
			. $boite_liste_abonnes
			. "</div>\n"
			. fin_cadre_relief(true)
			;
		echo($page_result);
		echo __plugin_html_signature(_SPIPLISTES_PREFIX, true), fin_gauche(), fin_page();
	}
}

//CP-200080519
// Nombre total d'auteurs (ou visiteur, ou perso) elligibles
// Nota: un compte 'nouveau' est un compte visiteur (inscription) qui ne s'est pas encore connecté
// Nota2: un compte créé via l'espace privé mais pas encore connecté
// n'a pas le statut 'nouveau' mais celui de son groupe
function spiplistes_auteurs_elligibles_compter () {
	static $nb;
	if(!$nb) {
		$sql_where = array(
			  "statut!=".sql_quote('5poubelle')
			, "statut!=".sql_quote('nouveau')
			);
		$nb = sql_countsel('spip_auteurs', $sql_where);
	}
	return($nb);
}

//CP-200080519
//Total des auteurs qui ne sont pas abonnes a une liste
function spiplistes_auteurs_non_abonnes_compter () {
	static $nb;
	if(!$nb) {
		$selection =
			(spiplistes_spip_est_inferieur_193())
			? "SELECT id_auteur FROM spip_auteurs_listes GROUP BY id_auteur"
			: sql_select("id_auteur", "spip_auteurs_listes", '','id_auteur','','','','',false)
		;
		$sql_where = array(
			  "statut!=".sql_quote('5poubelle')
			, "statut!=".sql_quote('nouveau')
			, "id_auteur NOT IN (".$selection.")"
			);
		$nb = sql_countsel('spip_auteurs', $sql_where);
	}
	return($nb);
}

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'abonn� et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU  */
/* pour plus de d�ails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU                    */
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis.                   */
/******************************************************************************************/
?>