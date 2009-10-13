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
include_spip("base/abstract_sql");

function exec_exportcsv_petitions() {
	global $connect_statut;
	
	if ($connect_statut != '0minirezo') {
		acces_interdit();
	}
	
	$id_article = _request('id_article');
	$out = exportcsv_make_petition($id_article);
	
	if(!$out) {
		acces_probleme(_T('exportcsv:erreur_pet_id_article'));
		
	} else {
		$out = unicode2charset(charset2unicode($out), 'iso-8859-1');

		$nom_fich = _PLUGIN_NAME_EXPORTCSV."_petition_".date("Y-m-d").".csv";

		header("Content-type: text/comma-separated-values");
		header("Content-Disposition: attachment; filename=".$nom_fich);
		if (strspn($_SERVER['HTTP_USER_AGENT'], "MSIE") != "") {
			 header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			 header("Pragma: public");
		} else {
			header("Pragma: no-cache");
		}

		echo trim($out);
	}
}
?>