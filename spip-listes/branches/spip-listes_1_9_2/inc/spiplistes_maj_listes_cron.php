<?php

/******************************************************************************************/
/* SPIP-listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.                   */
/******************************************************************************************/

function cron_spiplistes_maj_listes_cron($t){
	
	// ---------------------------------------------------------------------------------------------
	// Taches de fond
	//
	// mettre a jour les abonnés d'une liste
	//
	
	// definir la liste a mettre a jour automatiquement
	$numero_liste = '6' ;
	
	spip_log("liste maj cron","cron_maj_listes");
	
	// vider la liste numero 6
	
	$vidage = spip_query("DELETE FROM spip_auteurs_listes where id_liste=$numero_liste");
	
	// requette qui permet de trouver les id_auteur concernés (ici lié au plugin abonnement)
	$query = "SELECT a.id_auteur FROM spip_auteurs_elargis a, spip_zones_auteurs b, spip_auteurs_elargis_abonnements c	WHERE a.id_auteur = b.id_auteur	and a.id = c.id_auteur_elargi	and a.spip_listes_format <> 'non'	and c.validite <> '0000-00-00 00:00:00' 	and c.validite > NOW() " ;	
	
	$result = spip_query($query);
	
	while($row = spip_fetch_array($result)){
	var_dump($row);
	$id_auteur = $row['id_auteur'] ;
	$id_liste = $numero_liste ; 
	spip_query("insert into spip_auteurs_listes (id_auteur,id_liste) VALUES ('$id_auteur','$id_liste')");
	}

	
	return 1; 
}

/******************************************************************************************/
/* SPIP-listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.                   */
/******************************************************************************************/

?>
