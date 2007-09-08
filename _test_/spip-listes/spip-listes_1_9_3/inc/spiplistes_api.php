<?php
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

// API v0.2

include_spip ("inc/utils");
include_spip ("inc/filtres");    /* email_valide() */
include_spip ("inc/acces");      /* creer_uniqid() */

/* function privee
 * multi_queries mysql n'est pas en mesure de le faire en natif :-(
 * A tranformer le jour où mysql gerera correctement le multi_query
 * Et a transformer en transanction quand spip utilisera innodb ou autre table transactionnelle
 * @param $queries : requetes separees par des ';'
 */
function __exec_multi_queries($queries) {
	$queries = trim($queries);
	if (substr($queries, -1, 1) == ';') {
		$queries = substr($queries, 0, strlen($queries)-1);
	}
	$_queries = split(';', $queries);
	while( list(,$val) = each($_queries)) {
		spip_query($val);
	}
}

/*
 * Ajouter les abonnes d'une liste a un envoi
 * @param : $id_courrier : reference d'un envoi
 * @param $id_liste : refernce d'une liste
 */
function spiplistes_remplir_liste_envois($id_courrier,$id_liste){
	if($id_liste==0)
		$result_m = spip_query("SELECT id_auteur FROM spip_auteurs ORDER BY id_auteur ASC");
	else
		$result_m = spip_query("SELECT id_auteur FROM spip_auteurs_listes WHERE id_liste=".
							   _q($id_liste));
	
	while($row_ = spip_fetch_array($result_m)) {
		$id_abo = $row_['id_auteur'];
		spip_query("INSERT INTO spip_auteurs_courriers (id_auteur,id_courrier,statut,maj) VALUES (".
				   _q($id_abo).","._q($id_courrier).",'a_envoyer', NOW()) ");
	}
	$res = spip_query("SELECT COUNT(id_auteur) AS n FROM spip_auteurs_courriers WHERE id_courrier=".
					  _q($id_courrier)." AND statut='a_envoyer'");
	if ($row = spip_fetch_array($res))
		spip_query("UPDATE spip_courriers SET total_abonnes=".
				   _q($row['n'])." WHERE id_courrier="._q($id_courrier));
}

// Nombre d'abonnes a une liste
function spip_listes_nb_abonnes_liste($id_liste){
	$row = spip_fetch_array(spip_query("SELECT COUNT(id_auteur) AS n FROM spip_auteurs_listes WHERE id_liste=$id_liste"));
	$nb_abo = ( $row['n'] >1)?  $row['n']." abonn&eacute;s" :  $row['n']." abonn&eacute;";
	return "(".$nb_abo.")";
}


//taille d'une chaine sans saut de lignes ni espaces
function spip_listes_strlen($out){
	$out = preg_replace("/(\r\n|\n|\r| )+/", "", $out);
	return $out ;
}


//desabonner des listes publiques
function spiplistes_desabonner($id_auteur){
	$listes = spip_query ("SELECT * FROM spip_listes WHERE statut = 'liste'");
			while($row = spip_fetch_array($listes)) {
				$id_liste = $row['id_liste'] ;
				$result=spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur="._q($id_auteur)." AND id_liste="._q($id_liste));
			}
	$result=spip_query("UPDATE `spip_auteurs_elargis` SET `spip_listes_format`='non' WHERE `id_auteur` ="._q($id_auteur));
}


//function spiplistes_propre($texte)
// passe propre() sur un texte puis nettoye les trucs rajoutes par spip sur du html
// ca s'utilise pour afficher un courrier dans l espace prive
// on l'applique au courrier avant de confirmer l'envoi
function spiplistes_propre($texte){
	$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
	if (isset($style_reg[0])) 
		$style_str = $style_reg[0]; 
	else 
		$style_str = "";
	$texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);
	//passer propre si y'a pas de html (balises fermantes)
	if( !preg_match(',</?('._BALISES_BLOCS.')[>[:space:]],iS', $texte) ) 
	$texte = propre($texte); // pb: enleve aussi <style>...  
	$texte = propre_bloog($texte); //nettoyer les spip class truc en trop
	$texte = ereg_replace("__STYLE__", $style_str, $texte);
	//les liens avec double début #URL_SITE_SPIP/#URL_ARTICLE
	$texte = ereg_replace($GLOBALS['meta']['adresse_site']."/".$GLOBALS['meta']['adresse_site'], $GLOBALS['meta']['adresse_site'], $texte);
	$texte = liens_absolus($texte);
	
	return $texte;
}


/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
?>