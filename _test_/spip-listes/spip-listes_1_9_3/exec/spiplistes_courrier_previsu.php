<?php
// _SPIPLISTES_EXEC_COURRIER_PREVUE
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

include_spip('inc/spiplistes_api_globales');


// adapté de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com

/*
	Affiche prévisu d'un courrier
	- en plein écran si demandé
	- sinon pour import iframe
	- format html ou texte seul, si demandé
	
	Utilisé par courrier_gerer et courrier_edit
	
	CP-20071011
*/

function exec_spiplistes_courrier_previsu(){
spip_log("exec_spiplistes_courrier_previsu()");

	include_spip('inc/presentation');
	include_spip('inc/distant');
	include_spip('inc/urls');
	include_spip('inc/meta');
	include_spip('inc/filtres');
	include_spip('inc/lang');
	include_spip('inc/spiplistes_api');
	
	foreach(array('patron', 'titre', 'message', 'Confirmer', 'date', 'id_rubrique', 'id_rubrique', 'id_mot', 'id_courrier', 'id_liste'
		, 'lire_base', 'format', 'plein_ecran') as $key) {
		$$key = _request($key);
	}
	foreach(array('id_courrier', 'id_liste') as $key) {
		$$key = intval($$key);
	}

	$charset = lire_meta('charset');
	
	$texte_lien_courrier =
		(__plugin_lire_key_in_serialized_meta('opt_lien_en_tete_courrier', _SPIPLISTES_META_PREFERENCES) == 'oui')
		? spiplistes_lien_courrier_html_get(
			__plugin_lire_key_in_serialized_meta('lien_patron', _SPIPLISTES_META_PREFERENCES)
			, generer_url_public('courrier', "id_courrier=$id_courrier")
			)
		: ""
		;

	$pied_page = ($id_liste) ? spiplistes_pied_de_page_liste($id_liste, $lang) : "";
	
	$texte_editeur =
		(__plugin_lire_key_in_serialized_meta('opt_ajout_tampon_editeur', _SPIPLISTES_META_PREFERENCES) == 'oui')
		? spiplistes_tampon_html_get(__plugin_lire_key_in_serialized_meta('tampon_patron', _SPIPLISTES_META_PREFERENCES))
		: ""
		;
	
	if($lire_base) {
		// prendre le courrier enregistré dans la base
		$sql_select = 'texte,titre' . (($format=='texte') ? ',message_texte' : '');
		if($id_courrier && ($row=spip_fetch_array(spip_query("SELECT $sql_select FROM spip_courriers WHERE id_courrier=$id_courrier LIMIT 0,1")))) {
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
				
					header("Content-Type: text/plain charset=$charset");
					
					// forcer IE à afficher en ligne. 
					header("Content-Disposition: inline; filename=spiplistes-previsu.txt");

					$message_texte = 
						empty($message_texte) 
						? version_texte($texte_html) 
						: version_texte($texte_lien_courrier).$message_texte.version_texte($pied_page).version_texte($texte_editeur)
						;
					echo($message_texte);
					exit(0);
				}
				else {
					$texte_html = ""
						. "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Strict//EN\">\n"
						. (($lang) ? "<html lang='$lang' dir='ltr'>\n" : "")
						. "<head>\n"
						. "<meta http-equiv='Content-Type' content='text/html; charset=".$charset."'>\n"
						. "<meta http-equiv='Pragma' content='no-cache'>\n"
						. "<title>$titre</title>\n"
						. "</head>\n"
						. "<body style='text-align:center;'>\n"
						. "<div style='margin:0 auto;'>\n"
						. $texte_html
						. "</div>\n"
						. "</body>\n"
						. "</html>\n";
					ajax_retour($texte_html);
				}
			} // end if plein_ecran
			echo($texte);
		}
		else {
			echo(_T('spiplistes:Erreur_courrier_introuvable'));
		}
	}
	else {
		// générer le contenu (éditeur)
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

		$form_action = ($id_courrier) 
			? generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER,"id_courrier=$id_courrier")
			: generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER)
			;
	
		$contexte_pied = array('lang'=>$lang);
		$texte_pied = recuperer_fond('modeles/piedmail', $contexte_pied);
		
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
?>