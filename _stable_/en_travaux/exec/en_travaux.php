<?php

include_spip('inc/presentation');
include_spip('inc/barre');

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
	gros_titre(_T('entravaux:en_travaux'));
	debut_gauche();
	
	debut_boite_info();
	echo propre(_T('entravaux:info_message'));	
	fin_boite_info();
	
	debut_droite();

debut_cadre_formulaire();
	

	if ($GLOBALS['connect_statut'] == "0minirezo") {
	echo generer_url_post_ecrire("en_travaux");
//	echo "<p>";
	debut_cadre_trait_couleur("../"._DIR_PLUGIN_EN_TRAVAUX."/spip_mecano_24.png", false, "", _T('entravaux:parametrage_page_travaux'));
	echo "<input type='checkbox' name='est_en_travaux' value='true' $check_en_travaux/>";
	echo "<label for='est_en_travaux'><b>"._T("entravaux:activer_message")."</b></label>";
	fin_cadre_trait_couleur();
	echo "<br/><b>"._T('entravaux:message_temporaire')."</b><br/>";
	echo afficher_textarea_barre($en_travaux_texte);
/*	echo "<textarea name='en_travaux_message' class='formo'>";
	echo $en_travaux_texte;
	echo "</textarea>";*/
//	echo "</p>";
	echo '<div style="text-align: right;">';
	echo "<input class='fondo' type='submit' name='modifier' value='"._T('bouton_valider')."' />";
	echo "</div></div>";
	echo "</form>";
		
	}
	else 
		echo "<strong>"._T("avis_non_acces_page")."</strong>";
	echo "</span>";

fin_cadre_formulaire();
	fin_page();
	
}


?>