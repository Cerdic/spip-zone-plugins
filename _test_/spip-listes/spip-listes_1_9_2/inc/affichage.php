<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
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


//maj

//
function spiplistes_verifier_tables_spip_listes() {
global $table_prefix;

$accepter_visiteurs = lire_meta('accepter_visiteurs');

if($accepter_visiteurs != 'oui'){
$accepter_visiteurs = 'oui';
ecrire_meta("accepter_visiteurs", $accepter_visiteurs);
ecrire_metas();
echo _T('spiplistes:autorisation_inscription');
}

$info_spiplistes = plugin_get_infos(_DIR_PLUGIN_SPIPLISTES);
//print_r($info_spiplistes);
$version_plugin = $info_spiplistes['version'];

if (!isset($GLOBALS['meta']['spiplistes_version'])) {
// Verifie que les tables spip_listes existent, sinon les creer

if (!spip_query("SELECT id_liste FROM ".$table_prefix."_listes")) {
spip_log('creation des tables spip_listes');
include_spip('base/create');
creer_base();
}


//Mise a jour des listes anciennes // à mettre en fonction
$requete_listes = 'SELECT *
FROM '.$table_prefix.'_articles
WHERE statut in ("liste","inact","poublist")';
$resultat_aff = spip_query($requete_listes);

if(@spip_num_rows($resultat_aff) > 0){
echo "<h2>SPIP-listes va mettre a jour</h2>";
while ($row = spip_fetch_array($resultat_aff)) {
$id_article=$row['id_article'];
$titre_liste=addslashes(corriger_caracteres($row['titre']));
$texte_liste = addslashes(corriger_caracteres($row['texte']));
$date_liste = $row['date'];
$langue=$row["lang"];
$statut = $row['statut'];
$extra=unserialize($row['extra']);
$patron_liste=$extra["squelette"];
$periode_liste=$extra["periode"];
$maj_liste=$extra["majnouv"];
$email_envoi=$extra["email_envoi"];
$message_auto=$extra["auto"];
$options="<p>".$titre_liste."<br/>";
echo $options."</p>";

//pied de page
include_spip('public/assembler');
$contexte_pied = array('lang'=>$langue);
$pied = recuperer_fond('modeles/piedmail', $contexte_pied);

$requette_maj="INSERT INTO ".$table_prefix."_listes (titre, texte, statut, date, lang, pied_page) VALUES ('$titre_liste','$texte_liste','$statut', '$date_liste','$langue','$pied')";
spip_query($requette_maj);
$id_liste=spip_insert_id();
//echo $requette_maj."<br><br>" ;
if($message_auto=="oui"){
$requette_maj2="UPDATE ".$table_prefix."_listes SET patron='$patron_liste', periode='$periode_liste', maj=FROM_UNIXTIME($maj_liste), email_envoi='$email_envoi', message_auto='$message_auto' WHERE id_liste=$id_liste";
//echo $requette_maj2 ;
spip_query($requette_maj2);
}


//Auteur de la liste (moderateur)
spip_query("DELETE FROM ".$table_prefix."_auteurs_listes WHERE id_liste = $id_liste");
spip_query("INSERT INTO ".$table_prefix."_auteurs_listes (id_auteur, id_liste) VALUES ($connect_id_auteur, $id_liste)");
//recuperer les abonnes (peut etre plus tard ?)
$abos=spip_query("SELECT id_auteur, id_article FROM ".$table_prefix."_auteurs_articles WHERE id_article='$id_article'");

while($abonnes=spip_fetch_array($abos)){
$abo=$abonnes["id_auteur"];
spip_query("INSERT INTO ".$table_prefix."_abonnes_listes (id_auteur, id_liste) VALUES ($abo, $id_liste)");
}

//effacer les anciens articles/abo
spip_query("DELETE FROM ".$table_prefix."_articles WHERE id_article = $id_article");
spip_query("DELETE FROM ".$table_prefix."_auteurs_articles WHERE id_article = $id_article");

//manquent les courriers

}

}

ecrire_meta('spiplistes_version', $version_plugin);
ecrire_metas();
}

//maj pour les installs a re maj	
$spiplistes_version_base = $GLOBALS['meta']['spiplistes_version'] ;

if ( $spiplistes_version_base < 1.92 ) {
echo "<br /> Maj 1.92<br />";
spip_query("ALTER TABLE ".$table_prefix."_listes ADD titre_message varchar(255) NOT NULL default '';");
spip_query("ALTER TABLE ".$table_prefix."_listes ADD pied_page longblob NOT NULL;");
ecrire_meta('spiplistes_version', 1.92);
ecrire_metas();
}


}

////

function spip_listes_onglets($rubrique, $onglet){
global $id_auteur, $connect_id_auteur, $connect_statut, $statut_auteur, $options;

echo debut_onglet();


if ($rubrique == "messagerie"){
echo onglet(_T('spiplistes:Historique_des_envois'), "?exec=spip_listes", "messagerie", $onglet, "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/stock_hyperlink-mail-and-news-24.gif");
echo onglet(_T('spiplistes:Listes_de_diffusion'), "?exec=listes_toutes", "messagerie", $onglet, "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/reply-to-all-24.gif");
echo onglet(_T('spiplistes:Suivi_des_abonnements'), "?exec=abonnes_tous", "messagerie", $onglet,  "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/addressbook-24.gif");
}




echo fin_onglet();
}


function spip_listes_raccourcis(){
global  $connect_statut;

// debut des racourcis
echo debut_raccourcis("../"._DIR_PLUGIN_SPIPLISTES."/img_pack/mailer_config.gif");

if ($connect_statut == "0minirezo") {
 icone_horizontale(_T('spiplistes:Nouveau_courrier'), "?exec=courrier_edit&new=oui&type=nl", "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/stock_mail_send.gif");
echo "</a>"; // bug icone_horizontale()
echo "<br />" ;
echo "<br />" ;

 icone_horizontale(_T('spiplistes:Nouvelle_liste_de_diffusion'), "?exec=liste_edit&new=oui", "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/reply-to-all-24.gif");
echo "</a>"; // bug icone_horizontale()
 icone_horizontale(_T('spiplistes:import_export'), "?exec=import_export", "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/listes_inout.png");
echo "</a>"; // bug icone_horizontale()

 icone_horizontale(_T('spiplistes:Configuration'), "?exec=config","../"._DIR_PLUGIN_SPIPLISTES."/img_pack/mailer_config.gif");
echo "</a>"; // bug icone_horizontale()

}
echo fin_raccourcis();
//



//Afficher la console d'envoi ?

global $table_prefix;
$qery_message = "SELECT * FROM ".$table_prefix."_courriers AS messages WHERE statut='encour' LIMIT 0,1";
$rsult_pile = spip_query($qery_message);
$mssage_pile = spip_num_rows($rsult_pile);
$mess=spip_fetch_array($rsult_pile);	
$id_mess = $mess['id_courrier'];
if($mssage_pile > 0 ){

echo "<br />";
echo debut_boite_info();
echo "<script type='text/javascript' src='".find_in_path('javascript/autocron.js')."'></script>";

echo "<div style='font-weight:bold;text-align:center'>"._T('spiplistes:envoi_en_cours')."</div>";
echo "<div style='padding : 10px;text-align:center'><img src='../"._DIR_PLUGIN_SPIPLISTES."/img_pack/48_import.gif'></div>";
echo "<div id='meleuse'></div>" ;
echo "<p>"._T('spiplistes:texte_boite_en_cours')."</p>" ;
echo "<p align='center'><a href='".generer_url_ecrire('gerer_courrier','change_statut=publie&id_message='.$id_mess)."'>["._T('annuler')."]</a></p>";


echo fin_boite_info();
} 

// colonne gauche boite info
echo "<br />" ;
echo debut_boite_info();
echo _T('spiplistes:_aide');
echo fin_boite_info();


}

/**
* spiplistes_afficher_en_liste
*
* affiche des listes d'éléments
*
* @param string titre
* @param string image
* @param string statut
* @param string recherche
* @param string nom_position
* @return string la liste des lettres pour le statut demandé
* @author BoOz / Pierre Basson
**/
function spiplistes_afficher_en_liste($titre, $image, $element='listes', $statut, $recherche='', $nom_position='position') {

global $pas, $id_auteur;
$position = intval($_GET[$nom_position]);

$clause_where = '';
if (!empty($recherche)) {
$recherche = addslashes($recherche);
$clause_where.= ' AND ( titre LIKE "%'.$recherche.'%"  OR  descriptif LIKE "%'.$recherche.'%"  OR  texte LIKE "%'.$recherche.'%" )';
}

$lettres = '';

if(!$pas) $pas=10 ;
if(!$position) $position=0 ;

if($element == 'listes'){
$requete_listes = 'SELECT id_liste,
titre,
date
FROM spip_listes
WHERE statut="'.$statut.'" '.$clause_where.'
ORDER BY date DESC
LIMIT '.$position.','.$pas.'';

}

if($element == 'messages'){
$type='nl' ;
if($statut=='redac'){
$statut='redac" OR statut="ready';
}
if($statut=='auto'){
$type='auto';
$statut='publie';
}
if($statut=='encour'){
$type2='OR type="auto"';
}

$requete_listes = 'SELECT id_courrier,
titre,
date, nb_emails_envoyes
FROM spip_courriers
WHERE (type="'.$type.'"'.$type2.') AND statut="'.$statut.'" '.$clause_where.'
ORDER BY date DESC
LIMIT '.$position.','.$pas.'';
}

if($element == 'abonnements'){
if($statut==''){

$requete_listes = 'SELECT listes.id_liste, listes.titre, listes.statut, listes.date, 							lien.id_auteur,lien.id_liste FROM  spip_abonnes_listes AS lien LEFT JOIN spip_listes AS listes  ON 				lien.id_liste=listes.id_liste WHERE lien.id_auteur="'.$id_auteur.'" AND (listes.statut ="liste" OR 				listes.statut ="inact") ORDER BY listes.date DESC LIMIT '.$position.','.$pas.'';

}else{
$requete_listes = 'SELECT id_courrier,
titre,
date, nb_emails_envoyes
FROM spip_courriers
WHERE type="'.$type.'" AND statut="'.$statut.'" '.$clause_where.'
ORDER BY date DESC
LIMIT '.$position.','.$pas.'';
}
}

//echo "$requete_listes";
$resultat_aff = spip_query($requete_listes);



if (@spip_num_rows($resultat_aff) > 0) {

$en_liste.= "<div class='liste'>\n";
$en_liste.= "<div style='position: relative;'>\n";
$en_liste.= "<div style='position: absolute; top: -12px; left: 3px;'>\n";
$en_liste.= "<img src='".$image."'  />\n";
$en_liste.= "</div>\n";
$en_liste.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n";
$en_liste.= "<b>\n";
$en_liste.= $titre;
$en_liste.= "</b>\n";
$en_liste.= "</div>\n";
$en_liste.= "</div>\n";
$en_liste.= "<table width='100%' cellpadding='2' cellspacing='0' border='0'>\n";

while ($row = spip_fetch_array($resultat_aff)) {
$titre		= $row['titre'];
$date		= affdate($row['date']);				

switch ($element){
case "abonnements":
$id_row = $row['id_liste'];
$url_row	= generer_url_ecrire('gerer_liste', 'id_liste='.$id_row);
$url_desabo	= generer_url_ecrire('abonne_edit', 'id_liste='.$id_row.'&id_auteur='.$id_auteur.'&suppr_auteur='.$id_auteur);
break;

case "listes":
$id_row = $row['id_liste'];
$url_row	= generer_url_ecrire('gerer_liste', 'id_liste='.$id_row);
break;


default:
$id_row	= $row['id_courrier'];			
$nb_emails_envoyes	= $row['nb_emails_envoyes'];
$url_row	= generer_url_ecrire('gerer_courrier', 'id_message='.$id_row);
}

$en_liste.= "<tr class='tr_liste'>\n";
$en_liste.= "<td width='11'>";
switch ($statut) {
case 'brouillon':
$en_liste.= "<img src='img_pack/puce-blanche.gif' alt='puce-blanche' border='0' style='margin: 1px;' />";
break;
case 'publie':
$en_liste.= "<img src='img_pack/puce-verte.gif' alt='puce-verte' border='0' style='margin: 1px;' />";
break;
case 'envoi_en_cours':
$en_liste.= "<img src='img_pack/puce-orange.gif' alt='puce-orange' border='0' style='margin: 1px;' />";
break;
}
$en_liste.= "</td>";
$en_liste.= "<td class='arial2'>\n";
$en_liste.= "<div>\n";
$en_liste.= "<a href=\"".$url_row."\" dir='ltr' style='display:block;'>\n";
$en_liste.= $titre;

if ($element == 'listes') {

$nb_abo= spip_num_rows(spip_query("SELECT id_auteur FROM spip_abonnes_listes WHERE id_liste='$id_row'"));
$nb_abo = ($nb_abo>1)? $nb_abo." abonn&eacute;s" : $nb_abo." abonn&eacute;";

$en_liste.= " <font size='1' color='#666666' dir='ltr'>\n";
$en_liste.= "(".$nb_abo.")\n";
$en_liste.= "</font>\n";
}



if($nb_emails_envoyes>0){
$en_liste.= " <font size='1' color='#666666' dir='ltr'>\n";
$en_liste.= "(".$nb_emails_envoyes.")\n";
$en_liste.= "</font>\n";
}

$en_liste.= "</a>\n";
$en_liste.= "</div>\n";
$en_liste.= "</td>\n";

switch ($element){
case "abonnements":
$en_liste.= "<td width='120' class='arial1'><a href=\"".$url_desabo."\" dir='ltr' style='display:block;'>D&eacute;sabonnement</a></td>\n";
break;

default:
$en_liste.= "<td width='120' class='arial1'>".$date."</td>\n";
}

$en_liste.= "<td width='50' class='arial1'><b>N&nbsp;".$id_row."</b></td>\n";
$en_liste.= "</tr>\n";

}
$en_liste.= "</table>\n";


switch ($element){

case "listes":
$requete_total = 'SELECT id_liste
FROM spip_listes
WHERE statut="'.$statut.'" '.$clause_where.'
ORDER BY date DESC';
$retour = 'listes_toutes';
break;


case "messages":
$requete_total = 'SELECT id_courrier
FROM spip_courriers
WHERE type="'.$type.'" AND statut="'.$statut.'"';
$retour = 'spip_listes';
break;
case "abonnements":
$requete_total = 'SELECT listes.id_liste, listes.titre, listes.statut, listes.date, lien.id_auteur,lien.id_liste FROM  spip_abonnes_listes AS lien LEFT JOIN spip_listes AS listes  ON 	lien.id_liste=listes.id_liste WHERE lien.id_auteur="'.$id_auteur.'" AND (listes.statut ="liste" OR listes.statut ="inact") ORDER BY listes.date DESC';
$retour = 'abonne_edit';
$param = '&id_auteur='.$id_auteur;
break;
}

$resultat_total = spip_query($requete_total);
$total = spip_num_rows($resultat_total);

$en_liste.= spiplistes_afficher_pagination($retour, $param, $total, $position, $nom_position);
$en_liste.= "</div>\n";
$en_liste.= "<br />\n";
}

echo $en_liste;

}



/**
* adapté de lettres_afficher_pagination
*
* @param string fond
* @param string arguments
* @param int total
* @param int position
* @author Pierre Basson
**/
function spiplistes_afficher_pagination($fond, $arguments, $total, $position, $nom) {
global $pas;
$pagination = '';
$i = 0;

$nombre_pages = floor(($total-1)/$pas)+1;

if($nombre_pages>1) {

$pagination.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px;  padding-right: 40px; text-align: right;' class='verdana2'>\n";
while($i<$nombre_pages) {
$url = generer_url_ecrire($fond, $nom.'='.strval($i*$pas).$arguments, '&');
$item = strval($i+1);
if(($i*$pas) != $position) {
$pagination.= '&nbsp;&nbsp;&nbsp;<a href="'.$url.'">'.$item.'</a>'."\n";
} else {
$pagination.= '&nbsp;&nbsp;&nbsp;<i>'.$item.'</i>'."\n";
}
$i++;
}

$pagination.= "</ul>\n";
$pagination.= "</div>\n";


}

return $pagination;
}






//function spiplistes_propre($texte)
// passe propre() sur un texte puis nettoye les trucs rajoutes par spip sur du html
// ca s'utilise pour afficher un courrier dans l espace prive
// on l'applique au courrier avant de confirmer l'envoi
function spiplistes_propre($texte){
$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
if (isset($style_reg[0])) $style_str = $style_reg[0]; 
else $style_str = "";
$texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);
//passer propre si y'a pas de html (balises fermantes)
if( !preg_match(',</?('._BALISES_BLOCS.')[>[:space:]],iS', $texte) ) 
$texte = propre($texte); // pb: enleve aussi <style>...  
$texte = propre_bloog($texte); //nettoyer les spip class truc en trop
$texte = ereg_replace("__STYLE__", $style_str, $texte);
$texte = liens_absolus($texte);

return $texte;
}

//taille d'une chaine sans saut de lignes ni espaces
function spip_listes_strlen($out){
$out = preg_replace("/(\r\n|\n|\r| )+/", "", $out);
return $out ;
}




// API a enrichir


// ajouter les abonnes d'une liste a un envoi
function remplir_liste_envois($id_courrier,$id_liste){
global $table_prefix ;

if($id_liste==0){
$query_m = "SELECT id_auteur FROM ".$table_prefix."_auteurs ORDER BY id_auteur ASC";
}else{
$query_m = "SELECT id_auteur FROM ".$table_prefix."_abonnes_listes WHERE id_liste='".$id_liste."'";
}
//echo $query_m ."<br>";
$result_m = spip_query($query_m);
$i = 0 ;
while($row_ = spip_fetch_array($result_m)) {
$id_abo = $row_['id_auteur'];
//echo $id_abo.",".$id_message."<br>";
spip_query("INSERT INTO ".$table_prefix."_abonnes_courriers (id_auteur,id_courrier,statut,maj) VALUES ('$id_abo','$id_courrier','a_envoyer', NOW()) ");
$i++ ;
}
spip_query("UPDATE ".$table_prefix."_courriers SET total_abonnes='$i' WHERE id_courrier='$id_courrier'"); 

}

// compatibilite spip 1.9
if(!function_exists(fin_gauche)) { function fin_gauche(){return false;} }

// Nombre d'abonnes a une liste : a faire


/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
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
