<?php
/******************************************************************************************
 * Dépublication permet de dépublier un article, un auteur à une date donnée.			  *
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

function action_depublication_auteurs_dist() {

	// on sauvegarde en base la date de dépublication dans la table spip_auteurs_depublication
	
	$id_auteur = _request("arg");
	
	$jour = _request('jour');
	
	$mois = _request('mois');
	$annee = _request('annee');
	
	$heures = _request('heure');
	$minutes = _request('minute');
	
	$supp = _request('supp');
	
	
	$depublication = date("Y-m-d H:i:s", mktime($heures, $minutes, '00', $mois , $jour, $annee));
	
	/*echo "jour : ", $jour, "<br/>";
	echo "mois : ", $mois, "<br/>";
	echo "annee : ", $annee, "<br/>";
	echo "heures : ", $heures, "<br/>";
	echo "minutes : ", $minutes, "<br/>";
	echo "id_auteur : ", $id_auteur, "<br/>";
	
	*/
	//sql_delete("spip_auteurs_depublication","id_auteur=$id_auteur");
	
	if ($jour != '00' && $mois != '' && $annee != '' && $supp!='supp') {
		
		$statut = sql_getfetsel("statut","spip_auteurs","id_auteur=$id_auteur");
		
		$id_auteur_depublication = sql_getfetsel("id_auteur_depublication","spip_auteurs_depublication","id_auteur=$id_auteur");
		if ($id_auteur_depublication) {
			$id = sql_updateq("spip_auteurs_depublication", array(
				"depublication"	=> $depublication,
				"id_auteur"	=> $id_auteur, 
				"maj"			=> "NOW()",
				"statut" 	=> $statut),
				"id_auteur_depublication=$id_auteur_depublication");
		} else {
			$id = sql_insertq("spip_auteurs_depublication", array(
				"id_auteur"	=> $id_auteur, 
				"depublication"	=> $depublication,
				"maj"			=> "NOW()",
				"statut" 	=> $statut));
		}
	} else if ($supp == 'supp') {
		$id = sql_updateq("spip_auteurs_depublication", array(
			"depublication"	=> '0000-00-00 00:00:00',
			"maj"			=> "NOW()"),
			"id_auteur=$id_auteur");
		
	}
}

?>