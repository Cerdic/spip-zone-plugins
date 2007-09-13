<?php
#------------------------------------------------------------------------#
#  Plugin  : compte_clics - Licence : GPL                                #
#  File    : total_clics - balise #TOTAL_CLICS                           #
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


if (!defined("_ECRIRE_INC_VERSION")) return;

//include_ecrire ('inc_connect.php');

function balise_TOTAL_CLICS($p) {
	return calculer_balise_dynamique($p,'TOTAL_CLICS', array('id_syndic','id_syndic_article'));
}

function balise_TOTAL_CLICS_dyn($id_syndic = 0, $id_syndic_article = 0) {
//include_ecrire('base/db_mysql.php');

	if (!empty($id_syndic_article))
	{
		$r = spip_query_db("SELECT clic_compteur FROM spip_syndic_articles WHERE id_syndic_article='$id_syndic_article' LIMIT 1");
	}
	else
	{
		$r = spip_query_db("SELECT clic_compteur FROM spip_syndic WHERE id_syndic='$id_syndic' LIMIT 1");
	}

	$o = spip_fetch_array($r);
	return $o['clic_compteur'] ;

}

?>
