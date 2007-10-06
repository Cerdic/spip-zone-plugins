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
	
	// Trieuse 
	
	// - Verifie toutes les listes auto==oui publiques et privées
	// - créé le courrier pour la méleuse dans spip_courriers
	// - determine les dates prochain envoi si periode > 0
	// - si periode < 0, repasse la liste en dormeuse

function cron_spiplistes_cron ($last_time) {

	include_spip('inc/spiplistes_api');

	spiplistes_log("CRON: cron_spiplistes_cron() <<", LOG_DEBUG);
		
	$current_time = time();

	$sql_select = "id_liste,titre,titre_message,date,message_auto,periode,lang,patron";

	// demande les listes auto valides
	$sql_query = "SELECT $sql_select FROM spip_listes 
		WHERE message_auto='oui'
			AND (date NOT LIKE "._q(_SPIPLISTES_ZERO_TIME_DATE).") 
			AND (statut='"._SPIPLISTES_PUBLIC_LIST."' OR statut='"._SPIPLISTES_PRIVATE_LIST."')
			AND (date BETWEEN 0 AND NOW())"
		;
		
	$listes_privees_et_publiques = spip_query ($sql_query);
	
	while($row = spip_fetch_array($listes_privees_et_publiques)) {
	
		// initalise les variables
		foreach(explode(",", $sql_select) as $key) {
			$$key = $row[$key];
		}
		$id_liste = intval($id_liste);
		$periode = intval($periode);
	
		// demande id_auteur de la liste pour la signer
		$id_auteur = spiplistes_mod_listes_get_id_auteur($id_liste);
		
		spiplistes_log("CRON: la liste $id_liste demande un envoi", LOG_DEBUG);
		spiplistes_log("CRON: lang == $lang", LOG_DEBUG);

		/////////////////////////////
		// Tampon date d'envoi (dans maj)
		if($periode) {
			$next_time = strtotime($date) + (_SPIPLISTES_TIME_1_DAY * $periode);
			$sql_set = "date='" . __mysql_date_time($next_time) . "',maj=NOW()";
		}
		else {
		// pas de période ? c'est un envoyer_maintenant.
		// Applique le tampon date d'envoi et repasse la liste en auto non
			$sql_set = "date='',maj=NOW(),message_auto='non'";
		}
		spip_query("UPDATE spip_listes SET $sql_set WHERE id_liste=$id_liste LIMIT 1"); 

		/////////////////////////////
		// preparation du courrier à placer dans le panier
		include_spip('public/assembler');
		$contexte_patron = array('date' => $date, 'patron'=>$patron, 'lang'=>$lang);
		$texte = recuperer_fond('patrons/'.$patron, $contexte_patron);
		$titre = ($titre_message =="") ? $titre._T('spiplistes:_de_').$GLOBALS['meta']['nom_site'] : $titre_message;
		
		spiplistes_log("CRON: Titre => $titre", LOG_DEBUG);

		$taille = strlen(spip_listes_strlen(version_texte($texte)));
		spiplistes_log("CRON: Taille texte du courrier => $taille", LOG_DEBUG);

		/////////////////////////////
		// Place le courrier dans le panier si plus de 10 caractères
		if ( $taille > 10 ) {

			include_spip('inc/filtres');
			$texte = liens_absolus($texte);

			// Place le courrier dans le panier
			$result = spip_query("INSERT INTO spip_courriers (titre, texte, date, statut, type, id_auteur, id_liste) 
				VALUES ("._q($titre).","._q($texte).", NOW(),'"._SPIPLISTES_STATUT_ENCOURS."','"._SPIPLISTES_TYPE_LISTEAUTO."'
				, $id_auteur, $id_liste)");
			
			$id_courrier = spip_insert_id();
			
			//generer la pile d'envoi (spip_auteurs_courriers)
			spiplistes_remplir_liste_envois($id_courrier, $id_liste);
			spiplistes_log("CRON: remplir courrier $id_courrier, liste : $id_liste", LOG_DEBUG);
		} 
		else {
			// contenu du courrier vide
			spiplistes_log("CRON: envoi mail nouveautes : pas de nouveautes, taille == $taille", LOG_DEBUG);
			
			// Ajoute à la pile 
			// A revoir. Serait mieux de conserver le bon titre du courrier et
			// de changer le statut (voir SPIP-Listes-V)
			$result = spip_query("INSERT INTO spip_courriers (titre, texte, date, statut, type, id_auteur, id_liste) 
			 VALUES ("._q(_L("Pas d'envoi"))
			 .","._q(_L("aucune nouveaut&eacute;, le mail automatique n'a pas &eacute;t&eacute; envoy&eacute;"))
			 .", NOW(), '"._SPIPLISTES_STATUT_PUBLIE."', '"._SPIPLISTES_TYPE_LISTEAUTO."' , $id_auteur, $id_liste)");
			$id_courrier = spip_insert_id(); // ??
	
		} 

	}// fin traitement des listes
	
	/////////////////////////////
	// Si panier courriers des encours plein, appelle meleuse
	if (spiplistes_nb_courriers_en_cours()){
		spiplistes_log("CRON: appel meleuse", LOG_DEBUG);
		include_spip('inc/spiplistes_meleuse');
		return(spiplistes_meleuse());
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
