<?php

// exec/spiplistes_courrier_previsu.php
// _SPIPLISTES_EXEC_COURRIER_PREVISUE

// utilise par _SPIPLISTES_EXEC_COURRIER_EDIT

/******************************************************************************************/
/* SPIP-listes est un systeme de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
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

// adapte de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com

/*
	Affiche previsu d'un courrier
	- en plein ecran si demande
	- sinon pour import iframe
	- format html ou texte seul, si demande
	
	Utilise par courrier_gerer et courrier_edit
	
	CP-20080322 : 
	- ce script devrait plutot etre en action/ au lieur d'exec/ ?
	- charset en previsu plein ecran texte seul : Mozilla affiche parfois en iso ? parfois respecte UTF-8 !
	CP-20071011
*/

function exec_spiplistes_courrier_previsu () {
//spiplistes_log("exec_spiplistes_courrier_previsu(), _SPIPLISTES_LOG_DEBUG");
	global $meta;

	include_spip('base/abstract_sql');
	include_spip('inc/presentation');
	include_spip('inc/distant');
	include_spip('inc/date');
	include_spip('inc/urls');
	include_spip('inc/meta');
	include_spip('inc/filtres');
	include_spip('inc/lang');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_courrier');
	include_spip('inc/spiplistes_api_abstract_sql');
	include_spip('public/assembler');
	
	$int_values = array(
		'id_rubrique', 'id_mot', 'id_courrier', 'id_liste'
		, 'annee', 'mois', 'jour', 'heure', 'minute'
	);
	$str_values = array(
		'lang'
		, 'avec_intro', 'message_intro'
		, 'avec_patron', 'patron', 'patron_pos'
		, 'avec_sommaire'
		, 'titre', 'message'
		, 'Confirmer', 'date'
		, 'lire_base', 'format', 'plein_ecran'
		, 'date_sommaire'
	);

	foreach(array_merge($str_values, $int_values) as $key) {
		$$key = _request($key);
//spiplistes_log("$key :-: ".$$key);
	}
	foreach($int_values as $key) {
		$$key = intval($$key);
	}
	
	$date = format_mysql_date($annee,$mois,$jour,$heure,$minute);
	
	$charset = $meta['charset'];
	
	$texte_lien_courrier =
		(spiplistes_pref_lire('opt_lien_en_tete_courrier') == 'oui')
		? spiplistes_lien_courrier_html_get(
			spiplistes_pref_lire('lien_patron')
			, generer_url_public('courrier', "id_courrier=$id_courrier")
			)
		: ""
		;

	$pied_page = ($id_liste) ? spiplistes_pied_de_page_liste($id_liste, $lang) : "";
	
	$texte_editeur =
		(spiplistes_pref_lire('opt_ajout_tampon_editeur') == 'oui')
		? spiplistes_tampon_html_get(spiplistes_pref_lire('tampon_patron'))
		: ""
		;
	
	$texte_intro = $texte_patron = $texte_sommaire = "";
	
	if($lire_base) {
		// prendre le courrier enregistre dans la base
		$sql_select = 'texte,titre' . (($format=='texte') ? ',message_texte' : '');
		
		if(
			$id_courrier 
			&& ($row = sql_fetsel($sql_select, "spip_courriers", "id_courrier=".sql_quote($id_courrier), "", "", 1))
		) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = propre($row[$key]);
			}
			
			if($plein_ecran) {
			
				$texte_html = ""
					. $texte_lien_courrier
					. $texte
					. $pied_page
					. $texte_editeur
					;
					
				if($format=="texte") {
				
					header("Content-Type: text/plain ; charset=$charset");
					
					// forcer IE a afficher en ligne. 
					header("Content-Disposition: inline; filename=spiplistes-previsu.txt");

					$message_texte = 
						empty($message_texte) 
						? spiplistes_courrier_version_texte($texte_html) 
						: spiplistes_courrier_version_texte($texte_lien_courrier).$message_texte.spiplistes_courrier_version_texte($pied_page).spiplistes_courrier_version_texte($texte_editeur)
						;
					echo($message_texte);
					exit(0);
				}
				// else 
				$texte_html = ""
					. "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Strict//EN\">\n"
					. (($lang) ? "<html lang='$lang' dir='ltr'>\n" : "")
					. "<head>\n"
					. "<meta http-equiv='Content-Type' content='text/html; charset=".$charset."'>\n"
					. "<meta http-equiv='Pragma' content='no-cache'>\n"
					. "<title>".textebrut($titre)."</title>\n"
					. "</head>\n"
					. "<body style='text-align:center;'>\n"
					. "<div style='margin:0 auto;'>\n"
					. $texte_html
					. "</div>\n"
					. "</body>\n"
					. "</html>\n";
				ajax_retour($texte_html);
				exit(0);
			} // end if plein_ecran
			echo($texte);
		}
		else {
			echo(_T('spiplistes:Erreur_courrier_introuvable'));
		}
	}
	else {
	
		if($avec_intro == 'oui') {
		
			$message_intro = "<div>".propre($message_intro)."</div>\n";
		
		} // end if($avec_intro == 'oui')

		if($avec_patron == 'oui') {
			// generer le contenu (editeur)
			include_spip('public/assembler');	
			$contexte_template = array(
				'date' => trim ($date)
				, 'id_rubrique' => $id_rubrique
				, 'id_mot' => $id_mot
				, 'patron' => $patron
				, 'lang' => $lang
				, 'sujet' => $titre
				, 'message' => $message 
			);
			
			if (find_in_path('patrons/'.$patron.'_texte.html')){
				$patron_version_texte = true ;
				$message_texte =  recuperer_fond('patrons/'.$patron.'_texte', $contexte_template);
			}
		

			// Il faut utiliser recuperer_page et non recuperer_fond car sinon les url des articles
			// sont sous forme privee : spip.php?action=redirect&.... horrible !
			// pour utiliser recuperer_fond,il faudrait etre ici dans un script action
			//	$texte_patron = recuperer_fond('patrons/'.$template, $contexte_template);
	
			$titre = $titre_patron = _T('spiplistes:lettre_info')." ".$nomsite;
			$texte = $texte_patron = recuperer_fond('patrons/'.$patron, $contexte_template);
		
		} // end if($avec_patron == 'oui')
		
		if($avec_sommaire == 'oui') {

			if($id_rubrique > 0) {
				
				$sql_where = array("id_rubrique=".sql_quote($id_rubrique));
				
				if($date_sommaire == 'oui') {
					$sql_where[] = "date >= " . sql_quote($date);
				}
				if($sql_result = sql_select("titre,id_article"
					, "spip_articles"
					, $sql_where
					)) {
					while($row = sql_fetch($sql_result)) {
						$texte_sommaire .= "<li> <a href='"
							. generer_url_article($row['id_article'])
							. "'>"
							. typo($row['titre'])
							. "</a></li>\n";
					}
				}
			}
		
			if($id_mot > 0) {
				if($sql_result = sql_select("a.titre,a.id_article"
					, "spip_articles AS a LEFT JOIN spip_mots_articles AS m ON a.id_article=m.id_article"
					, "m.id_mot=".sql_quote($id_mot)." AND a.date >= " . sql_quote($sql_date)
					)) {
					while($row = sql_fetch($sql_result)) {
						$texte_sommaire .= "<li> <a href='"
							. generer_url_article($row['id_article'])
							. "'>"
							. typo($row['titre'])
							. "</a></li>\n";
					}
				}
			}
			
			if(!empty($texte_sommaire)) {
				$texte_sommaire = "<ul>" . $texte_sommaire . "</ul>\n";
				$texte = 
					($patron_pos == "avant")
					? $texte . $texte_sommaire
					: $texte_sommaire . $texte
					;
			}
		
		} // end if($avec_sommaire == 'oui')

		$form_action = ($id_courrier) 
			? generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER,"id_courrier=$id_courrier")
			: generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER)
			;
	
		$contexte_pied = array('lang'=>$lang);
		$texte_pied = recuperer_fond('modeles/piedmail', $contexte_pied);
		
		$texte = $message_intro . $texte;
		
		$page_result = ""
			// boite courrier au format html
			. debut_cadre_couleur('', true)
			. "<form id='choppe_patron-1' action='$form_action' method='post' name='choppe_patron-1'>\n"
			. _T('spiplistes:version_html')
			. "<input type='hidden' name='modifier_message' value='oui' />\n"
			.	(
					($id_courrier)
					?	"<input type='hidden' name='id_courrier' value='$id_courrier' />\n"
					:	"<input type='hidden' name='new' value='oui' />\n"
				)
			. "<input type='hidden' name='titre' value=\"".htmlspecialchars($titre)."\">\n"
			. "<input type='hidden' name='texte' value=\"".htmlspecialchars($texte)."\">\n"
			. "<input type='hidden' name='date' value='$date'>\n"
			. "<div style='background-color:#fff;border:1px solid #000;overflow:scroll;'>\n"
			. liens_absolus($texte)
			. $message_erreur
			. $texte_pied
			. $texte_editeur
			. "</div>\n"
			. "<p style='text-align:right;margin-bottom:0;'><input type='submit' name='btn_courrier_valider' value='"._T('bouton_valider')."' class='fondo' /></p>\n"
			. "</form>\n"
			. fin_cadre_couleur(true)
			. "<br />\n"
			;
		echo($page_result);
	}
}	

/******************************************************************************************/
/* SPIP-listes est un systeme de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
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