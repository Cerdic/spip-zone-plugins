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

	if($connect_statut == "0minirezo") {
	
		// Nombre total d'auteurs (ou visiteur, ou perso) elligibes
		// Nota: un compte 'nouveau' est un compte visiteur (inscription) qui ne s'est pas encore connecté
		// Nota2: un compte créé via l'espace privé mais pas encore connecté
		// n'a pas le statut 'nouveau' mais celui de son groupe
		$sql_query = "
			SELECT COUNT(id_auteur) AS n 
			FROM spip_auteurs 
			WHERE statut!='5poubelle' AND statut!='nouveau'";
		$row = spip_fetch_array(spip_query($sql_query));
		$total_auteurs = $row['n'];
		
		//Total des auteurs qui ne sont pas abonnes a une liste
		$sql_query = "
			SELECT COUNT(id_auteur) AS n 
			FROM spip_auteurs
			WHERE statut!='5poubelle' AND statut!='nouveau' 
				AND id_auteur NOT IN (SELECT id_auteur FROM spip_auteurs_listes GROUP BY id_auteur)";
		$row = spip_fetch_array(spip_query($sql_query));
		$nb_abonnes_a_rien = $row['n'];

		//evaluer les formats de tous les auteurs + compter tous les auteurs
		$sql_query = "
			SELECT `spip_listes_format`, COUNT(`spip_listes_format`) 
			FROM spip_auteurs_elargis GROUP BY `spip_listes_format`";
		$sql_result = spip_query($sql_query);
		//repartition des formats
		$total_abonnes_format = 0;
		$nb_abonnes_par_format = array(
			'texte' => 0	// abonnés au format texte
			, 'html' => 0	// au format html
			, 'non' => 0	// qui a été désabonné
			);
		while ($row = spip_fetch_array($sql_result, SPIP_NUM)) {
			$nb_abonnes_par_format[$row[0]] = $row[1];
			$total_abonnes_format += $row[1];
		}
	
		//Compter tous les abonnes a des listes 
		$result_pile = spip_query(
		  'SELECT listes.statut, COUNT(abonnements.id_auteur)
			FROM spip_listes AS listes LEFT JOIN spip_auteurs_listes AS abonnements USING (id_liste)
			GROUP BY listes.statut');
		$nb_abonnes_listes = array();
		while ($row = spip_fetch_array($result_pile, SPIP_NUM)) {
			$nb_abonnes_listes[$row[0]] = intval($row[1]);
		}
	}
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:spip_listes');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "abonnes_tous";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));

	// la gestion des abonnés est réservée aux admins 
	if($connect_statut != "0minirezo") {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	$page_result = ""
		. spiplistes_onglets(_SPIPLISTES_RUBRIQUE, $titre_page, true)
		. debut_gauche($rubrique, true)
		;

	// formulaire de recherche 
	if ($total_auteurs > 1) {
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
		. spiplistes_boite_autocron(true)
		. spiplistes_boite_info_spiplistes(true)
		. debut_droite($rubrique, true)
		// boite résultat Recherche d'auteur
		. spiplistes_cherche_auteur(true)
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
	
	$retour = generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE);
	
	$tri = _request('tri') ? _request('tri') : 'nom';
	$retour = parametre_url($retour,"tri",$tri);
	
	//
	// Construire la requete
	//
	
	$sql_visible="1=1"; 
	$partri = " " . _T('info_par_tri', array('tri' => $tri));
	
	$sql_sel = '';
	
	// tri
	switch ($tri) {
		case 'nombre':
			$sql_order = ' ORDER BY compteur DESC, unom';
			$type_requete = 'nombre';
			$partri = " "._T('info_par_nombre_article');
			break;
		case 'statut':
			$sql_order = ' ORDER BY statut, login = "", unom';
			$type_requete = 'auteur';
			$sql_visible = " aut.statut!='5poubelle'";
			break;
		case 'nom':
			$sql_order = ' ORDER BY unom';
			$type_requete = 'auteur';
			$sql_visible = " aut.statut!='5poubelle'";
			break;
		case 'email':
			$sql_order = ' ORDER BY LOWER(email)';
			$type_requete = 'auteur';
			break;
		case 'multi':
		default:
			$type_requete = 'auteur';
			$sql_sel = ", ".creer_objet_multi ("nom", $spip_lang);
			$sql_order = " ORDER BY multi";
	}

	
	// La requete de base est tres sympa
	//
	
	$query = "SELECT
		aut.id_auteur AS id_auteur,
		aut.statut AS statut,
		aut.login AS login,
		aut.nom AS nom,
		aut.email AS email,
		aut.url_site AS url_site,
		aut.messagerie AS messagerie,
		fmt.`spip_listes_format` AS format,
		UPPER(aut.nom) AS unom,
		COUNT(lien.id_liste) as compteur
		$sql_sel
		FROM spip_auteurs as aut
		LEFT JOIN spip_auteurs_listes AS lien ON aut.id_auteur=lien.id_auteur
		LEFT JOIN spip_listes AS art ON (lien.id_liste = art.id_liste)
		LEFT JOIN spip_auteurs_elargis AS fmt ON aut.id_auteur=fmt.id_auteur
		WHERE
		$sql_visible
		GROUP BY aut.id_auteur
		$sql_order";

	$page_result .= ""
		. "<div id='auteurs'>\n"
		. spiplistes_afficher_auteurs($query, generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE), true)
		. "</div>\n"
		;
	
	echo($page_result);

	echo __plugin_html_signature(_SPIPLISTES_PREFIX, true), fin_gauche(), fin_page();
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