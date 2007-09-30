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
// $LastChangedRevision: 15426 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2007-09-22 18:27:40 +0200 (sam., 22 sept. 2007) $

	// Appelé en tache de fond (CRON SPIP)
	
	// - Verifie toutes les listes auto==oui publiques et privées
	// - créé le courrier pour la méleuse dans spip_courriers
	// - determine les dates prochain envoi si periode > 0
	// - si periode < 0, repasse la liste en dormeuse

function cron_spiplistes_cron ($last_time) {

	include_spip('inc/spiplistes_api');

	spiplistes_log("CRON: cron_spiplistes_cron() <<", LOG_DEBUG);
		
	$current_time = time();

	// peut-etre optimisé en demandant directement les listes à la bonne date
	$listes_privees_et_publiques = spip_query ("SELECT * FROM spip_listes 
		WHERE statut = '"._SPIPLISTES_PUBLIC_LIST."' OR statut = '"._SPIPLISTES_PRIVATE_LIST."'");
	
	while($row = spip_fetch_array($listes_privees_et_publiques)) {
	
		$id_liste = $row['id_liste'] ;
		$titre = $row['titre'] ;
		$titre_message = $row['titre_message'] ;
		$last_maj_bg = strtotime($row["maj"]);
		$message_auto =  $row["message_auto"];
		$periode = intval($row["periode"]);
	
		$id_auteur =spiplistes_mod_listes_get_id_auteur($id_liste);
		
		$temps = $current_time - $last_maj_bg ;
		$top = 3600 * 24 * $periode ;
		
		if(($message_auto == 'oui') && ($temps > $top)) {
		
			spiplistes_log("CRON: la liste $id_liste demande un envoi"); // pour DEBUG
			//squelette du patron
			$patron = $row["patron"] ;
			$lang = $row["lang"];
			spiplistes_log("CRON: lang == $lang"); // pour DEBUG
			//Maj de la date d'envoi
			spip_query("UPDATE spip_listes SET maj=NOW() WHERE id_liste="._q($id_liste)." LIMIT 1"); 
	
		
			// preparation mail
			
			$date = date('Y-m-d H:i:s',$last_maj_bg) ;
			
			include_spip('public/assembler');
			$contexte_patron = array('date' => $date,'patron'=>$patron, 'lang'=>$lang);
			$texte_patron_bg = recuperer_fond('patrons/'.$patron, $contexte_patron);
		 	//$texte_patron_bg = recuperer_page(generer_url_public('patron_switch',"patron=$patron&date=$date",true)) ;		
			
			$titre_patron_bg = ($titre_message =="") ? $titre._T('spiplistes:_de_').$GLOBALS['meta']['nom_site'] : $titre_message;
			$titre = $titre_patron_bg;
			
			spiplistes_log("CRON: Message choppe titre == $titre"); // pour DEBUG
	
			include_spip('inc/spiplistes_api');
			$taille = strlen(spip_listes_strlen(version_texte($texte_patron_bg))) ;
			spiplistes_log("CRON: taille == $taille"); // pour DEBUG
	
			// ne pas envoyer des textes de moins de 10 caracteres
			if ( $taille > 10 ) {

				include_spip('inc/filtres');
				$texte_patron_bg = liens_absolus($texte_patron_bg);

				// creer le courrier
				$result = spip_query("INSERT INTO spip_courriers (titre, texte, date, statut, type, id_auteur, id_liste) 
					VALUES ("._q($titre).","._q($texte_patron_bg).", NOW(),'"._SPIPLISTES_STATUT_ENCOURS."','"._SPIPLISTES_TYPE_LISTEAUTO."'
					, $id_auteur, $id_liste)");
				
				$id_message_bg = spip_insert_id();
				
				//generer la pile d'envoi (spip_auteurs_courriers)
				spiplistes_remplir_liste_envois($id_message_bg, $id_liste);
				spiplistes_log("CRON: remplir courrier $id_message_bg, liste : $id_liste"); // pour DEBUG
			} 
			else {
				// contenu du courrier vide
				spiplistes_log("CRON: envoi mail nouveautes : pas de nouveautes, taille == $taille"); // pour DEBUG
				
				// pas de période ? c'est un envoyer_maintenant. Le repasse en auto non
				if(!$periode) {
					spip_query("UPDATE spip_listes SET message_auto='non' WHERE id_liste=$id_liste LIMIT 1"); 
				}
				
				$result = spip_query("INSERT INTO spip_courriers (titre, texte, date, statut, type, id_auteur, id_liste) 
				 VALUES ("._q(_L("Pas d'envoi"))
				 .","._q(_L("aucune nouveaut&eacute;, le mail automatique n'a pas &eacute;t&eacute; envoy&eacute;"))
				 .", NOW(), '"._SPIPLISTES_STATUT_PUBLIE."', '"._SPIPLISTES_TYPE_LISTEAUTO."' , $id_auteur, $id_liste)");
				$id_message_bg = spip_insert_id();
		
			} 
		} // c'est l'heure
	
	}// fin du test nb listes
	
	/**************/
	
	// Envoi d'un mail automatique ?
	$result_pile = spip_query("SELECT COUNT(id_courrier) AS n FROM spip_courriers WHERE statut='encour'");
	if (($row = spip_fetch_array($result_pile)) && $row['n']){
	
		spiplistes_log("CRON: appel meleuse"); // pour DEBUG
		include_spip('inc/spiplistes_meleuse');
		spiplistes_meleuse();
		
		if(spiplistes_nb_courriers_en_cours() > 0){
			spiplistes_log("CRON: il reste des courriers a envoyer !"); // pour DEBUG
			return (0 - $last_time);
		}
	}
	return ($last_time); 
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
