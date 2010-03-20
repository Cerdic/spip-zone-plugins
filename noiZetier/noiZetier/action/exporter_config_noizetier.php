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
	
	// On calcule le tableau des noisettes
	$data['noisettes'] = sql_allfetsel(
		'type, composition, bloc, noisette, parametres',
		'spip_noisettes',
		'1',
		'',
		'type, composition, bloc, rang'
	);
	
	// On remet au propre les parametres
	foreach ($data['noisettes'] as $cle => $noisette)
		$data['noisettes'][$cle]['parametres'] = unserialize($noisette['parametres']);
	
	// On récupère les compositions du noizetier
	$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
	if (is_array($noizetier_compositions) AND count($noizetier_compositions)>0)
		$data['noizetier_compositions'] = $noizetier_compositions;
	
	// On encode en yaml
	$export = yaml_encode($data);
	
	Header("Content-Type: text/x-yaml;");
	Header("Content-Disposition: attachment; filename=config_noizetier-".date('Y-m-d').".yaml");
	Header("Content-Length: ".strlen($export));
	echo $export;
	exit();
	
}

?>
