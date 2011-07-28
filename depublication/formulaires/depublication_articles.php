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

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_depublication_articles_charger_dist($retour='', $lier_article=0, $config_fonc='depublication_articles_config', $row=array(), $hidden=''){
	
	//$valeurs = formulaires_editer_objet_charger('articles_depublication',$id_art_depub,0,0,$retour,$config_fonc,$row,$hidden);
	$valeurs = array();
	
	return $valeurs;
}

function depublication_articles_config($row) {
	return array();
}

function formulaires_depublication_articles_verifier_dist($retour='', $lier_article=0, $config_fonc='depublication_articles_config', $row=array(), $hidden='') {
	
	//$erreurs = formulaires_editer_objet_verifier('articles_depublication',$id_art_depub,array('statut'));
	$erreurs = array();
	return $erreurs;
}

function formulaires_depublication_articles_traiter_dist($retour='', $lier_article=0, $config_fonc='depublication_articles_config', $row=array(), $hidden=''){

	foreach ($_POST as $cle => $valeur) {
		//echo "cle : ", $cle, ' ----> ' , $valeur[0],"<br>";
		if (preg_match('"^([0-9]*)_state$"',$cle,$regs)) {
			// on change l'etat de l'article
			$statut = sql_getfetsel("statut","spip_articles","id_article=".$regs[1]);
			$statut_new = _request($regs[0]);
			/*echo "id ",$regs[0],"<br>";
			echo "id_article ",$regs[1],"<br>";
			echo "$statut_new : ", _request($regs[0]),'<br>';
			echo "statut : ",$statut;
			exit();*/
			//if ($statut != '') {
				$id_art_depub = sql_getfetsel("id_art_depub","spip_articles_depublication","id_article=".$regs[1]);
				//echo "id_art_depub : ", $id_art_depub;
				
				if ($id_art_depub == '') {
					// insert
					sql_insertq("spip_articles_depublication",array(
						"statut"	=> $statut_new,
						"id_article"	=> $regs[1],
						"depublication"	=> "0000-00-00 00:00:00",
						"maj"			=> "NOW()")
						);
				} else {
					// update
					sql_updateq("spip_articles_depublication",array(					
						"statut"	=> $statut_new,
						"depublication"	=> "0000-00-00 00:00:00",
						"maj"			=> "NOW()"),
						"id_art_depub=".$id_art_depub);
				}
				
				// on update le statut dans spip_articles
				sql_updateq("spip_articles",array(
					"statut" 	=> $statut_new,
					"maj"		=> "NOW()"),
					"id_article=".$regs[1]);
			//}
		}
	}
	
	
	include_spip('inc/headers');
	$retour = generer_url_ecrire("depublication_list_articles","");
	$message .= redirige_formulaire($retour);
	
	return $message;
}


?>