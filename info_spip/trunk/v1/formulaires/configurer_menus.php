<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_menus_saisies_dist(){
	include_spip('inc/menus');
	include_spip('inc/config');
	
	// Récupérer les entrées existantes
	$entrees = menus_lister_disponibles();
	
	// Trier les entrées par rang
	uasort($entrees, create_function('$a, $b', '$a = $a["rang"]; $b = $b["rang"]; return ($a==$b)?0:($a<$b?-1:1);'));
	
	// Remplir la liste des cases
	$data = array();
	foreach ($entrees as $type_entree=>$entree){
		$data[$type_entree] = '<img src="'.$entree['icone'].'" /> '.$entree['nom'];
	}
	
	$saisies = array(
		array(
			'saisie' => 'checkbox',
			'options' => array(
				'nom' => 'entrees_masquees',
				'explication' => _T('menus:configurer_entrees_masquees_explication'),
				'datas' => $data,
				'li_class' => 'pleine_largeur',
				'defaut' => lire_config('menus/entrees_masquees',array())
			)
		)
	);
	
	return $saisies;
}

?>
