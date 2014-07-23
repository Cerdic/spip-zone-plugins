<?php
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$
 
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
/* Vous devez avoir reeu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.                   */
/******************************************************************************************/

/*
	Formulaire de creation de courrier
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function exec_spiplistes_courrier_edit(){

	include_spip('inc/barre');
	include_spip('inc/documents');
	include_spip('base/spiplistes_tables');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');
	include_spip('inc/spiplistes_dater_envoi');
	include_spip('inc/spiplistes_api_courrier');
	include_spip('public/assembler');
	include_spip('inc/spiplistes_naviguer_paniers');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $spip_ecran
		, $compteur_block
		;
	
	$eol = "\n";
	$id_temp = false;
	
	$type = _request('type');
	$id_courrier = intval(_request('id_courrier'));

	foreach(array('btn_courrier_apercu') as $key) {
		$$key = _request($key);
	}

	if($id_courrier > 0) {
	///////////////////////////
	// Edition /modification d'un courrier
		$sql_select_array = array('titre','texte','message_texte','type','statut','id_auteur');
		if($row = spiplistes_courriers_premier($id_courrier, $sql_select_array)) {
			foreach($sql_select_array as $key) {
				$$key = $row[$key];
			}
			$titre = entites_html($titre);
			$texte = entites_html($texte);
		}
		else {
			$id_courrier = false;
		}
	}
	// n'existe pas encore ?
	// placer un marqueur pour les documents joints
	else {
		$id_temp = 0-intval(substr(creer_uniqid(),0,5));
	}

	// l'edition du courrier est reservee aux super-admins 
	// ou aux admin createur du courrier
	$flag_editable = (($connect_statut == "0minirezo") 
		&& ($connect_toutes_rubriques || ($connect_id_auteur == $id_auteur) || !$id_courrier));

	if($flag_editable) {
		if(!$id_courrier) {
		// si pas de ID courrier, c'est une creation
			$statut = _SPIPLISTES_COURRIER_STATUT_REDAC; 
			$type = _SPIPLISTES_COURRIER_TYPE_NEWSLETTER;
			$new = 'oui';
			$titre = _T('spiplistes:nouveau_courrier');
			$clearonfocus = " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
		}
		else {
			$clearonfocus = "";
		}
	
		$gros_bouton_retour =
			($id_courrier)
			? icone_verticale(
				_T('spiplistes:retour_link')
				, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER, "id_courrier=$id_courrier")
				, spiplistes_items_get_item('icon', $statut)
				, "rien.gif"
				, ""
				, false
				)
			: ""
			;
		$boite_documents = afficher_documents_colonne(
							  ($id_courrier ? $id_courrier : $id_temp )
							  , 'courrier');
	}
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('spiplistes:edition_du_courrier');
	// Permet entre autres d'ajouter les classes a' la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = _SPIPLISTES_PREFIX;
	$sous_rubrique = "courrier_edit";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('spiplistes:spiplistes') . " - " . $titre_page, $rubrique, $sous_rubrique));

	if(!$flag_editable) {
		die (spiplistes_terminer_page_non_autorisee() . fin_page());
	}
	
	$page_result = ''
		. '<br class="debut-page" />'.$eol
		. spiplistes_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. spiplistes_boite_info_id(_T('spiplistes:courrier_numero_'), $id_courrier, true)
		. spiplistes_naviguer_paniers_courriers(_T('spiplistes:aller_au_panier_'), true)
		. $boite_documents
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		//. creer_colonne_droite($rubrique, true)  // spiplistes_boite_raccourcis() s'en occupe
		. spiplistes_boite_raccourcis(true)
		//. spiplistes_boite_autocron() // ne pas gener l'edition
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		;
		
	$page_result .= debut_droite($rubrique, true);
		
	$page_result .= '<div id="courrier-contenu">'.$eol;
	
	$page_result .= ''
		// le bloc pour apercu (retour ajax)
		. '<div id="apercu-courrier"></div>'.$eol
		//
		// debut_cadre_formulaire
		. '<div class="cadre-formulaire">'.$eol
		. "<a name='haut-block' id='haut-block'></a>\n"
		;
		
	$page_result .= ''
		// bloc titre
		. "<table cellpadding='0' cellspacing='0' border='0' width='100%'>\n"
		. "<tr width='100%'>"
		. "<td>"
		. $gros_bouton_retour
		. '</td>'
		. '<td id="cel-titre" width="100%">'.$eol
		. ($id_courrier ? _T('spiplistes:modifier_un_courrier__') : _T('spiplistes:creer_un_courrier_') )
		. '<h1>'.$titre.'</h1>'
		. "</td>\n"
		. "</tr></table>\n"
		. "<hr />\n"
		;
		//
		// debut formulaire
	
	$page_result .= ""
		. "<form action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER
											  , ($id_courrier ? "id_courrier=$id_courrier" : "")
											  )
			."' method='post' name='formulaire_courrier_edit' id='formulaire_courrier_edit'>\n"
		. "<input type='hidden' name='modifier_message' value=\"oui\" />\n"
		. "<input type='hidden' name='id_courrier' value='$id_courrier' />\n"
		//
		// bloc sujet du courrier
		. "<label for='sujet_courrier'>"._T('spiplistes:sujet_courrier').":</label>\n"
		. "<input id='sujet_courrier' type='text' class='formo' name='titre' value=\"$titre\" size='40' $clearonfocus />\n"
		. "<p style='margin-bottom:1.75em;'>"._T('spiplistes:courrier_edit_desc').'</p>'.$eol
		;
		
	$titre_block_depliable = _T('spiplistes:generer_le_contenu');
	$ico_loader = chemin_image ( 'ajax_indicator.gif' );
	$page_result .= ''
		//
		// generer le contenu
		// Reprise du Formulaire adapte de abomailman () // MaZiaR - NetAktiv	// tech@netaktiv.com
		. debut_cadre_relief( chemin_image ( 'stock_insert-slide.gif' ), true)
		//. bouton_block_invisible(md5(_T('spiplistes:charger_patron')))
		. spiplistes_bouton_block_depliable($titre_block_depliable, false, md5(_T('spiplistes:charger_patron')))
		. "<span class='verdana2 triangle_label' onclick=\"javascript:$('#triangle".$compteur_block."').click();\">"
			. '</span>'.$eol
		. spiplistes_debut_block_invisible(md5(_T('spiplistes:charger_patron')))
		// 
		. '<div id="ajax-loader" align="right">'.$eol
			. '<script type="text/javascript">'.$eol
			. 'document.write(\'<img src="' . $ico_loader . '" alt="" />\');'	
			. '</script>'.$eol
			. '<noscript>'.$eol
			. spiplistes_boite_alerte (_T('spiplistes:javascript_inactif'), true)
			. $eol
			. '</noscript>'.$eol
		. '</div>'.$eol // #ajax-loader
		;
	
	if(strpos($GLOBALS['meta']['langues_multilingue'], ",") !== false) {
		$langues = liste_options_langues('changer_lang');
		$options = "";
		$default = $GLOBALS['spip_lang'];
		foreach ($langues as $l) {
			$selected = ($l == $default) ? ' selected=\'selected\'' : '';
			$options .= "<option value='$l'$selected>[".$l."] ".traduire_nom_langue($l)."</option>\n";
		}

		$page_result .= ""
			// selecteur de langues
			. "<div class='boite-generer-option'>\n"
			. "<label class='verdana2'>"._T('spiplistes:langue_du_courrier_')
			. "<select name='lang' class='fondo'>\n"
			. $options
			. "</select></label>\n"
			. "</div>\n"
			;
	}
	
	$page_result .= ""
		// Prendre en compte a partir de quelle date ?
		. spiplistes_dater_envoi(
			'courrier', $id_courrier, $statut
			, $flag_editable
			, _T('spiplistes:contenu_a_partir_de_date_')
			, normaliser_date(time()), 'btn_changer_date'
			, false
			)
		;
		
	$page_result .= ""
		// texte introduction a placer avant le patron et sommaire 
		. '<div class="boite-generer-option">'.$eol
		. '<label class="verdana2">'
		. '<input type="checkbox" id="avec_intro" name="avec_intro" value="non" />'
		. _T('spiplistes:avec_introduction')
		. '</label>'.$eol
		. '<div id="choisir_intro" class="option">'.$eol
		. '<label class="verdana2" style="display:block;" for="message_intro">'
		. _T('spiplistes:introduction_du_courrier_').':</label>'.$eol
		. '<textarea id="message_intro" name="message_intro" '.$GLOBALS['browser_caret'].' rows="5" cols="40" wrap="soft" style="width:100%">'.$eol
		. '</textarea>'
		. '</div>'.$eol
		. '</div>'.$eol
		;
		
	// selection du patron
	$page_result .= ''
		. "<div class='boite-generer-option'>\n"
		. "<label class='verdana2'>"
		. "<input type='checkbox' id='avec_patron' name='avec_patron' value='non' />"
		. _T('spiplistes:a_partir_de_patron')
		. "</label>\n"
		. "<div id='choisir_patron' class='option'>"
		. "<label class='verdana2'>"
		. _T('spiplistes:choisir_un_patron_').":</label>\n"
		. spiplistes_boite_selection_patrons ("", true, _SPIPLISTES_PATRONS_DIR, "patron", 1)
		. "<div id='patron_pos' style='display:none'>\n"
		. "<span class='verdana2'>" . _T('spiplistes:generer_patron_'). "</span>\n"
		. spiplistes_form_input_radio ('patron_pos', 'avant', _T('spiplistes:generer_patron_avant'), true, true, false)
		. spiplistes_form_input_radio ('patron_pos', 'apres', _T('spiplistes:generer_patron_apres'), false, true, false)
		. "</div>\n"
		. "</div>\n"
		. "</div>\n"
		;
	
	// Generer un sommaire
	$page_result .= ""
		. "<div class='boite-generer-option'>\n"
		. "<label class='verdana2'>"
		. "<input type='checkbox' id='avec_sommaire' name='avec_sommaire' value='non' />"
		. _T('spiplistes:generer_un_sommaire')
		. "</label>\n"
		. "<div id='choisir_sommaire' class='option'>";		
	$page_result .= ""
		//
		// selecteur de rubriques
		. "<label class='verdana2' for='ajouter_rubrique'>"._T('spiplistes:lister_articles_de_rubrique').":</label>\n"
		. "<select name='id_rubrique' id='ajouter_rubrique' class='formo'>\n"
		. "<option value=''></option>\n"
		. spiplistes_arbo_rubriques()
		. "</select>\n"
		. "<br />\n"
		//
		// selecteur des mots-cles
		. "<label class='verdana2' for='ajouter_motcle'>"._T('spiplistes:lister_articles_mot_cle').":</label>\n"
		. "<select name='id_mot' id='ajouter_motcle' class='formo'>\n"
		. "<option value=''></option>\n"
		;
	
	if (version_compare($GLOBALS['spip_version_code'], '1.9300', '<')){
		$rqt_gmc = sql_select (array('id_groupe','titre'), 'spip_groupes_mots', "articles=".sql_quote('oui'));
	}else{
		$rqt_gmc = sql_select (array('id_groupe','titre'), 'spip_groupes_mots', "tables_liees LIKE '%articles%'");		
	}
	
	while ($row = sql_fetch($rqt_gmc)) {
		$id_groupe = intval($row['id_groupe']);
		$titre = $row['titre'];
		$page_result .= "<option value='' disabled='disabled'>". supprimer_numero (typo($titre)) . "</option>\n";
		$rqt_mc = sql_select (array('id_mot','titre'), 'spip_mots', "id_groupe=".sql_quote($id_groupe));
		while ($row = sql_fetch($rqt_mc)) {
			$id_mot = intval($row['id_mot']);
			$titre = supprimer_numero (typo($row['titre']));
			$page_result .= "<option value='$id_mot'>--&nbsp;$titre</option>\n";
		}
	}
	$page_result .= ""
		. "</select><br />\n"
		//
		// a partir de la date selectionnee plus haut
		. spiplistes_form_input_item ('checkbox', 'date_sommaire', 'oui'
			, _T('spiplistes:sommaire_date_debut'), $sommaire_date == 'oui', true, false)
		. "</div>\n"
		. "</div>\n"
		; // fin generer le sommaire
		
		// choisir son patron de pied
	$page_result .= ""
		. "<div class='boite-generer-option'>\n"
			. "<label class='verdana2'>"
		. _T('spiplistes:avec_patron_pied__')
		. spiplistes_boite_selection_patrons (_SPIPLISTES_PATRONS_PIED_DEFAUT, true, _SPIPLISTES_PATRONS_PIED_DIR, "pied_patron", 1)
		. "</label>\n"
		. "</div>\n"
		; 
		
	$page_result .= ""
		. "<p class='verdana2'>\n"
			. _T('spiplistes:cliquez_generer_desc'
				, array('titre_bouton'=>_T('spiplistes:generer_apercu'), 'titre_champ_texte'=>_T('spiplistes:texte_courrier'))
				)
			. "</p>\n"
		. "<p class='verdana2'>\n" ._T('spiplistes:calcul_patron_attention') .  "</p>\n"
		. spiplistes_form_bouton_valider ('Valider', _T('spiplistes:generer_apercu'))
		. fin_block() // fin_block_invisible
		. fin_cadre_relief(true)
		. "<br />\n"
		;
		
	//
	// bloc du courrier (titre, texte), toujours visible
	$page_result .= ''
		. '<label for="texte_courrier" style="display:bloc">'._T('spiplistes:texte_courrier').'</label>'
		. '<textarea id="texte_courrier" name="message" '.$GLOBALS['browser_caret'].' class="porte_plume_partout barre_inserer formo" rows="20" cols="40" wrap=soft>'.$eol
		. $texte
		. '</textarea>'.$eol
		. (!$id_courrier ? '<input type="hidden" name="new" value="oui" />'.$eol : '')
		//
		. '<p style="text-align:right;">'.$eol
		. '<input type="submit" onclick="this.value=\'oui\';" id="btn_courrier_edit" '
			. ' name="btn_courrier_valider" value="'._T('bouton_valider').'" class="fondo" /></p>'.$eol
		// le marqueur pour les documents joints
		. (($id_temp!==false) ? '<input type="hidden" name="id_temp" value="' . $id_temp . '" />'.$eol : '')
		//
		// fin formulaire
		. '</form>'.$eol
		. '</div>'.$eol // fin_cadre_formulaire
		;
	
	$page_result .= '</div>'.$eol; // fin #courrier-contenu
	
	
	echo($page_result);

	// COURRIER EDIT FIN ---------------------------------------------------------------

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, spiplistes_html_signature(_SPIPLISTES_PREFIX)
		, fin_gauche(), fin_page();
	
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
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.                   */
/******************************************************************************************/
?>