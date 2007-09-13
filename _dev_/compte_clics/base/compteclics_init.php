<?php
/* Balise #TOTAL_CLICS
   Auteur chryjs (c) 2007
   Basé sur http://www.spip-contrib.net/Compter-les-clics-sur-les-liens
   et http://www.plugandspip.com/spip.php?article37
   Plugin pour spip 1.9.2
   Licence GNU/GPL
*/

function clic_install($action)
{
switch ($action) {
 case 'test' : /* test pour savoir si les actions sont nécessaires */
	include_ecrire ('base/abstract_sql.php');
	$desc = spip_abstract_showtable("spip_syndic", '', true);
	return (isset($desc['field']['clic_compteur']));
	break;
 case 'install' :
	spip_query("ALTER TABLE spip_syndic ADD COLUMN clic_compteur INTEGER DEFAULT 0");
	spip_query("ALTER TABLE spip_syndic ADD COLUMN clic_compteur_derniere_ip VARCHAR(15)");
	spip_query("ALTER TABLE spip_syndic ADD COLUMN clic_compteur_temps TIMESTAMP DEFAULT 0");
	spip_query("ALTER TABLE spip_syndic_articles ADD COLUMN clic_compteur INTEGER DEFAULT 0");
	echo "<br> Fin de l'installation du compteur de clics <br>";
	break;
 case 'uninstall' :
	spip_query("ALTER TABLE spip_syndic DROP COLUMN clic_compteur");
	spip_query("ALTER TABLE spip_syndic DROP COLUMN clic_compteur_derniere_ip");
	spip_query("ALTER TABLE spip_syndic DROP COLUMN clic_compteur_temps");
	spip_query("ALTER TABLE spip_syndic_articles DROP COLUMN clic_compteur");
//	echo "<br> Fin de la désinstallation de compte clics <br>";
	break;
 }
} /* clic_install */

/* rend disponible l'icone de désinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function clic_uninstall(){
}

?>
