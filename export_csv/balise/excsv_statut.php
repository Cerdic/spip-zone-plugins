<?php
/*##############################################################
 * ExportCSV
 * Export des articles / rubriques SPIP en fichiers CSV.
 *
 * Auteur :
 * St�phanie De Nada� 
 * webdesigneuse.net
 * � 2008 - Distribu� sous licence GNU/GPL
 *
##############################################################*/

if (!defined("_ECRIRE_INC_VERSION")) return; #securite

include_spip("base/db_mysql");
include_spip("base/abstract_sql");
/**/
function balise_EXCSV_STATUT($p) {
	return calculer_balise_dynamique($p, 'EXCSV_STATUT', array('id_rubrique'));
}

function balise_EXCSV_STATUT_dyn($id_rubrique) {
	
	$q = "SELECT statut FROM spip_rubriques WHERE id_rubrique='".$id_rubrique."'";
	$r = spip_fetch_array(spip_query($q));

	$txt = 'exportcsv:'.$r['statut'];
	return _T($txt);
}
?>