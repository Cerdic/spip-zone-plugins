<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function echanger_formulaire_yaml_exporter_dist($id_formulaire){
	include_spip('base/abstract_sql');
	include_spip('inc/yaml');
	$id_formulaire = intval($id_formulaire);
	$export = '';
	
	if ($id_formulaire > 0){
		// On récupère le formulaire
		$formulaire = sql_fetsel(
			'*',
			'spip_formulaires',
			'id_formulaire = '.$id_formulaire
		);
		
		// On décompresse les trucs sérialisés
		$formulaire['saisies'] = unserialize($formulaire['saisies']);
		$formulaire['traitements'] = unserialize($formulaire['traitements']);
		
		// On envode en yaml
		$export = yaml_encode($formulaire);
	}
	
	Header("Content-Type: text/x-yaml;");
	Header('Content-Disposition: attachment; filename=formulaire-'.$formulaire['identifiant'].'.yaml');
	Header("Content-Length: ".strlen($export));
	echo $export;
	exit();
}

function echanger_formulaire_yaml_importer_dist($fichier){
}

?>
