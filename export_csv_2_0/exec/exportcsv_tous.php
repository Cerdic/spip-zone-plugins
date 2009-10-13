<?php
/*##############################################################
 * ExportCSV
 * Export des articles / rubriques SPIP en fichiers CSV.
 *
 * Auteur :
 * Stéphanie De Nadaï
 * webdesigneuse.net
 * © 2008 - Distribué sous licence GNU/GPL
 *
##############################################################*/

include_spip("inc/exportcsv");
include_spip("inc/exportcsv_petition");
include_spip("inc/presentation");

function exec_exportcsv_tous() {
	global 	$connect_statut, $connect_toutes_rubriques, $couleur_claire, $couleur_foncee, $prefix_t;
	
	// vérifier les droits
	if ($connect_statut != '0minirezo') {
		acces_interdit();
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('exportcsv:extract_data'), _PLUGIN_NAME_EXPORTCSV);

	echo "<br />";
	echo gros_titre(_T('exportcsv:titre_page').' : '._T('exportcsv:extract_data'), '', false);

	echo debut_gauche('naviguer', true);

	$raccourcis = '';

	if($connect_toutes_rubriques) {
		$raccourcis .= icone_horizontale (_T('exportcsv:config'), generer_url_ecrire("cfg&cfg="._PLUGIN_NAME_EXPORTCSV), _DIR_IMG_EXPORTCSV."cfg-22.png", "", false);
	}
	if(is_config()) {
		$raccourcis .= icone_horizontale (_T('exportcsv:telecharger_data'), generer_url_ecrire(_PLUGIN_NAME_EXPORTCSV), _DIR_IMG_EXPORTCSV."exportcsv-24.png", "", false);
	}
		
	echo bloc_des_raccourcis($raccourcis);

	echo exportcsv_afficher_petition();
		
	debut_cadre_relief(_DIR_IMG_EXPORTCSV.'help-24.png');
		echo '<div class="verdana1 spip_xx-small"><h3>'._T('exportcsv:extraction_data').'&nbsp;:</h3></p>'
		._T('exportcsv:explications')
		.'</div>';
	fin_cadre_relief();
		
	echo "<br />";
	debut_cadre_relief();
		echo '<!--div class="verdana1 spip_xx-small"-->'
		._T('exportcsv:signature')	
		. '<!--/div-->';
	fin_cadre_relief();

	echo debut_droite('', true);

		echo debut_cadre_trait_couleur(_DIR_IMG_EXPORTCSV."apercu-24.png", true, "", _T('exportcsv:apercu_data'));

		debut_cadre_relief();
			echo '<div id="exportcsv_tab">';
				exportcsv_make(false);
			echo '</div>';
		fin_cadre_relief();
		
	echo fin_cadre_trait_couleur(true), fin_gauche(), fin_page();
}

?>