<?php

/******************************************************************************************/
/* SPIP-Listes est un syst�me de gestion de listes d'abonn�s et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G�n�rale GNU publi�e par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique G�n�rale GNU                    */
/* en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.                   */
/******************************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")){
include('../ecrire/inc_version.php3') ;
include_ecrire('inc_filtres.php3');
include_ecrire('mes_options.php3');
include_ecrire('inc_db_mysql.php3');
}
$nomsite=lire_meta("nom_site");
$urlsite=lire_meta("adresse_site");


// ---------------------------------------------------------------------------------------------
// Taches de fond

//
// Envoi du mail quoi de neuf
//

$time = time();


$meta_liste = get_extra(1,"auteur");
$locked = $meta_liste["locked"];
if(!$locked){
$meta_liste["locked"] = "non" ;
set_extra(1,$meta_liste,"auteur");
$meta_liste = get_extra(1,"auteur");
}
$locked = $meta_liste["locked"];



// V�rifier toutes les listes et determiner les dates d'envoi


/***********/



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
		$maj = $ext["majnouv"];
		//squelette du patron
		$patron = $ext["squelette"] ;
		
		//Maj de la date d'envoi -> si envoi ok ?
		$ext["majnouv"]= $time;
		set_extra($id_article_bg,$ext,"article");
	
		// preparation mail
		
		$date = date('Y/m/d',$maj) ;
		
		ob_start();
		include('patron.php3');
		// on recup�re le buffer
		$texte_patron_bg = ob_get_contents();
		// on vide et ferme le buffer
		ob_end_clean();  
		
		$titre_patron_bg = $titre_bg." de ".$nomsite;
		$titre_bg = addslashes($titre_patron_bg);
		
		// ne pas envoyer des textes de moins de 10 caract�res
			if ( (strlen($texte_patron_bg) > 10) ) {
				$texte_patron_bg = "__bLg__".$id_article_bg."__bLg__ ".$texte_patron_bg;
				$texte_patron_bg = addslashes($texte_patron_bg);
				//echo "->$texte_patron_bg" ; 
				// si un mail a pu etre g�n�r�, on l'ajoute � la pile d'envoi
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
				VALUES ('Pas d\'envoi', 'aucune nouveaut�, le mail automatique n a pas �t� envoy�' , NOW(), '$statut', '$type', 1 )";
				$result = spip_query($query);
				$id_message_bg = spip_insert_id();
				spip_query("INSERT INTO spip_auteurs_messages (id_auteur,id_message,vu) VALUES ('1','$id_message_bg','oui')");
		
			} // y'a du neuf
	} // c'est l'heure

}// fin du test nb listes

/**************/

// Envoi d'un mail automatique ?

global $table_prefix;
$query_message = "SELECT * FROM ".$table_prefix."_messages AS messages WHERE statut='encour' AND (type='auto' OR type='nl') LIMIT 0,1";

$result_pile = spip_query($query_message);
$message_pile = spip_num_rows($result_pile);
			
if (($message_pile > 0) AND ($locked == 'non') ) {
//echo "<br>yeah";
echo "<iframe src='spip-listes/spip-meleuse.php3' height='1' width='1' frameborder='0' >D�sol�</iframe>";
}




/******************************************************************************************/
/* SPIP-Listes est un syst�me de gestion de listes d'abonn�s et d'envoi d'information     */
/* par email  pour SPIP.                                                                  */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G�n�rale GNU publi�e par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU  */
/* pour plus de d�tails.                                                                  */
/*                                                                                        */
/* Vous devez avoir re�u une copie de la Licence Publique G�n�rale GNU                    */
/* en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.                   */
/******************************************************************************************/

?>
