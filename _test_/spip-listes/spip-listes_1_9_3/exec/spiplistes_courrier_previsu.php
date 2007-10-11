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

include_spip('inc/presentation');
include_spip('inc/distant');
include_spip('inc/affichage');
include_spip('inc/meta');
include_spip('inc/filtres');
include_spip('inc/lang');

// adapté de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com

function exec_spiplistes_courrier_previsu(){

	foreach(array('patron', 'titre', 'message', 'Confirmer', 'date', 'id_rubrique', 'id_rubrique', 'id_mot', 'id_courrier') as $key) {
		$$key = _request($key);
	}
	$id_courrier = intval($id_courrier);
	$charset = lire_meta('charset');
	
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
		. "</div>\n"
		. "<p style='text-align:right;margin-bottom:0;'><input type='submit' name='btn_courrier_valider' value='"._T('bouton_valider')."' class='fondo' /></p>\n"
		. "</form>\n"
		. fin_cadre_couleur(true)
		. "<br />\n"
		;
	echo($page_result);

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