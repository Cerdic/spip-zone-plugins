<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/

function cron_spiplistes_cron($t){

include_spip('inc/distant');

$nomsite=lire_meta("nom_site");
$urlsite=lire_meta("adresse_site");


// ---------------------------------------------------------------------------------------------
// Taches de fond

//
// Envoi du mail quoi de neuf
//

$time = time();
	
$locked = lire_meta('lock');

if(!$locked){
$meta_liste = lire_meta('lock');
	$meta_liste = "non" ;
	ecrire_meta('lock', $meta_liste);
	ecrire_metas();
}


// Vérifier toutes les listes et determiner les dates d'envoi

$list_bg = spip_query ("SELECT * FROM spip_articles WHERE statut = 'liste' OR statut = 'inact'");

while($row = spip_fetch_array($list_bg)) {

	$id_article_bg = $row['id_article'] ;
	$titre_bg = $row['titre'] ;
	
	$extra = get_extra($id_article_bg,"article");
	$last_maj_bg = $extra["majnouv"];
	$auto_bg =  $extra["auto"];
	$periode_bg = $extra["periode"];
	
	$temps = $time - $last_maj_bg ;
	$top = 3600 * 24 * $periode_bg ;
	
	if ( ($auto_bg == 'oui') AND ($periode_bg > 0) AND ( $temps > $top) AND ($locked == 'non')) {
	
		$ext = get_extra($id_article_bg,"article");
		
		//date dernier envoi
		$maj_nouv = $ext["majnouv"];
		//squelette du patron
		$patron = $ext["squelette"] ;
		
		//Maj de la date d'envoi -> si envoi ok ?
		$ext["majnouv"]= $time;
		set_extra($id_article_bg,$ext,"article");
	
		// preparation mail
		
		$date = date('Y-m-d H:i:s',$maj_nouv) ;
			
			include_spip('public/assembler');
			$contexte_patron = array('date' => $date,'patron'=>$patron);
			$texte_patron_bg = recuperer_fond('patrons/'.$patron, $contexte_patron);
		 	//$texte_patron_bg = recuperer_page(generer_url_public('patron_switch',"patron=$patron&date=$date",true)) ;		
			
			$titre_bg = $titre_bg." de ".$nomsite ;
			
			spip_log("Message choppe->$titre".$titre_bg);
	
			// ne pas envoyer des textes de moins de 10 caracteres
			$tampon_sp = preg_replace("/(\r\n|\n|\r| )+/", "", $texte_patron_bg);
			spip_log("taille ->".strlen($tampon_sp));
			
			

			if ( (strlen($tampon_sp) > 10) ) {
				$texte_patron_bg = "__bLg__".$id_article_bg."__bLg__ ".$texte_patron_bg;
				$texte_patron_bg = addslashes($texte_patron_bg);
				$titre_bg = addslashes($titre_bg);
				//echo "->$texte_patron_bg" ; 
				// si un mail a pu etre généré, on l'ajoute à la pile d'envoi
				$type_bg = 'auto';
				$statut_bg = 'encour';
				
				// astuce : on passe l'id_article dans le texte.
				$query = "INSERT INTO spip_messages (titre, texte, date_heure, statut, type, id_auteur) 
					VALUES ('$titre_bg', '$texte_patron_bg', NOW(), '$statut_bg', '$type_bg', '1' )";
				$result = spip_query($query);
				$id_message_bg = spip_insert_id();
				spip_query("INSERT INTO spip_auteurs_messages (id_auteur,id_message,vu) VALUES ('1','$id_message_bg','non')");
				
			} else {
				spip_log("envoi mail nouveautes : pas de nouveautes");
					
				$type_bg = 'auto';
				$statut_bg = 'publie';

				$query = "INSERT INTO spip_messages (titre, texte, date_heure, statut, type, id_auteur) 
				VALUES ('Pas d\'envoi', 'aucune nouveaut&eacute;, le mail automatique n a pas &eacute;t&eacute; envoy&eacute;' , NOW(), '$statut_bg', '$type_bg', 1 )";
				$result = spip_query($query);
				$id_message_bg = spip_insert_id();
				spip_query("INSERT INTO spip_auteurs_messages (id_auteur,id_message,vu) VALUES ('1','$id_message_bg','oui')");
		
			} // y'a du neuf
	} // c'est l'heure

}// fin du test nb listes

/**************/

spip_log("lock actif : ".$locked);
// Envoi d'un mail automatique ?
global $table_prefix;
$query_message = "SELECT * FROM spip_messages AS messages WHERE statut='encour' AND (type='auto' OR type='nl') LIMIT 0,1";

$result_pile = spip_query($query_message);
$message_pile = spip_num_rows($result_pile);
			
if (($message_pile > 0) AND ( $locked == 'non' OR _request('var_mode')=='test') ) {
//echo "<br>yeah";
spip_log("appel meleuse");
include_spip('inc/spiplistes_meleuse');
}


return 1; 
}

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'abonnés et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/

?>