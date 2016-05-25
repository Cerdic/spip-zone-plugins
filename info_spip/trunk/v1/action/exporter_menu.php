<?php 

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_exporter_menu_dist(){
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_menu = intval($arg);
	$export = '';
	
	if ($id_menu > 0){
		include_spip('base/abstract_sql');
		include_spip('inc/yaml');
		
		// On récupère l'identifiant du menu pour le nom de fichier
		$identifiant = sql_getfetsel(
			'identifiant',
			'spip_menus',
			'id_menu = '.$id_menu
		);
		
		// On calcule le tableau des entrees
		$entrees = exporter_menu_recursif($id_menu);
		
		// On envode en yaml
		$export = yaml_encode($entrees);
	}
	
	Header("Content-Type: text/x-yaml;");
	Header("Content-Disposition: attachment; filename=menu-$identifiant.yaml");
	Header("Content-Length: ".strlen($export));
	echo $export;
	exit();
	
}

function exporter_menu_recursif($id_menu){
	$entrees = sql_allfetsel(
		'id_menus_entree, type_entree, parametres',
		'spip_menus_entrees',
		'id_menu = '.$id_menu,
		'',
		'rang'
	);
	
	// Pour chaque entree on nettoie et on ajoute le sous-menu eventuel
	foreach ($entrees as $cle => $entree){
		// On remet au propre les parametres
		$entrees[$cle]['parametres'] = unserialize($entree['parametres']);
		
		// On regarde s'il existe un sous-menu
		$id_sous_menu = intval(sql_getfetsel(
			'id_menu',
			'spip_menus',
			'id_menus_entree = '.$entree['id_menus_entree']
		));
		
		// Si le menu existe, il faut l'ajouter au tableau
		if ($id_sous_menu > 0){
			$entrees[$cle]['sous_menu'] = exporter_menu_recursif($id_sous_menu);
		}
		
		// On enleve id_menus_entree car on en a pas besoin
		unset($entrees[$cle]['id_menus_entree']);
	}
	
	return $entrees;
}

?>
