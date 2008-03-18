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
include_spip("inc/presentation");

function exec_exportcsv_tous() {
	global 	$connect_statut, $couleur_claire, $couleur_foncee;
	

	// vérifier les droits
	global $connect_statut;
	global $connect_toutes_rubriques;

	if ($connect_statut != '0minirezo') {
		acces_interdit();
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('exportcsv:extract_data'), "documents", _PLUGIN_NAME_EXPORTCSV);

	echo "<br />";
	gros_titre(_T('exportcsv:titre_page').' : '._T('exportcsv:extract_data'));

	debut_gauche();
		debut_raccourcis();
		if($connect_toutes_rubriques) {
			echo icone_horizontale (_T('exportcsv:config'), generer_url_ecrire("cfg&cfg="._PLUGIN_NAME_EXPORTCSV), _DIR_IMG_EXPORTCSV."cfg-22.png");
		}
		if(is_config()) {
			echo icone_horizontale (_T('exportcsv:telecharger_data'), generer_url_ecrire(_PLUGIN_NAME_EXPORTCSV), _DIR_IMG_EXPORTCSV."exportcsv-24.png");
		}
		fin_raccourcis();
		
		debut_boite_info();
			echo '<p><h3 style="color:'.$couleur_foncee.';">'._T('exportcsv:extraction_data').' :</h3></p>'
			._T('exportcsv:explications');
		fin_boite_info();
		
		echo "<br />";
		debut_cadre_relief();
			echo '<div class="verdana1 spip_xx-small">'
			._T('exportcsv:signature')	
			. '</div>';
		fin_cadre_relief();

	debut_droite();

		debut_cadre_trait_couleur(_DIR_IMG_EXPORTCSV."apercu-24.png", false, "", _T('exportcsv:apercu_data'));

		debut_cadre_relief();
		
		echo '<div id="exportcsv_tab">';
			exportcsv_make(false);
		echo '</div>';

	echo fin_gauche(), fin_page();
}

?>