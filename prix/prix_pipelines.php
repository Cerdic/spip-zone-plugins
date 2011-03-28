<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// Pour formater les prix avec la devise par défaut
function prix_declarer_tables_interfaces($interface){
	$interface['table_des_traitements']['PRIX'][]= 'prix_formater(%s)';
	$interface['table_des_traitements']['PRIX_HT'][]= 'prix_formater(%s)';
	
	return $interface;
}

// Pour déclarer les deux pipelines
function prix_prix_ht($flux){ return $flux; }
function prix_priiix($flux){ return $flux; }

?>
