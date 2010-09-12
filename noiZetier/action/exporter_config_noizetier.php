<?php 

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_exporter_config_noizetier_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$export = '';
	$data = array();
	
	include_spip('base/abstract_sql');
	include_spip('inc/yaml');
	
	// On ajoute un nom à la config
	include_spip('inc/filtres');
	$data['nom'] = $GLOBALS['meta']['nom_site'].' - '.affdate(date('Y-m-d'));
	
	// On ajoute les noisettes et les compos du noizetier
	include_spip('inc/noizetier');
	$data = array_merge($data,noizetier_tableau_export());
	
	// On encode en yaml
	$export = yaml_encode($data);
	
	Header("Content-Type: text/x-yaml;");
	Header("Content-Disposition: attachment; filename=config_noizetier-".date('Y-m-d').".yaml");
	Header("Content-Length: ".strlen($export));
	echo $export;
	exit();
	
}

?>
