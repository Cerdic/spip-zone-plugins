<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function echoppe_tables_interfaces($tables_interfaces){
	
	$tables_interfaces['table_des_tables']['spip_echoppe_categories'] = 'echoppe_categories';
	$tables_interfaces['table_des_tables']['spip_echoppe_produits'] = 'echoppe_produits';
	$tables_interfaces['table_des_tables']['spip_echoppe_stocks'] = 'echoppe_stocks';
	$tables_interfaces['table_des_tables']['spip_echoppe_depots'] = 'echoppe_depots';
	$tables_interfaces['table_des_tables']['spip_echoppe_gammes'] = 'echoppe_gammes';
	$tables_interfaces['table_des_tables']['spip_echoppe_options'] = 'echoppe_options';
	$tables_interfaces['table_des_tables']['spip_echoppe_valeurs'] = 'echoppe_valeurs';
	$tables_interfaces['table_des_tables']['spip_echoppe_prix'] = 'echoppe_prix';
	$tables_interfaces['table_des_tables']['spip_echoppe_clients'] = 'echoppe_clients';
	$tables_interfaces['table_des_tables']['spip_echoppe_commentaires_paniers'] = 'echoppe_commentaires_paniers';
	$tables_interfaces['table_des_tables']['spip_echoppe_prestataires'] = 'echoppe_prestataires';
	
	
	
	$tables_interfaces['tables_jointures']['echoppe_categories'] = 'echoppe_categories_produits';
	$tables_interfaces['tables_jointures']['echoppe_produits'] = 'echoppe_categories_produits';
	
	$tables_interfaces['tables_jointures']['echoppe_produits'] = 'echoppe_gammes_produits';
	$tables_interfaces['tables_jointures']['echoppe_gammes'] = 'echoppe_gammes_produits';
	
	$tables_interfaces['tables_jointures']['echoppe_categories'] = 'echoppe_categories_rubriques';
	$tables_interfaces['tables_jointures']['rubriques'] = 'echoppe_categories_rubriques';
	
	$tables_interfaces['tables_jointures']['echoppe_categories'] = 'echoppe_categories_articles';
	$tables_interfaces['tables_jointures']['articles'] = 'echoppe_categories_articles';
	
	$tables_interfaces['tables_jointures']['articles'] = 'echoppe_produits_articles';
	$tables_interfaces['tables_jointures']['echoppe_produits'] = 'echoppe_produits_articles';
	
	$tables_interfaces['tables_jointures']['echoppe_categories'] = 'table_jointure';
	$tables_interfaces['tables_jointures']['echoppe_categories'] = 'table_jointure';

	$tables_interfaces['tables_jointures']['rubriques'] = 'echoppe_produits_rubriques';
	$tables_interfaces['tables_jointures']['echoppe_produits'] = 'echoppe_produits_rubriques';
	
	$tables_interfaces['tables_jointures']['echoppe_produits'] = 'echoppe_produits_sites';
	$tables_interfaces['tables_jointures']['sites'] = 'echoppe_produits_sites';
	
	//var_dump($tables_interfaces);
}

?>
