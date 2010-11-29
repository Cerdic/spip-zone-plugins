<?php
/******************************************************************************************
 * Dépublication permet de dépublier un article à une date donnée.						  *
 * Copyright (C) 2005-2010 Nouveaux Territoires support<at>nouveauxterritoires.fr		  *
 * http://www.nouveauxterritoires.fr							    					  *
 *                                                                                        *
 * Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes *
 * de la Licence Publique Générale GNU publiée par la Free Software Foundation            *
 * (version 3).                                                                           *
 *                                                                                        *
 * Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       *
 * ni explicite ni implicite, y compris les garanties de commercialisation ou             *
 * d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  *
 * pour plus de détails.                                                                  *
 *                                                                                        *
 * Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    *
 * en même temps que ce programme ; si ce n'est pas le cas,								  * 
 * regardez http://www.gnu.org/licenses/ 												  *
 * ou écrivez à la	 																	  *
 * Free Software Foundation,                                                              *
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   *
 ******************************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


function genie_depublication_dist($time) {
	
	// on dépublie les articles et/ou les auteurs si besoin
	spip_log('depublication des articles','depublication');
	$result = sql_select("depublication,id_article","spip_articles_depublication","depublication!='0000-00-00 00:00:00'");
	while ($row = sql_fetch($result)) {
				
		$date = $row['depublication'];
		$id_article = $row['id_article'];
		
		if (strtotime($date) < strtotime(date('Y-m-d H:i:s')))  {
			// on dépublie l'article automatiquement
			
			spip_log("depublication article num $id_article",'depublication');
			
			$etat = lire_config('depublication/nouvetat');
			if ($etat == '')
				$etat = lire_config('depublication/etatdep');
				
			//$statutArticle = sql_getfetsel("statut","spip_articles","id_article=$id_article");
			
			sql_updateq("spip_articles",array("statut" => $etat)," id_article=$id_article");
			
			$id_art_depub = sql_getfetsel("id_art_depub","spip_articles_depublication","id_article=$id_article");
			
			if ($id_art_depub == '') {
				sql_insertq("spip_articles_depublication",array("statut" => $etat,"maj" => "NOW()","id_article=" => $id_article,"depublication" => "0000-00-00 00:00:00"));
			} else {
				sql_updateq("spip_articles_depublication",array("statut" => $etat,"maj" => "NOW()","depublication" => "0000-00-00 00:00:00"),"id_art_depub=$id_art_depub");
			}

		}
	}
	
	
	// on dépublie les articles et/ou les auteurs si besoin
	spip_log('expiration des auteurs','depublication');
	$result = sql_select("depublication,id_auteur","spip_auteurs_depublication","depublication!='0000-00-00 00:00:00'");
	while ($row = sql_fetch($result)) {
				
		$date = $row['depublication'];
		$id_auteur = $row['id_auteur'];
		
		if (strtotime($date) < strtotime(date('Y-m-d H:i:s')))  {
			// on dépublie l'article automatiquement
			
			spip_log("depublication auteur num $id_auteur",'depublication');
			
			/*$etat = lire_config('depublication/nouvetat');
			if ($etat == '')
				$etat = lire_config('depublication/etatdep');*/
				
			//$statutArticle = sql_getfetsel("statut","spip_articles","id_article=$id_article");
			
			sql_updateq("spip_auteurs",array("statut" => "5poubelle")," id_auteur=$id_auteur");
			
			$id_auteur_depublication = sql_getfetsel("id_auteur_depublication","spip_auteurs_depublication","id_auteur=$id_auteur");
			
			if ($id_auteur_depublication == '') {
				sql_insertq("spip_auteurs_depublication",array("statut" => $etat,"maj" => "NOW()","id_auteur=" => $id_auteur,"depublication" => "0000-00-00 00:00:00"));
			} else {
				sql_updateq("spip_auteurs_depublication",array("statut" => $etat,"maj" => "NOW()","depublication" => "0000-00-00 00:00:00"),"id_auteur_depublication=$id_auteur_depublication");
			}

		}
	}
	
	return 1;
}


?>