<?php



// on reprend la fonction de crayons...
function action_crayons_config_store_dist() {
	include_spip('cfg_fonctions');
	include_spip('action/crayons_store');
	// on donne une autre fonction de traitement des donnees
	return action_crayons_store_args('crayons_config_store');
}

// pour le traitement, on appelle crayons_store avec 2 fonctions
// la premiere pour recuperer la valeur avant modification
// la seconde pour realiser les modifications 
function crayons_config_store() {
	$options = array(
			'f_get_valeur' => 'crayons_config_store_get_valeur',
			'f_set_modifs' => 'crayons_config_store_set_modifs');
	return  crayons_store($options);
}

// recuperer la valeur de la config demandee
function crayons_config_store_get_valeur($content, $regs) {
	list(,$crayon,$type,$modele,$id) = $regs;
	$config = cfg_crayon2config($modele);
	$val = lire_config($config);
	return array('config' => $val);	
}

// sauver les modifications de configs
function crayons_config_store_set_modifs($modifs, $return) {
	foreach ($modifs as $modif) {
		list($type, $modele, $id, $content, $wid) = $modif;
		$config = cfg_crayon2config($modele);
		ecrire_config($config, $content['config']);
	}
	return $return;
}



?>
