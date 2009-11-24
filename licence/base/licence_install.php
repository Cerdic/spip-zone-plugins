<?php

#   +----------------------------------+
#    Nom du Filtre : licence   
#   +----------------------------------+
#    date : 11/04/2007
#    auteur :  fanouch - lesguppies@free.fr
#    version: 0.1
#    licence: GPL
#   +-------------------------------------+
#    Fonctions de ce filtre :
#	permet de lier une licence à un article 
#   +-------------------------------------+
# Pour toute suggestion, remarque, proposition d ajout
# reportez-vous au forum de l article :
# http://www.spip-contrib.net/fr_article2147.html
#   +-------------------------------------+

$GLOBALS['licence_base_version'] = 0.1;

function licence_upgrade($numero_version)
{
#	include_spip('base/licence');
	spip_query("ALTER TABLE spip_articles ADD `id_licence` bigint(21) DEFAULT '0' NOT NULL AFTER `id_article`");
	ecrire_meta('licence_base_version',$GLOBALS['licence_base_version'],'non');
	ecrire_metas();
}


function licence_vider_tables($numero_version)
{
	// suppression du champ id_licence a la table spip_articles
	spip_query("ALTER TABLE `spip_articles` DROP `id_licence`");
	effacer_meta('licence_base_version');
	ecrire_metas();
}
	
function licence_install($action){
	$version_base = $GLOBALS['agenda_base_version'];
	switch ($action){
		case 'test':
			break;
		case 'install':
			licence_upgrade($version_base);
			break;
		case 'uninstall':
			licence_vider_tables($version_base);
			break;
	}
}	

?>