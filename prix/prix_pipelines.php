<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Pour formater les prix avec la devise par défaut
function prix_declarer_tables_interfaces($interface) {
	$interface['table_des_traitements']['PRIX'][]= 'prix_formater(%s)';
	$interface['table_des_traitements']['PRIX_HT'][]= 'prix_formater(%s)';
	
	return $interface;
}

function prix_bank_devise_defaut($devise) {
	include_spip('prix_fonctions');
	
	// On va chercher la devise par défaut configurée
	$devise_defaut_code = prix_devise_defaut();
	$devise_defaut_info = prix_devise_info($devise_defaut_code);
	
	return $devise_defaut_info;
}
