<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_exporter_statistiques_campagnes_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	list($type, $id, $date_debut, $date_fin) = explode('/', $arg);
	
	$statistiques_array = charger_fonction('statistiques_campagnes_to_array', 'inc');
	
	if (
		$statistiques = $statistiques_array($type, $id, $date_debut, $date_fin)
		and $exporter_csv = charger_fonction('exporter_csv', 'inc/', true)
	){
		// La première ligne des titres
		array_unshift(
			$statistiques,
			array(
				'id_campagne',
				ucfirst(trim(trim(_T('date'), ':'))),
				_T('campagne:statistiques_champ_vues'),
				_T('campagne:statistiques_champ_clics'),
				_T('campagne:statistiques_champ_ratio'),
			)
		);
		// Le nom du fichier
		$fichier = "statistiques_${type}_${id}".($date_debut?"_depuis-$date_debut":'').($date_fin?"_jusque-$date_fin":'');
		
		header('Status: 200 OK');
		header("Content-type: text/csv; charset=utf-8");
		echo $exporter_csv($fichier, $statistiques);
		exit();
	}
}

?>
