<?php
/* 
 2014
 Anne-lise Martenot 
 elastick.net 
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function genie_depublier_dist($time) {
	//va chercher les objets de spip_depublies avec une date_depublie pour aujourd'hui
	include_spip('base/abstract_sql');
	$today = date('Y-m-d H:i:s');
	
	if(
		$depublications = sql_allfetsel('*','spip_depublies','DATE_FORMAT(date_depublie, "%Y-%m-%d %H:%i:%s") <= '.sql_quote($today).' AND DATE_FORMAT(date_depublie, "%Y-%m-%d %H:%i:%s") >0')
		and is_array($depublications)
	){
		foreach ($depublications as $depublication){
			$objet= $depublication['objet'];
			$id_objet= $depublication['id_objet'];
			$statut_depublication= $depublication['statut'];
			$date_depublie= $depublication['date_depublie'];
			spip_log("on veut depublier $objet $id_objet $statut_depublication",'depublication');

			//on cherche la table de l'objet donné
			$_id_objet = id_table_objet($objet); //id_article
			$table = table_objet_sql($objet); //articles

			//si le statut est différent de celui demandé
			if ($a_depublier = sql_getfetsel($_id_objet,$table,"statut != ".sql_quote($statut_depublication)." AND $_id_objet = ".intval($id_objet))){
				//si les conditions sont remplies, on change le statut dans cette table
				sql_updateq($table, array("statut" => $statut_depublication), "$_id_objet= ".intval($id_objet));
				//et on supprime l'entrée
				sql_delete('spip_depublies', 'id_objet='.intval($id_objet).' AND objet='.sql_quote($objet));
			}
		}
	}
	return 1;
}

?>
