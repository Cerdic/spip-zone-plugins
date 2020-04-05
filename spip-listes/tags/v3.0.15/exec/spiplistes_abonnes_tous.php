<?php
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$
 
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

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api_globales');
include_spip('inc/spiplistes_listes_selectionner_auteur');

function exec_spiplistes_abonnes_tous () {

	include_spip('inc/presentation');
	include_spip('inc/mots');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	$flag_autorise = ($connect_statut == '0minirezo');
	
	if($flag_autorise) {
	
		$total_auteurs_elligibles = spiplistes_auteurs_elligibles_compter();
		
		//Total des auteurs qui ne sont pas abonnes a une liste
		$nb_abonnes_a_rien = spiplistes_auteurs_non_abonnes_compter();

		//evaluer les formats de tous les auteurs + compter tous les auteurs
		$sql_result = sql_select(
			"`spip_listes_format` AS format, COUNT(`spip_listes_format`) AS nb"
			, 'spip_auteurs_elargis', '', "`spip_listes_format`"
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
		// etablit l'inventaire des listes
		// tries pas statut de la liste
		$nb_abonnes_listes = array();
		while ($row = sql_fetch($sql_result)) {
			$nb_abonnes_listes[$row['statut']] = intval($row['nb']);
		}
	}
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:suivi');
	// Permet entre autres d'ajouter les classes a' la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = 'abonnes_tous';

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page( _T('spiplistes:spiplistes') . " - " . $titre_page, $rubrique, $sous_rubrique));
	
	// la gestion des abonnés est réservée aux admins 
	if(!$flag_autorise) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	$page_result = ''
		. '<br class="debut-page" />'.PHP_EOL
		. spiplistes_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		;

	// formulaire de recherche 
	if ($total_auteurs_elligibles > 1) {
		$page_result .= ""
			. debut_cadre_relief("contact_loupe-24.png", true, "", _T('spiplistes:chercher_un_auteur'))
			. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE)."' method='post' class='verdana2'>"
			. "<div style='text-align:center'>\n"
			. "<input type='text' name='cherche_auteur' class='fondl' value='' size='20' />"
			. "<div style='text-align:right;margin-top:0.5em;'><input type='submit' name='Chercher' value='"._T('bouton_chercher')."' class='fondo' /></div>"
			. "</div></form>"
			. fin_cadre_relief(true)
			;
	}

	$page_result .= ""
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		. spiplistes_boite_autocron()
		. spiplistes_boite_info_spiplistes(true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. debut_droite($rubrique, true)
		// boite résultat Recherche d'auteur
		. spiplistes_cherche_auteur()
		;
	
	// première boite des stats
	$page_result .= ''
		. debut_cadre_trait_couleur('forum-interne-24.gif', true)
		. spiplistes_titre_boite_info(_T('spiplistes:abonnes_titre'))
		. '<div class="verdana2" style="position:relative;margin:1ex;height:14em">'
		// bloc de gauche. Répartition des abonnés.
		. '<div style="position:absolute;top:0;left:0;width:250px" id="info_abo">'
		. PHP_EOL
		. '<p style="margin:0;">'._T('spiplistes:repartition_abonnes').' : </p>'
		. PHP_EOL
		. '<ul style="margin:0;padding:0 1ex;list-style:none">' . PHP_EOL

		// Total des abonnés listes privées (internes)
		. '<li>- '._T('spiplistes:listes_diffusion_prive') . ': '
			. (0 + $nb_abonnes_listes[_SPIPLISTES_LIST_PRIVATE])
			. '</li>' . PHP_EOL
		// Total des abonnés listes périodiques (hebdomadaires)
	 	. '<li>- '. _T('spiplistes:listes_diffusion_hebdo') . ': '
			. (0 + $nb_abonnes_listes[_SPIPLISTES_LIST_PUB_HEBDO] 
				+ $nb_abonnes_listes[_SPIPLISTES_LIST_PUB_WEEKLY])
			. '</li>' . PHP_EOL
		// privees hebdo
	 	. '<li>- '. _T('spiplistes:listes_privees_hebdo') . ': '
			. (0 + $nb_abonnes_listes[_SPIPLISTES_LIST_PRIV_HEBDO] 
				+ $nb_abonnes_listes[_SPIPLISTES_LIST_PRIV_WEEKLY])
			. '</li>' . PHP_EOL
		// Total des abonnés listes périodiques (mensuels)
	 	. '<li>- '. _T('spiplistes:listes_diffusion_mensuelle') . ': '
			. (0 + $nb_abonnes_listes[_SPIPLISTES_LIST_PUB_MENSUEL] 
				+ $nb_abonnes_listes[_SPIPLISTES_LIST_PUB_MONTHLY])
			. '</li>' . PHP_EOL
		// privees mensuelles
	 	. '<li>- '. _T('spiplistes:listes_privees_mensuelle') . ': '
			. (0 + $nb_abonnes_listes[_SPIPLISTES_LIST_PRIV_MENSUEL] 
				+ $nb_abonnes_listes[_SPIPLISTES_LIST_PRIV_MONTHLY])
			. '</li>' . PHP_EOL
		// Total des abonnés listes périodiques (annuelles)
	 	. '<li>- '. _T('spiplistes:listes_diffusion_annuelle') . ': '
			. (0 + $nb_abonnes_listes[_SPIPLISTES_LIST_PUB_YEARLY])
			. '</li>' . PHP_EOL
		// privees annuelles
	 	. '<li>- '. _T('spiplistes:listes_privees_annuelle') . ': '
			. (0 + $nb_abonnes_listes[_SPIPLISTES_LIST_PRIV_YEARLY])
			. '</li>' . PHP_EOL
		// Total des abonnés listes périodiques (periode ou envoi manuel)
	 	. '<li>- '. _T('spiplistes:listes_autre_periode') . ': '
			. (0 + $nb_abonnes_listes[_SPIPLISTES_LIST_PUBLIC] 
				+ $nb_abonnes_listes[_SPIPLISTES_LIST_PUB_DAILY])
			. '</li>' . PHP_EOL
		// privees quotidiennes
	 	. '<li>- '. _T('spiplistes:listes_privees_autre_periode') . ': '
			. (0 + $nb_abonnes_listes[_SPIPLISTES_LIST_PRIVATE] 
				+ $nb_abonnes_listes[_SPIPLISTES_LIST_PRIV_DAILY])
			. '</li>' . PHP_EOL
		// Total des non abonnés
	 	. '<li>- '. _T('spiplistes:abonne_aucune_liste') . ': '.$nb_abonnes_a_rien
			. '</li>' . PHP_EOL
		. '</ul>' . PHP_EOL
		. '</div>' . PHP_EOL

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

	$id_boite_dest_ajax = 'auteurs';
	
	$page_result .= 
		debut_cadre_relief('redacteurs-24.gif', true)
		. "<div id='$id_boite_dest_ajax'>\n"
		//. spiplistes_listes_boite_abonnes(0, $tri, $debut, _SPIPLISTES_EXEC_ABONNES_LISTE, $id_boite_dest_ajax)
		. spiplistes_listes_boite_abonnements(
			0, false, $tri, $debut, _SPIPLISTES_EXEC_ABONNES_LISTE
			)
		. "</div>\n"
		. fin_cadre_relief(true)
		;
	echo($page_result);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();
}



/*
 * @return string boite de selection des auteurs trouves
 */
function spiplistes_cherche_auteur () {
	if (!$cherche_auteur = _request('cherche_auteur')) return;
	
	$col = strpos($cherche_auteur, '@') !== false ? 'email' : 'nom';
	$like = '';
	if (strpos($cherche_auteur, '%') !== false) {
		$like = " WHERE $col LIKE '" . $cherche_auteur . "'";
		$cherche_auteur = str_replace('%', ' ', $cherche_auteur);
	}
	
	$sql_result = sql_select("id_auteur,$col", "spip_auteurs", $like);
	
	while($row = sql_fetch($sql_result)) {
		$table_auteurs[] = $row[$col];
		$table_ids[] = $row['id_auteur'];
	}
	
	$resultat = mots_ressemblants($cherche_auteur, $table_auteurs, $table_ids);

	$result = ""
		. "<div id='boite-result-chercher-auteur'>"
		. debut_boite_info(true)
		;
	if (!$resultat) {
		$result .= ""
			. "<strong>"._T('texte_aucun_resultat_auteur', array('cherche_auteur' => $cherche_auteur)).".</strong><br />\n"
			;
	}
	else if (count($resultat) == 1) {
		list(, $nouv_auteur) = each($resultat);
		$result .= ""
			. "<strong>"._T('spiplistes:une_inscription')."</strong>:<br />\n"
			. "<ul>"
			;
		$sql_result = sql_select("id_auteur,nom,email,bio", "spip_auteurs", "id_auteur=".sql_quote($nouv_auteur), '', '', 1);
		while ($row = sql_fetch($sql_result)) {
			$id_auteur = $row['id_auteur'];
			$nom_auteur = $row['nom'];
			$email_auteur = $row['email'];
			$bio_auteur = $row['bio'];

			$result .= ""
				. "<li class='auteur'>"
					. "<a class='nom_auteur' href=\"".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNE_EDIT, "id_auteur=$id_auteur")."\">".typo($nom_auteur)."</a>"
				. " | $email_auteur"
				. "</li>\n"
				;
		}
		$result .= ""
			. "</ul>\n"
			;
	}
	else if (count($resultat) < 16) {
		reset($resultat);
		unset($les_auteurs);
		while (list(, $id_auteur) = each($resultat)) {
			$les_auteurs[] = $id_auteur;
		}
		if($les_auteurs) {
			$les_auteurs = join(',', $les_auteurs);
			$result .= ""
				. "<strong>"._T('texte_plusieurs_articles', array('cherche_auteur' => $cherche_auteur))."</strong><br />"
				. "<ul>"
				;
			$sql_select = array('id_auteur','nom','email','bio');
			$sql_result = sql_select($sql_select, "spip_auteurs", "id_auteur IN ($les_auteurs)", '', array('nom'));
			while ($row = sql_fetch($sql_result)) {
				$id_auteur = $row['id_auteur'];
				$nom_auteur = $row['nom'];
				$email_auteur = $row['email'];
				$bio_auteur = $row['bio'];
				
				$result .= ""
					. "<li class='auteur'><span class='nom_auteur'>".typo($nom_auteur)."</span>"
					;
				if ($email_auteur) {
					$result .= ""
						. " ($email_auteur)"
						;
				}
				$result .= ""
					. " | <a href=\"".generer_url_ecrire(_SPIPLISTES_EXEC_ABONNE_EDIT,"id_auteur=$id_auteur")."\">"
					. _T('spiplistes:choisir')."</a>"
					;
				if (trim($bio_auteur)) {
					$result .= ""
						. "<br /><font size=1>".couper(propre($bio_auteur), 100)."</font>\n"
						;
				}
				$result .= ""
					. "</li>\n"
					;
			}
			$result .= ""
				. "</ul>\n"
				;
		}
	}
	else {
		$result .= ""
			. "<strong>"._T('texte_trop_resultats_auteurs', array('cherche_auteur' => $cherche_auteur))."</strong><br />"
			;
	}
	
	$result .= ""
		. fin_boite_info(true)
		. "</div>"
		;

	return($result);
} // end spiplistes_cherche_auteur()

function mots_ressemblants($mot, $table_mots, $table_ids='') {

	$result = array();

	if (!$table_mots) return $result;

	$lim = 2;
	$nb = 0;
	$opt = 1000000;
	$mot_opt = '';
	$mot = reduire_mot($mot);
	$len = strlen($mot);

	while (!$nb AND $lim < 10) {
		reset($table_mots);
		if ($table_ids) reset($table_ids);
		while (list(, $val) = each($table_mots)) {
			if ($table_ids) list(, $id) = each($table_ids);
			else $id = $val;
			$val2 = trim($val);
			if ($val2) {
				if (!isset($distance[$id])) {
					$val2 = reduire_mot($val2);
					$len2 = strlen($val2);
					if ($val2 == $mot)
						$m = -2; # resultat exact
					else if (substr($val2, 0, $len) == $mot)
						$m = -1; # sous-chaine
					else {
						# distance
						$m = levenshtein255($val2, $mot);
						# ne pas compter la distance due a la longueur
						$m -= max(0, $len2 - $len);
					}
					$distance[$id] = $m;
				} else $m = 0;
				if ($m <= $lim) {
					$selection[$id] = $m;
					if ($m < $opt) {
						$opt = $m;
						$mot_opt = $val;
					}
					$nb++;
				}
			}
		}
		$lim += 2;
	}

	if (!$nb) return $result;
	reset($selection);
	if ($opt > -1) {
		$moy = 1;
		while(list(, $val) = each($selection)) $moy *= $val;
		if($moy) $moy = pow($moy, 1.0/$nb);
		$lim = ($opt + $moy) / 2;
	}
	else $lim = -1;

	reset($selection);
	while (list($key, $val) = each($selection)) {
		if ($val <= $lim) {
			$result[] = $key;
		}
	}
	return $result;
}

// reduit un mot a sa valeur translitteree et en minuscules
function reduire_mot($mot) {
	return strtr(
			translitteration(trim($mot)),
			'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'abcdefghijklmnopqrstuvwxyz'
	);
}

// ne pas faire d'erreur si les chaines sont > 254 caracteres
function levenshtein255 ($a, $b) {
	$a = substr($a, 0, 254);
	$b = substr($b, 0, 254);
	return @levenshtein($a,$b);
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
