<?php
#------------------------------------------------------------------------#
#  Plugin  : compte_clics - Licence : GPL                                #
#  File    : action/compteclics_action - comptage                        #
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

function action_compteclics() {
	verifier_visiteur(); // securite...
	$id_syndic = intval(_request('id_syndic'));
	$id_syndic_article = intval(_request('id_syndic_article'));

	// [fr] Soit un identifiant de syndication soit d article syndique
	// [en] Either a syndication id or a syndicated article
	if (!empty($id_syndic)) {
		$r = spip_query("SELECT url_site AS url, clic_compteur AS clic_compteur_site,id_syndic FROM spip_syndic WHERE id_syndic='$id_syndic' LIMIT 1");
		$o = spip_fetch_array($r);
	}
	elseif (!empty($id_syndic_article)) {
		$r = spip_query("SELECT url, spip_syndic.clic_compteur AS clic_compteur_site, spip_syndic_articles.clic_compteur AS clic_compteur_site_article,spip_syndic.id_syndic FROM spip_syndic, spip_syndic_articles WHERE spip_syndic.id_syndic=spip_syndic_articles.id_syndic AND id_syndic_article='$id_syndic_article' LIMIT 1");
		$o = spip_fetch_array($r);
		// [fr] on incremente le compteur lie a l article syndique
		// [en] increase the counter related to the syndicated article
		if (spip_num_rows($r)<>0) spip_query("UPDATE LOW_PRIORITY spip_syndic_articles SET clic_compteur = clic_compteur + 1 WHERE id_syndic_article='$id_syndic_article' LIMIT 1");
	}
	// [fr] Sinon on envoie un message d'erreur
	// [en] Else show the error message
	else {
		echo "<strong>"._T('compteclics:err_no_site')."</strong>"; exit;
	}

	if (spip_num_rows($r) == 0) {
		echo "<strong>"._T('compteclics:err_no_site')."</strong>"; exit;
	}

	$remote_addr=$_SERVER["REMOTE_ADDR"]; 

	// [fr] On incrémente le compteur du site dans tous les cas
	// [en] Increase the website counter

	if (empty($o[clic_compteur_site])) { // [fr] premiere fois [en]first time
		@spip_query("UPDATE LOW_PRIORITY spip_syndic SET clic_compteur = 1, clic_compteur_derniere_ip ='$remote_addr', clic_compteur_temps=NOW() WHERE id_syndic='$o[id_syndic]' LIMIT 1");
	}
	else {
		@spip_query("UPDATE LOW_PRIORITY spip_syndic SET clic_compteur = clic_compteur + 1, clic_compteur_derniere_ip ='$remote_addr' WHERE id_syndic='$o[id_syndic]' LIMIT 1");
	}

	// [fr] Puis on redirige vers l'URL du site
	// [en] Last the redirect to the URI
	header("Location:$o[url]");
}

?>
