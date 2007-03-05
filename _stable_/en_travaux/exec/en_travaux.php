<?php

include_spip('inc/presentation');
include_spip('inc/barre');

// compatibilite spip 1.9
if (!function_exists(afficher_textarea_barre)) {
	function afficher_textarea_barre($texte, $forum=false) {
		global $spip_display, $spip_ecran;
		$rows = ($spip_ecran == "large") ? 28 : 15;
		return (($spip_display == 4) ? '' :
			afficher_barre('document.formulaire.texte', $forum))
		. "<textarea name='texte' id='texte' "
		. $GLOBALS['browser_caret']
		. " rows='$rows' class='formo' cols='40'>"
		. entites_html($texte)
		. "</textarea>\n";
	}
}
if (!function_exists(fin_gauche)) { function fin_gauche() {return false;} }

function exec_en_travaux(){
	$check_en_travaux=''; //gestion de l'etat de la case a cocher
 	if (isset($_POST['modifier'])){
 		if (isset($_POST['est_en_travaux'])){
	 		if ($_POST['est_en_travaux'] == 'true') ecrire_meta('en_travaux','true');
	 	} else 
 			effacer_meta('en_travaux');

 		ecrire_meta('en_travaux_message', $_POST['texte']);
 		ecrire_metas();
 		lire_metas();
 	}

 	if ($GLOBALS['meta']['en_travaux']=='true')	$check_en_travaux='checked';
	$en_travaux_texte = $GLOBALS['meta']['en_travaux_message'];

 	debut_page(_T('entravaux:en_travaux'));
	echo "<br /><br /><br />";
	gros_titre(http_img_pack("../"._DIR_PLUGIN_EN_TRAVAUX."/spip_mecano_24.png", "", "") . "&nbsp;" . _T('entravaux:en_travaux'));
	debut_gauche();
	
	debut_boite_info();
	echo propre(_T('entravaux:info_message'));	
	fin_boite_info();
	
	debut_droite();
	debut_cadre_formulaire();

	if ($GLOBALS['connect_statut'] == "0minirezo") {

		echo generer_url_post_ecrire('en_travaux', '', 'formulaire');
		echo "<b>" . _T('entravaux:parametrage_page_travaux') . "</b><hr /><br />";
		echo debut_cadre_relief('', true),
			"<input type='checkbox' name='est_en_travaux' value='true' $check_en_travaux/>",
			"<label for='est_en_travaux'>&nbsp;<b>"._T("entravaux:activer_message")."</b></label>",
			fin_cadre_relief(true);
		echo "<br/><b>"._T('entravaux:message_temporaire')."</b><br />",
			afficher_textarea_barre($en_travaux_texte),
			"<p align='right'><input class='fondo' type='submit' name='modifier' value='"._T('bouton_valider')."' /></p>";
		echo "</form>";
	}
	else 
		echo "<strong>"._T("avis_non_acces_page")."</strong>";
	echo "</span>";

	fin_cadre_formulaire();
	echo fin_gauche(), fin_page();	
}


?>