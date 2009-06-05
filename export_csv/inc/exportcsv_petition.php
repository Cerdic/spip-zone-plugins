<?php
/*##############################################################
 * ExportCSV
 * Export des articles / rubriques SPIP en fichiers CSV.
 *
 * Auteur :
 * Stéphanie De Nadaï * webdesigneuse.net
 * (c)2008 - Distribué sous licence GNU/GPL
 *
##############################################################*/

include_spip("base/db_mysql");
include_spip("base/abstract_sql");
include_spip("base/exportcsv_librairie");

function exportcsv_afficher_petition($id_article=null) {
	global 	$connect_statut, $couleur_claire, $couleur_foncee, $prefix_t;
	$return = '';
	if(!is_null($id_article)) {

		$is_pet = spip_query("SELECT art.id_article FROM ".$prefix_t."articles AS art, ".$prefix_t."petitions AS pet WHERE art.id_article=pet.id_article AND art.statut='publie' AND art.id_article=".$id_article);

		if(spip_num_rows($is_pet) === 1) {

			$is_sign = spip_query("SELECT COUNT(id_article) FROM ".$prefix_t."signatures WHERE id_article=".$id_article);

			if(spip_num_rows($is_sign) > 0) {

				$return .= debut_cadre_relief("petition-24.gif", true);
				$return .= '<div class="verdana1"><h3 style="color:'.$couleur_foncee.';"> '._T('exportcsv:pet_titre').'</h3></div>';
				$return .= icone_horizontale(_T('exportcsv:pet_lien_extract'), generer_url_ecrire(_PLUGIN_NAME_EXPORTCSV.'_petitions', 'id_article='.$id_article), _DIR_IMG_EXPORTCSV."exportcsv-24.png", '', false);
				$return .= fin_cadre_relief(true);				
			}
		}
	}
	else {

		$sel = "";
		$sql_pet = spip_query("SELECT art.id_article, art.titre FROM ".$prefix_t."articles AS art, ".$prefix_t."petitions AS pet WHERE art.id_article=pet.id_article AND art.statut='publie'");
		
		while($petition = spip_fetch_array($sql_pet)) {
			$sel .= '<li><a href="'
			.generer_url_ecrire(_PLUGIN_NAME_EXPORTCSV.'_petitions', 'id_article='.$petition['id_article'])
			.'">'.$petition['titre'].'</a></li>';		
		}

		$return .= debut_cadre_relief("petition-24.gif", true);
#		$return .= gros_titre(_T('exportcsv:pet_lien_extract'), _DIR_IMG_EXPORTCSV.'exportcsv-24.png', false);
		$return .= '<div class="verdana1"><h3> '._T('exportcsv:pet_lien_extract').'</h3></div>';
		$return .= '<ul>'.$sel.'</ul>';
		
		$return .= fin_cadre_relief(true);
	}
	return $return;
}
?>