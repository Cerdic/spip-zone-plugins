<?php

include_spip('inc/listeimc_functions');

function listeimc_install()
{
	include_spip('inc/listeimc_functions');
	include_spip('base/create');

	// création de la structure de la BDD
	sql_drop_table('spip_listeimc_imc');
	sql_drop_table('spip_listeimc_groupe');
	sql_drop_table('spip_listeimc_imc_groupe');
	creer_base();

	// on nettoi les variables relative au plugin
 	effacer_config('listeimc');

	// premiere génération du fichier cities.html
	// par défaut : 164 heures	
	ecrire_config('listeimc/frequence_fichier','164');

	// on générer une première fois le fichier
  	generer_cities_html(); 
}


function listeimc_upgrade()
{
	return;
}

function listeimc_uninstall()
{

	sql_drop_table('spip_listeimc_imc');
	sql_drop_table('spip_listeimc_groupe');
	sql_drop_table('spip_listeimc_imc_groupe');

}


?>