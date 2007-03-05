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

function cron_spiplistes_cron($t){

	$nomsite = $GLOBALS['meta']['nom_site'];
	
	// ---------------------------------------------------------------------------------------------
	// Taches de fond
	
	//
	// Envoi du mail quoi de neuf
	//
	
	$time = time();
	spip_log("liste meleuse cron");
		
	// Verifier toutes les listes et determiner les dates d'envoi
	
	$list_bg = spip_query ("SELECT * FROM spip_listes WHERE statut = 'liste' OR statut = 'inact'");
	
	while($row = spip_fetch_array($list_bg)) {
	
		$id_liste_bg = $row['id_liste'] ;
		$titre_bg = $row['titre'] ;
		$titre_message = $row['titre_message'] ;
		$last_maj_bg = strtotime($row["maj"]);
		$auto_bg =  $row["message_auto"];
		$periode_bg = $row["periode"];
	
		
		$temps = $time - $last_maj_bg ;
		$top = 3600 * 24 * $periode_bg ;
		
		if ( ($auto_bg == 'oui') AND ($periode_bg > 0) AND ( $temps > $top) ) {
		spip_log("la liste $id_liste_bg demande un envoi");
			//squelette du patron
			$patron = $row["patron"] ;
			$lang_bg = $row["lang"];
			spip_log('->'.$lang_bg);
			//Maj de la date d'envoi
			spip_query("UPDATE spip_listes SET maj=NOW() WHERE id_liste="._q($id_liste_bg)); 
	
		
			// preparation mail
			
			$date = date('Y-m-d H:i:s',$last_maj_bg) ;
			
			include_spip('public/assembler');
			$contexte_patron = array('date' => $date,'patron'=>$patron, 'lang'=>$lang_bg);
			$texte_patron_bg = recuperer_fond('patrons/'.$patron, $contexte_patron);
		 	//$texte_patron_bg = recuperer_page(generer_url_public('patron_switch',"patron=$patron&date=$date",true)) ;		
			
			$titre_patron_bg = ($titre_message =="") ? $titre_bg." de ".$nomsite : $titre_message;
			$titre_bg = $titre_patron_bg;
			
			spip_log("Message choppe->$titre".$titre_bg);
	
			// ne pas envoyer des textes de moins de 10 caracteres
			include_spip('inc/affichage');
			$taille = strlen(spip_listes_strlen(version_texte($texte_patron_bg))) ;
			spip_log("taille ->$taille");
	
			if ( $taille > 10 ) {
	
					$texte_patron_bg = $texte_patron_bg;
					// si un mail a pu etre genere, on l'ajoute a la pile d'envoi
					$type_bg = 'auto';
					$statut_bg = 'encour';
					
					// creer le courrier
					$result = spip_query("INSERT INTO spip_courriers (titre, texte, date, statut, type, id_auteur, id_liste) 
						VALUES ("._q($titre_bg).","._q($texte_patron_bg).", NOW(),"._q($statut_bg).","._q($type_bg).", '1',"._q($id_liste_bg).")");
					
					$id_message_bg = spip_insert_id();
					
					//generer la pile d'envoi
					 remplir_liste_envois($id_message_bg,$id_liste_bg);
					spip_log("remplir courrier $id_message_bg, liste : $id_liste_bg");
				} else {
					spip_log("envoi mail nouveautes : pas de nouveautes ->$taille");
						
					$type_bg = 'auto';
					$statut_bg = 'publie';
	
					$result = spip_query("INSERT INTO spip_courriers (titre, texte, date, statut, type, id_auteur, id_liste) 
					 VALUES ("._q(_L("Pas d'envoi"))
					 .","._q(_L("aucune nouveaut&eacute;, le mail automatique n'a pas &eacute;t&eacute; envoy&eacute;"))
					 .", NOW(),"._q($statut_bg).","._q($type_bg).", '1' ,"._q($id_liste_bg)." )");
					$id_message_bg = spip_insert_id();
			
				} // y'a du neuf
		} // c'est l'heure
	
	}// fin du test nb listes
	
	/**************/
	
	// Envoi d'un mail automatique ?
	$result_pile = spip_query("SELECT COUNT(id_courrier) AS n FROM spip_courriers WHERE statut='encour'");
	if (($row = spip_fetch_array($result_pile)) && $row['n']){
		spip_log("appel meleuse");
		include_spip('inc/spiplistes_meleuse');
		
		$result_pile = spip_query("SELECT COUNT(id_courrier) AS n FROM spip_courriers WHERE statut='encour'");
		if (($row = spip_fetch_array($result_pile)) && $row['n']){
			spip_log("spiplistes cron : il reste des courriers a envoyer !");
			return (0 - $t);
		}
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