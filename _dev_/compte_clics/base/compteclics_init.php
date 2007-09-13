<?php
#------------------------------------------------------------------------#
#  Plugin  : compte_clics - Licence : GPL                                #
#  File    : compteclics_init : installation/desinstallation du plugin   #
#  Authors : Chryjs, 2007 +                                              #
#  based on: http://www.spip-contrib.net/Compter-les-clics-sur-les-liens #
#  and     : http://www.plugandspip.com/spip.php?article37               #
#  Contact : chryjs¡@!free¡.!fr                                          #
#------------------------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


function compteclics_install($action)
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
	break;
 }
} /* clic_install */

/* rend disponible l'icone de désinstallation */
/* ca doit certainement permettre d'effacer les fichiers et autres */
function compteclics_uninstall(){
}

?>
