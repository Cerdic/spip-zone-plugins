<?php


function exec_en_travaux(){
include_ecrire("inc_presentation");
	$check_en_travaux=''; //gestion de l'etat de la case a cocher
 	if (isset($_POST['modifier'])){
 		if (isset($_POST['est_en_travaux'])){
	 		if ($_POST['est_en_travaux'] == 'true') ecrire_meta('en_travaux','true');
	 	} else 
 			effacer_meta('en_travaux');

 		ecrire_meta('en_travaux_message', $_POST['en_travaux_message']);
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
	
	debut_cadre_trait_couleur("../"._DIR_PLUGIN_EN_TRAVAUX."/spip_mecano_24.png", false, "", _T('entravaux:parametrage_page_travaux'));

	if ($GLOBALS['connect_statut'] == "0minirezo") {
	echo generer_url_post_ecrire("en_travaux");
	echo "<p>";
	echo "<label for='est_en_travaux'>"._T("entravaux:activer_message")."</label> ";
	echo "<input type='checkbox' name='est_en_travaux' value='true' $check_en_travaux/><br/><br/>";
	echo _T('entravaux:message_temporaire')."<br/>";
	echo "<textarea name='en_travaux_message' class='formo'>";
	echo $en_travaux_texte;
	echo "</textarea>";
	echo "</p>";
	echo '<div style="text-align: right;">';
	echo "<input class='fondo' type='submit' name='modifier' value='"._T('bouton_valider')."' />";
	echo "</div></div>";
	echo "</form>";
		
	}
	else 
		echo "<strong>"._T("avis_non_acces_page")."</strong>";
	echo "</span>";
	fin_cadre_trait_couleur();
	fin_page();
	
}


?>