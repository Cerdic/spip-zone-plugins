<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function echoppe_tables_interfaces($tables_interfaces){

	$tables_interfaces['table_des_tables']['echoppe_categories'] = 'categories';
	$tables_interfaces['table_des_tables']['echoppe_produits'] = 'produits';
	$tables_interfaces['table_des_tables']['echoppe_stocks'] = 'stocks';
	$tables_interfaces['table_des_tables']['echoppe_depots'] = 'depots';
	$tables_interfaces['table_des_tables']['echoppe_gammes'] = 'gammes';
	$tables_interfaces['table_des_tables']['echoppe_options'] = 'options';
	$tables_interfaces['table_des_tables']['echoppe_valeurs'] = 'valeurs';
	$tables_interfaces['table_des_tables']['echoppe_prix'] = 'prix';
	$tables_interfaces['table_des_tables']['echoppe_clients'] = 'clients';
	$tables_interfaces['table_des_tables']['echoppe_commentaires_paniers'] = 'commentaires_paniers';
	$tables_interfaces['table_des_tables']['echoppe_prestataires'] = 'prestataires';
	
	
	
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
	
	
}

?>
