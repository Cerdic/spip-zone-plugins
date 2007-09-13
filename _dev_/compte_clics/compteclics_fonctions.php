<?php
#------------------------------------------------------------------------#
#  Plugin  : compte_clics - Licence : GPL                                #
#  File    : compteclics_fonctions - filtre + balise #TOTAL_CLICS        #
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


//
// Génération du lien allant vers le compteur de liens
//
function compteur_clic_site($id_syndic=0) {
	if (!empty($id_syndic)) {
//		return "./spip.php?page=clic&amp;id_syndic=".$id_syndic;
		return generer_url_action("compteclics","id_syndic=".$id_syndic,true);
	}
	else {
		$r = spip_query_db("SELECT url_site FROM spip_syndic WHERE id_syndic='$id_syndic' LIMIT 1");
		$o = spip_fetch_array($r);
		return $o['url_site'];
	}
}

function compteur_clic_site_article($id_syndic_article=0) {
	if (!empty($id_syndic_article)) {
//		return "./spip.php?page=clic&amp;id_syndic_article=".$id_syndic_article;
		return generer_url_action("compteclics","id_syndic_article=".$id_syndic,true);
	}
	else {
		$r = spip_query_db("SELECT url FROM spip_syndic_articles WHERE id_syndic_article='$id_syndic_article' LIMIT 1");
		$o = spip_fetch_array($r);
		return $o['url'];
	}
}

?>
