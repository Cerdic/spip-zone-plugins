<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Action de attribuer date aux rubriques
 * @param unknown_type $id_secteur
 * @return unknown_type
 */
function attribuer_date_rubriques($id_secteur) {
//spip_log("attribuer_date_rubriques $id_secteur",'rubriques');

	// si id_secteur n'est pas un nombre, stopper tout
	if (!$id_secteur = intval($id_secteur)) {
		return;
	}

	// Enregistre l'envoi dans la BD
	if ($id_secteur > 0) {
	modifier_date($id_secteur);
	return true;
	}
}

/**
 *
 * @param array $champs Un tableau avec les champs par defaut
 * @return int id_secteur
 */
function modifier_date($id_secteur) {
	
	//routine OK pour inserer en date_utile d'une rubrique la date de son premier article
	$r = sql_select("id_rubrique", "spip_rubriques", "id_secteur=$id_secteur");

	while ($row = sql_fetch($r)) {
		//date du premier article = min, ou du dernier = max
		
	$ru = sql_select("min(fille.date) AS date_art", "spip_articles AS fille","id_rubrique=".$row['id_rubrique']." AND fille.date >'0000-00-00 00:00:00'");
			while ($rowu = sql_fetch($ru)){
				if($rowu['date_art']>0){
					//spip_log("pour rubrique ".$row['id_rubrique']." date_utile sera ".$rowu['date_art'],'rubriques');
					sql_updateq("spip_rubriques", array("date_utile" => $rowu['date_art']), "id_rubrique=".$row['id_rubrique']);
				}
			}
	}

}

?>
