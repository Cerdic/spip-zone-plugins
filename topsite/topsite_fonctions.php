<?php
#------------------------------------------------------------------------#
#  Plugin  : TOP site - Licence : GPL                                    #
#  File    : topsite_fonctions - #CLIC_COMPTEUR                          #
#  Authors : JN, 2008 +                                                  #
#  based on: https://contrib.spip.net/Compter-les-clics-sur-les-liens #
#  Contact : JN.jamesnicolas@gmail.com                                   #
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



function topsite_ajout($flux) {
	verifier_visiteur(); // securite...
	$site = $_SERVER['HTTP_REFERER'];
	preg_split(",\t,", $site, 3);
	if($row=mysql_fetch_array(spip_query("SELECT url_site, topsite  FROM spip_syndic WHERE url_site='$site' LIMIT 1")))
	{
		$topsite=$row['topsite'];
sql_updateq("spip_syndic", array("topsite" => $topsite), "ulr_site=$site");
		
	}
	return $flux;

	
}
?>
