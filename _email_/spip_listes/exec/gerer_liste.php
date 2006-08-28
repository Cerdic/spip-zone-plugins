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


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/mots');
include_spip('inc/affichage');


function exec_gerer_liste()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type;
global $new;
global $connect_statut;

global $id_article;
global $modifier_message;
global $titre;
global $texte;


global $statut_nouv;
global $flag_auteur,$creer_auteur,$ajout_auteur,$supp_auteur,$cherche_auteur,$nouv_auteur,$valider_ajouter_auteur;
global $ok_nouveau_statut;

global $Valider_auto,$Modifier;
global $auto;
global $changer_extra,$email_envoi,$patron,$periode;
global $envoyer_direct;

global $debut;




 
 
$nomsite=lire_meta("nom_site"); 
$urlsite=lire_meta("adresse_site"); 

 
// Admin SPIP-Listes
debut_page("Spip listes", "redacteurs", "spiplistes");

// spip-listes bien installé ?
if (!function_exists(spip_listes_onglets)){
    echo("<h3>erreur: spip-listes est mal installé !</h3>");    
    fin_page();
	  exit;
}

if ($connect_statut != "0minirezo" ) {
	echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
	fin_page();
	exit;
}

if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
	$statut_auteur=$statut;
	spip_listes_onglets("messagerie", "Spip listes");
}

debut_gauche();

debut_boite_info();

echo '<div align="center">
<font face="Verdana,Arial,Sans,sans-serif" size="1"><b>LISTE NUMÉRO&nbsp;:</b></font>
<br><font face="Verdana,Arial,Sans,sans-serif" size="6"><b>'.$id_article.'</b></font>
</div>';

fin_boite_info();

spip_listes_raccourcis();

creer_colonne_droite();


debut_droite("messagerie");




// MODE LISTE EDIT: afficher une liste -----------------------------------------
////

if ($id_article==0) {
	if ($new=='oui') {
		$id_rubrique = intval($id_rubrique);
		if ($titre=='') $titre = _T('spiplistes:liste_sans_titre');

		$langue_new = '';
		$result_lang_rub = spip_query("SELECT lang FROM spip_rubriques WHERE id_rubrique=$id_rubrique");
		if ($row = spip_fetch_array($result_lang_rub))
			$langue_new = $row["lang"];

		if (!$langue_new) $langue_new = lire_meta('langue_site');
		$langue_choisie_new = 'non';

		$forums_publics = substr(lire_meta('forums_publics'),0,3);
		spip_query("INSERT INTO spip_articles (id_rubrique, statut, date, accepter_forum, lang, langue_choisie) VALUES ($id_rubrique, 'inact', NOW(), '$forums_publics', '$langue_new', '$langue_choisie_new')");
		$id_article = spip_insert_id();
		spip_query("DELETE FROM spip_auteurs_articles WHERE id_article = $id_article");
		spip_query("INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($connect_id_auteur, $id_article)");
	} /*else {
		@header("Location: ./index.php3");
		exit;
	}*/
}


//$clean_link = new Link("?exec=spiplistekks&mode=list_edit&id_article=$id_article");

// Initialiser doublons pour documents (completes par "propre($texte)")
$id_doublons['documents'] = "0";



//////////////////////////////////////////////////////
// Determiner les droits d'edition de l'article
//

$query = "SELECT statut, titre, id_rubrique FROM spip_articles WHERE id_article=$id_article";
$result = spip_query($query);
if ($row = spip_fetch_array($result)) {
	$statut_article = $row['statut'];
	$titre_article = $row['titre'];
	$rubrique_article = $row['id_rubrique'];
}
else {
	$statut_article = '';
}

$query = "SELECT * FROM spip_auteurs_articles WHERE id_article=$id_article AND id_auteur=$connect_id_auteur";
$result_auteur = spip_query($query);

//
// Droits mieux structuré que ca ?
//

$flag_auteur = (spip_num_rows($result_auteur) > 0);
$flag_editable = (acces_rubrique($rubrique_article)
	OR ($flag_auteur AND ($statut_article == 'inact' OR $statut_article == 'liste' OR $statut_article == 'poublist')));


//
// Appliquer les modifications
//




$suivi_edito = lire_meta("suivi_edito");
$reindexer = false;

$ok_nouveau_statut = false;

function terminer_changement_statut() {
	global $ok_nouveau_statut, $statut_nouv, $statut_ancien, $id_article, $reindexer;

	if ($ok_nouveau_statut) {
		//calculer_rubriques();
		if ($statut_nouv == 'publie' AND $statut_ancien != $statut_nouv) {
			include_ecrire("inc_mail.php3");
			envoyer_mail_publication($id_article);
		}
		
	}

	
}



if ($statut_nouv) {
	if ($flag_auteur) {
	     //il faut etre admin et abonné pour modifer une liste
		if ($statut_nouv == 'liste' AND $statut_article == 'inact')
			$ok_nouveau_statut = true;
		else if ($statut_nouv == 'inact' AND $statut_article == 'poublist')
			$ok_nouveau_statut = true;
		else if ($statut_nouv == 'poublist' AND $statut_article == 'inact')
			$ok_nouveau_statut = true;
		else if ($statut_nouv == 'poublist' AND $statut_article == 'liste')
			$ok_nouveau_statut = true;
		else if ($statut_nouv == 'inact' AND $statut_article == 'liste')
			$ok_nouveau_statut = true;
		else if ($statut_nouv == 'liste' AND $statut_article == 'poublist')
			$ok_nouveau_statut = true;
	}
	if ($ok_nouveau_statut) {
		$query = "UPDATE spip_articles SET statut='$statut_nouv' WHERE id_article=$id_article";
		$result = spip_query($query);


	}
}

if ($jour && $flag_editable) {
	if ($annee == "0000") $mois = "00";
	if ($mois == "00") $jour = "00";
	$query = "UPDATE spip_articles SET date='$annee-$mois-$jour' WHERE id_article=$id_article";
	$result = spip_query($query);
	calculer_rubriques();
}

if ($jour_redac && $flag_editable) {
	if ($annee_redac<>'' AND $annee_redac < 1001) $annee_redac += 9000;

	if ($mois_redac == "00") $jour_redac = "00";

	if ($avec_redac=="non"){
		$annee_redac = '0000';
		$mois_redac = '00';
		$jour_redac = '00';
	}

	$query = "UPDATE spip_articles SET date_redac='$annee_redac-$mois_redac-$jour_redac' WHERE id_article=$id_article";
	$result = spip_query($query);
}


// Appliquer la modification de langue
if (lire_meta('multi_articles') == 'oui' AND $flag_editable) {
	$row = spip_fetch_array(spip_query("SELECT lang FROM spip_rubriques WHERE id_rubrique=$rubrique_article"));
	$langue_parent = $row['lang'];

	if ($changer_lang) {
		if ($changer_lang != "herit")
			spip_query("UPDATE spip_articles SET lang='".addslashes($changer_lang)."', langue_choisie='oui' WHERE id_article=$id_article");
		else
			spip_query("UPDATE spip_articles SET lang='".addslashes($langue_parent)."', langue_choisie='non' WHERE id_article=$id_article");
	}
}



//
// Reunit les textes decoupes parce que trop longs
//

$nb_texte = 0;
while ($nb_texte ++ < 100){		// 100 pour eviter une improbable boucle infinie
	$varname = "texte$nb_texte";
	$texte_plus = $$varname;	// double $ pour obtenir $texte1, $texte2...
	if ($texte_plus){
		$texte_plus = ereg_replace("<!--SPIP-->[\n\r]*","\n\n\n",$texte_plus);
		$texte_ajout .= " ".$texte_plus;
	} else {
		break;
	}
}
$texte = $texte_ajout . $texte;

//
// Traiter les fins de lignes
//
if ($post_autobr) {
	$chapo = post_autobr($chapo);
	$texte = post_autobr($texte);
}



if ($titre && !$ajout_forum && $flag_editable) {
	$surtitre = addslashes(corriger_caracteres($surtitre));
	$titre = addslashes(corriger_caracteres($titre));
	$soustitre = addslashes(corriger_caracteres($soustitre));
	$descriptif = addslashes(corriger_caracteres($descriptif));
	$nom_site = addslashes(corriger_caracteres($nom_site));
	$url_site = addslashes(corriger_caracteres($url_site));
	$chapo = addslashes(corriger_caracteres($chapo));
	$texte = addslashes(corriger_caracteres($texte));
	$ps = addslashes(corriger_caracteres($ps));


	// Verifier qu'on envoie bien dans une rubrique autorisee
	if ($flag_auteur OR acces_rubrique($id_rubrique)) {
		$change_rubrique = "id_rubrique=\"$id_rubrique\",";
	} else {
		$change_rubrique = "";
	}

	$query = "UPDATE spip_articles SET surtitre=\"$surtitre\", titre=\"$titre\", soustitre=\"$soustitre\", $change_rubrique descriptif=\"$descriptif\", chapo=\"$chapo\", texte=\"$texte\", ps=\"$ps\", url_site=\"$url_site\", nom_site=\"$nom_site\" $add_extra WHERE id_article=$id_article";
	$result = spip_query($query);
	//calculer_rubriques();
	if ($statut_article == 'publie') $reindexer = true;
	
	

	// Changer la langue heritee
	if ($id_rubrique != $id_rubrique_old) {
		$row = spip_fetch_array(spip_query("SELECT lang, langue_choisie FROM spip_articles WHERE id_article=$id_article"));
		$langue_old = $row['lang'];
		$langue_choisie_old = $row['langue_choisie'];

		if ($langue_choisie_old != "oui") {
			$row = spip_fetch_array(spip_query("SELECT lang FROM spip_rubriques WHERE id_rubrique=$id_rubrique"));
			$langue_new = $row['lang'];
			if ($langue_new != $langue_old) spip_query("UPDATE spip_articles SET lang = '$langue_new' WHERE id_article = $id_article");
		}
	}

	// afficher le nouveau titre dans la barre de fenetre
	$titre_article = stripslashes($titre);

	// marquer l'article (important pour les articles nouvellement crees)
	spip_query("UPDATE spip_articles SET date_modif=NOW(), auteur_modif=$connect_id_auteur WHERE id_article=$id_article");
	$id_article_bloque = $id_article;   // message pour inc_presentation
}



//
// Suivi forums publics
//

if (!function_exists('get_forums_publics')) {

// fonction dupliquee dans inc-forum.php3
function get_forums_publics($id_article=0) {
	$forums_publics = lire_meta("forums_publics");
	if ($id_article) {
		$query = "SELECT accepter_forum FROM spip_articles WHERE id_article=$id_article";
		$res = spip_query($query);
		if ($obj = spip_fetch_object($res))
			$forums_publics = $obj->accepter_forum;
	} else { // dans ce contexte, inutile
		$forums_publics = substr(lire_meta("forums_publics"),0,3);
	}
	return $forums_publics;
}

}

//
// Lire l'article
//

$query = "SELECT * FROM spip_articles WHERE id_article='$id_article'";
$result = spip_query($query);

if ($row = spip_fetch_array($result)) {
	$id_article = $row["id_article"];
	$surtitre = $row["surtitre"];
	$titre = $row["titre"];
	$soustitre = $row["soustitre"];
	$id_rubrique = $row["id_rubrique"];
	$descriptif = $row["descriptif"];
	$nom_site = $row["nom_site"];
	$url_site = $row["url_site"];
	$chapo = $row["chapo"];
	$texte = $row["texte"];
	$ps = $row["ps"];
	$date = $row["date"];
	$statut_article = $row["statut"];
	$maj = $row["maj"];
	$date_redac = $row["date_redac"];
	$visites = $row["visites"];
	$referers = $row["referers"];
	$extra = $row["extra"];
	$id_trad = $row["id_trad"];
}



if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $date_redac, $regs)) {
        $mois_redac = $regs[2];
        $jour_redac = $regs[3];
        $annee_redac = $regs[1];
        if ($annee_redac > 4000) $annee_redac -= 9000;
}

if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $date, $regs)) {
        $mois = $regs[2];
        $jour = $regs[3];
        $annee = $regs[1];
}





changer_typo('','article'.$id_article);


if (!function_exists('my_sel')) {

	function my_sel($num,$tex,$comp){
		if ($num==$comp){
			echo "<option value='$num' SELECTED>$tex\n";
		}else{
			echo "<option value='$num'>$tex\n";
		}
	
	}
	
	function afficher_mois($mois){
		my_sel("00",_T('mois_non_connu'),$mois);
		my_sel("01",_T('date_mois_1'),$mois);
		my_sel("02",_T('date_mois_2'),$mois);
		my_sel("03",_T('date_mois_3'),$mois);
		my_sel("04",_T('date_mois_4'),$mois);
		my_sel("05",_T('date_mois_5'),$mois);
		my_sel("06",_T('date_mois_6'),$mois);
		my_sel("07",_T('date_mois_7'),$mois);
		my_sel("08",_T('date_mois_8'),$mois);
		my_sel("09",_T('date_mois_9'),$mois);
		my_sel("10",_T('date_mois_10'),$mois);
		my_sel("11",_T('date_mois_11'),$mois);
		my_sel("12",_T('date_mois_12'),$mois);
	}
	
	function afficher_annee($annee){
		// Cette ligne permettrait de faire des articles sans date de publication
		// my_sel("0000","n.c.",$annee);
	
		if($annee<1996 AND $annee <> 0){
			echo "<option value='$annee' SELECTED>$annee\n";
		}
		for($i=1996;$i<date(Y)+2;$i++){
			my_sel($i,$i,$annee);
		}
	}
	
	function afficher_jour($jour){
		my_sel("00",_T('jour_non_connu_nc'),$jour);
		for($i=1;$i<32;$i++){
			if ($i<10){$aff="&nbsp;".$i;}else{$aff=$i;}
			my_sel($i,$aff,$jour);
		}
	}

}

// prendre en compte les modifs sur les extras
if($Valider_auto){

	// On réupée les extras
   $extra = get_extra($id_article, 'article');

          // Tient il n'y avait pas d'extra
          if (!is_array($extra)) {
          $extra = array();
          }

        if($auto == "oui"){
         $extra['auto'] = "oui" ;
         set_extra($id_article, $extra, 'article');
         }
         elseif($auto == "non"){
             $extra['auto'] = "non" ;
             set_extra($id_article, $extra, 'article');
             }

			 
		if($email_envoi){
         $extra = get_extra($id_article, 'article');
		 $extra['email_envoi'] = $email_envoi ;
         set_extra($id_article, $extra, 'article');
         }
         


   if(($changer_extra == "oui") AND ($auto == "oui") ){
	// On recupere les extras
        $extra = get_extra($id_article, 'article');

        // Tient il n'y avait pas d'extra
        if (!is_array($extra)) {
	$extra = array();
        }

      
      $extra['squelette'] = $patron ;
      $extra['periode'] = $periode ;
   
        
        if($envoyer_direct){
        $extra['majnouv'] = (time() - ($periode * 3600*24));
        //echo"<iframe src='../meleuse-cron.php3' height='1' width='1' frameborder='0' >Déolé/iframe>";
        }elseif(!$extra['majnouv']){
        $extra['majnouv'] = time();
        }
      set_extra($id_article, $extra, 'article');
      
      }
}
	include_ecrire("inc_extra.php3");
  /*echo "ap" ;
  $ex = get_extra($id_article, 'article');
  extra_affichage(serialize($ex), "articles"); */


debut_cadre_relief();
echo "<center>";

//
// Titre, surtitre, sous-titre
//

if($statut == 'liste') $couleur = 'publie';
if($statut == 'inact') $couleur = 'redac';
if($statut == 'poublist') $couleur = 'poubelle';

$logo_statut = "puce-".puce_statut("publie").".gif";

echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
echo "<tr width='100%'><td width='100%' valign='top'>";
if ($surtitre) {
	echo "<span $dir_lang><font face='arial,helvetica' size=3><b>";
	echo typo($surtitre);
	echo "</b></font></span>\n";
}
	gros_titre($titre, $logo_statut);

if ($soustitre) {
	echo "<span $dir_lang><font face='arial,helvetica' size=3><b>";
	echo typo($soustitre);
	echo "</b></font></span>\n";
}


if ($descriptif OR $url_site OR $nom_site) {
	echo "<p><div align='left' style='padding: 5px; border: 1px dashed #aaaaaa; background-color: #e4e4e4;' $dir_lang>";
	echo "<font size=2 face='Verdana,Arial,Sans,sans-serif'>";
	$texte_case = ($descriptif) ? "{{"._T('info_descriptif')."}} $descriptif\n\n" : '';
	$texte_case .= ($nom_site.$url_site) ? "{{"._T('info_urlref')."}} [".$nom_site."->".$url_site."]" : '';
	echo propre($texte_case);
	echo "</font>";
	echo "</div>";
}



echo "</td>";


if ($flag_editable) {
	echo "<td><img src='img_pack/rien.gif' width=5></td>\n";
	echo "<td align='center'>";
	$flag_modif = false;

	// Recuperer les donnees de l'article
	if (lire_meta('articles_modif') != 'non') {
		$query = "SELECT auteur_modif, UNIX_TIMESTAMP(date_modif) AS modification, UNIX_TIMESTAMP(NOW()) AS maintenant FROM spip_articles WHERE id_article='$id_article'";
		$result = spip_query($query);

		if ($row = spip_fetch_array($result)) {
			$auteur_modif = $row["auteur_modif"];
			$modification = $row["modification"];
			$maintenant = $row["maintenant"];

			$date_diff = floor(($maintenant - $modification)/60);

			if ($date_diff >= 0 AND $date_diff < 60 AND $auteur_modif > 0 AND $auteur_modif != $connect_id_auteur) {
				$flag_modif = true;
				$query_auteur = "SELECT nom FROM spip_auteurs WHERE id_auteur='$auteur_modif'";
				$result_auteur = spip_query($query_auteur);
				if ($row_auteur = spip_fetch_array($result_auteur)) {
					$nom_auteur_modif = typo($row_auteur["nom"]);
				}
			}
		}
	}
	if ($flag_modif) {
		icone(_T('spiplistes:modifier_liste'), "?exec=liste_edit&id_article=$id_article", "article-24.gif", "edit.gif");
		echo "<font face='arial,helvetica,sans-serif' size='2'>"._T('avis_article_modifie', array('nom_auteur_modif' => $nom_auteur_modif, 'date_diff' => $date_diff))."</font>";
		echo aide("artmodif");
	}
	else {
		icone(_T('spiplistes:modifier_liste'), "?exec=liste_edit&id_article=$id_article", "article-24.gif", "edit.gif");
	}

	echo "</td>";
}
echo "</tr></table>\n";



echo "<div class='serif' align='left'>";


//////////////////////////////////////////////////////
// Corps de l'article
//

echo "\n\n<div align='justify'>";

if ($virtuel) {
	debut_boite_info();
	echo _T('info_renvoi_article')." ".propre("<center>[->$virtuel]</center>");
	fin_boite_info();
}
else {
	echo "<div $dir_lang><b>";
	$revision_nbsp = $activer_revision_nbsp;
	echo justifier(propre($chapo));
	echo "</b></div>\n\n";

	echo "<div $dir_lang>";
	echo justifier(propre($texte));
	echo "</div>";

	if ($ps) {
		echo debut_cadre_enfonce();
		echo "<div $dir_lang><font size=2 face='Verdana,Arial,Sans,sans-serif'>";
		echo justifier("<b>"._T('info_ps')."</b> ".propre($ps));
		echo "</font></div>";
		echo fin_cadre_enfonce();
	}
	$revision_nbsp = false;

	if ($les_notes) {
		echo debut_cadre_relief();
		echo "<div $dir_lang><font size=2>";
		echo justifier("<b>"._T('info_notes')."&nbsp;:</b> ".$les_notes);
		echo "</font></div>";
		echo fin_cadre_relief();
	}

	if ($champs_extra AND $extra) {
		include_ecrire("inc_extra.php3");
		//extra_affichage($extra, "articles");
	}
}




//////////////////////////////////////////////////////
// Modifier le statut de l'article
//


?>
<script type='text/javascript'>
<!--
function change_bouton(selObj){

	var selection=selObj.options[selObj.selectedIndex].value;

	if (selection=="liste"){
		document.statut.src="img_pack/puce-verte.gif";
	}
	if (selection=="inact"){
		document.statut.src="img_pack/puce-blanche.gif";
	}

	if (selection=="poublist"){
		document.statut.src="img_pack/puce-poubelle.gif";
	}
}
// -->
</script>
<?php


if ($connect_statut == '0minirezo' ) {
	echo "<form action='?exec=gerer_liste&id_article=$id_article' METHOD='get'>";
	debut_cadre_relief("racine-site-24.gif");
	echo "<CENTER>";
	
	echo "<input type='Hidden' name='exec' value='gerer_liste'>";

        echo "<input type='Hidden' name='id_article' value=\"$id_article\">";

	echo "<b>"._T('spiplistes:Cette_liste_est').": </b> ";

	echo "<SELECT name='statut_nouv' size='1' class='fondl' onChange='change_bouton(this)'>";

	echo "<option" . mySel("inact", $statut_article) ." style='background-color: white'>"._T('spiplistes:statut_interne')."\n";
	echo "<option" . mySel("liste", $statut_article) . " style='background-color: #B4E8C5'>"._T('spiplistes:statut_publique')."\n";
	echo "<option" . mySel("poublist", $statut_article) . " style='background:url(img_pack/rayures-sup.gif)'>"._T('texte_statut_poubelle')."\n";

	echo "</SELECT>";

	echo " \n";

	if ($statut_article=='liste') {
		echo "<img src='img_pack/puce-verte.gif' alt='' width='13' height='14' border='0' name='statut'>";
	}
	else if ($statut_article=='inact') {
		echo "<img src='img_pack/puce-blanche.gif' alt='' width='13' height='14' border='0' name='statut'>";
	}

	else if ($statut_article == 'poublist') {
		echo "<img src='img_pack/puce-poubelle.gif' alt='' width='13' height='14' border='0' name='statut'>";
	}
	echo " \n";

	echo "<input type='submit' name='Modifier' value='"._T('bouton_modifier')."' class='fondo'>";
	echo aide ("artstatut");
	echo "</CENTER>";
	fin_cadre_relief();
	echo "</form>";
}




echo "<p>" ;





debut_cadre_relief("../"._DIR_PLUGIN_SPIPLISTES."/img_pack/reply-to-all-24.gif");
     
echo "<form action='?exec=gerer_liste&id_article=$id_article' METHOD='post'>";
		 
	// On réupée les extras
$extra = get_extra($id_article, 'article');

// Tient il n'y avait pas d'extra 
if (!is_array($extra)) {
	$extra = array();
}

$email_envoi = entites_html(lire_meta("email_envoi"));
$email_envoi = ($extra['email_envoi'] !='') ? $extra['email_envoi'] : $email_envoi ;
		
		echo "<b><font face='Verdana,Arial,Sans,sans-serif' size=3 COLOR='#000000'>";
		echo _T('spiplistes:retour')."</font></b><br />";

		echo "<p>"._T('spiplistes:adresse')."</p>";
		echo "<input type='text' name='email_envoi' value=\"".$email_envoi."\" size='20' class='fondl'>&nbsp;";
		
	
	if($id_article)
	echo "<input type='hidden' name='id_article' value='$id_article'>";
	if($new)
	echo "<input type='hidden' name='new' value='$new'>";
	echo "<input type='submit' name='Valider_auto' value='"._T('bouton_valider')."' class='fondo'>";
	
	
	echo "</form>";

fin_cadre_relief();

debut_cadre_relief("../"._DIR_PLUGIN_SPIPLISTES."/img_pack/stock_timer.gif");
     
echo "<form action='?exec=gerer_liste&id_article=$id_article' METHOD='post'>";
		 
	// On réupere les extras
$extra = get_extra($id_article, 'article');

// Tient il n'y avait pas d'extra 
if (!is_array($extra)) {
	$extra = array();
}

echo "</h3>"._T('spiplistes:program')."<h3>";
   

echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";
	
echo "<tr><td background='img_pack/rien.gif' align='$spip_lang_left' class='verdana2'>";
if ($extra['auto'] != "oui") {
echo _T('spiplistes:non_program');
}
else {
     if(($changer_extra == "oui") AND ($auto == "oui") )
     echo "<h2>"._T('spiplistes:date_act')."</h2>" ;
     echo _T('spiplistes:env_esquel')." <em>".$extra['squelette']."</em> " ;
	
				
    echo "<br />"._T('spiplistes:Tous_les')."  <b>".$extra['periode']."</b>  "._T('info_jours') ;
	
    $dernier_envoi =  $extra['majnouv'];
                 $periode= $extra['periode'];

            $sablier = (time() - $dernier_envoi) ;
            
           
           $proch = round(  (( (24*3600*$periode) - $sablier) / (3600*24)) ) ;
            $last = round(  ($sablier / (3600*24)) ) ;
            echo "<br />Dernier envoi il y a <b>$last</b> "._T('spiplistes:jours')."<br />";
           if($proch != 0) {
            echo "<br />"._T('spiplistes:prochain_envoi_prevu')."<b>$proch</b> "._T('spiplistes:jours')."<br />";
            }
            else {
                 	echo "<br />"._T('spiplistes:prochain_envoi_aujd')."<br />";
            }

}
		


  

echo "</td></tr>";


echo "<tr><td background='img_pack/rien.gif' align='$spip_lang_left' class='verdana2'>";
	
        
        if ($extra['auto'] != "oui") {
		echo "<input type='radio' name='auto' value='oui' id='auto_oui'>";
		echo " <label for='auto_oui'>"._T('spiplistes:prog_env')."</label> ";
		echo "<br /><input type='radio' name='auto' value='non' CHECKED id='auto_non'>";
		echo " <b><label for='auto_non'>"._T('spiplistes:prog_env_non')."</label></b> ";
		
	}
	else {
		echo "<input type='radio' name='auto' value='oui' id='auto_oui' CHECKED>";
		echo " <b><label for='auto_oui'>"._T('spiplistes:prog_env')."</label></b> ";
		echo "<input type='hidden' name='changer_extra' value='oui'>";
		echo "<p>";
		
		echo "<ul>";
                echo "<li>"._T('spiplistes:squel');
		
	  $dir = find_in_path("patrons/");
		

		// Ouvre un dossier bien connu, et liste tous les fichiers
		if (is_dir($dir)) {
    		if ($dh = opendir($dir)) {
        		$total_option=0;
		while (($file = readdir($dh)) !== false) {
                if($file != '..' && $file !='.' && $file !='') $total_option=$total_option+1;
        	}
        	closedir($dh);
		}
				if ($dh = opendir($dir)) {
        		//echo "<SELECT name='patron' size='".$total_option."'>";
			echo "<select name='patron'>";
			while (($file = readdir($dh)) !== false) {
               		 if($file != '..' && $file !='.' && $file !=''){
			$titre_option=ereg_replace('(\.html|\.HTML)','',$file);
			 ($extra['squelette'] == $titre_option) ? $selected = "selected='selected" : $selected ="" ;

                        echo "<option ".$selected." value='".$titre_option."'>".$titre_option."</option>\n";
			}
					}
				echo "</select>";
        		closedir($dh);
   		  		}
		}
		
		echo "</li>";
		
		

		echo "<li>"._T('spiplistes:Tous_les')." <input type='text' name='periode' value='".$extra['periode']."' size='4' class='fondl'> "._T('info_jours')."</li>" ;
	
        	if(!$envoyer_direct){
                echo " <li><input type='checkbox' class='checkbox' name='envoyer_direct' id='box' class='fondl' /><label for='box'>"._T('spiplistes:env_maint')."</label></li>";

                }

		echo "</ul><br />";
	
		
		echo "<br /><input type='radio' name='auto' value='non' id='auto_non'>";
		echo " <label for='auto_non'>"._T('spiplistes:prog_env_non')."</label> ";

	}
	echo "</td></tr>\n";
	
	echo "<tr><td style='text-align:$spip_lang_right;'>";
	if($id_article)
	echo "<input type='hidden' name='id_article' value='$id_article'>";
	if($new)
	echo "<input type='hidden' name='new' value='$new'>";
	echo "<input type='submit' name='Valider_auto' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</td></tr>";
	echo "</table>\n";
	
	echo "</FORM>";
		


fin_cadre_relief();


 /*   $ex = get_extra($id_article, 'article');
 extra_affichage(serialize($ex), "articles");*/



//
// Liste des auteurs de l'article
//

////////////////////////////////////////////////////
// Gestion des auteurs
//

// Creer un nouvel auteur et l'ajouter

if ($creer_auteur AND $connect_statut=='0minirezo'){
	$creer_auteur = addslashes($creer_auteur);
	$query_creer = "INSERT INTO spip_auteurs (nom, statut) VALUES (\"$creer_auteur\", '1comite')";
	$result_creer = spip_query($query_creer);

	$nouv_auteur = spip_insert_id();
	$ajout_auteur = true;
}

//
// Appliquer les modifications sur les auteurs
//


if ($supp_auteur && $flag_editable) {
	$query="DELETE FROM spip_auteurs_articles WHERE id_auteur='$supp_auteur' AND id_article='$id_article'";
	$result=spip_query($query);
	if (lire_meta('activer_moteur') == 'oui') {
		include_ecrire ("inc_index.php3");
		indexer_article($id_article);
	}
}




echo "<a name='auteurs'></a>";
debut_cadre_enfonce("auteur-24.gif", false, "",  _T('spiplistes:abon').aide ("artauteurs"));

//
// Recherche d'auteur
//

if ($cherche_auteur) {
	echo "<p align='left'>";
	$query = "SELECT id_auteur, nom FROM spip_auteurs";
	$result = spip_query($query);
	unset($table_auteurs);
	unset($table_ids);
	while ($row = spip_fetch_array($result)) {
		$table_auteurs[] = $row["nom"];
		$table_ids[] = $row["id_auteur"];
	}
	$resultat = mots_ressemblants($cherche_auteur, $table_auteurs, $table_ids);
	debut_boite_info();
	if (!$resultat) {
		echo "<b>"._T('texte_aucun_resultat_auteur', array('cherche_auteur' => $cherche_auteur)).".</b><br />";
	}
	else if (count($resultat) == 1) {
		$ajout_auteur = 'oui';
		list(, $nouv_auteur) = each($resultat);
		echo "<b>"._T('spiplistes:nouvelle_abonne')."</b><br />";
		$query = "SELECT * FROM spip_auteurs WHERE id_auteur=$nouv_auteur";
		$result = spip_query($query);
		echo "<ul>";
		while ($row = spip_fetch_array($result)) {
			$id_auteur = $row['id_auteur'];
			$nom_auteur = $row['nom'];
			$email_auteur = $row['email'];
			$bio_auteur = $row['bio'];

			echo "<li><font face='Verdana,Arial,Sans,sans-serif' size=2><b><font size=3>".typo($nom_auteur)."</font></b>";
			echo "</font>\n";
		}
		echo "</ul>";
	}
	else if (count($resultat) < 16) {
		reset($resultat);
		unset($les_auteurs);
		while (list(, $id_auteur) = each($resultat)) $les_auteurs[] = $id_auteur;
		if ($les_auteurs) {
			$les_auteurs = join(',', $les_auteurs);
			echo "<b>"._T('texte_plusieurs_articles', array('cherche_auteur' => $cherche_auteur))."</b><br />";
			$query = "SELECT * FROM spip_auteurs WHERE id_auteur IN ($les_auteurs) ORDER BY nom";
			$result = spip_query($query);
			echo "<ul>";
			while ($row = spip_fetch_array($result)) {
				$new_auteur = $row['id_auteur'];
				$nom_auteur = $row['nom'];
				$email_auteur = $row['email'];
				$bio_auteur = $row['bio'];
				$ajouter_auteur=true;
				echo "<li><font face='Verdana,Arial,Sans,sans-serif' size=2><b><font size=3>".typo($nom_auteur)."</font></b>";

				if ($email_auteur) echo " ($email_auteur)";
				echo " | <a href=\"".generer_url_ecrire("gerer_liste","id_article=$id_article&ajout_auteur=oui&nouv_auteur=$new_auteur#auteurs")."\">"._T('lien_ajouter_auteur')."</a>";

				if (trim($bio_auteur)) {
					echo "<br /><font size=1>".couper(propre($bio_auteur), 100)."</font>\n";
				}
				echo "</font><p>\n";
			}
			echo "</ul>";
		}
	}
	else {
		echo "<b>"._T('texte_trop_resultats_auteurs', array('cherche_auteur' => $cherche_auteur))."</b><br />";
	}

	

	fin_boite_info();
	echo "<p>";

}

if ($ajout_auteur && $flag_editable) {
	if ($nouv_auteur > 0) {
		$query="DELETE FROM spip_auteurs_articles WHERE id_auteur='$nouv_auteur' AND id_article='$id_article'";
		$result=spip_query($query);
		$query="INSERT INTO spip_auteurs_articles (id_auteur,id_article) VALUES ('$nouv_auteur','$id_article')";
		$result=spip_query($query);
		//attribuer un format de réception si besoin (ancien auteur)
		$extra_format=get_extra($nouv_auteur,"auteur");
		if(!$extra_format["abo"]){
		$extra_format["abo"] = "html";
		set_extra($nouv_auteur,$extra,'auteur');
		}
	
	}

	if (lire_meta('activer_moteur') == 'oui') {
		include_ecrire ("inc_index.php3");
		indexer_article($id_article);
	}
}

//
// Afficher les auteurs
//

//
// Liste des auteurs de l'article
//

$query_ = "SELECT * FROM spip_auteurs AS auteurs, spip_auteurs_articles AS lien ".
	"WHERE auteurs.id_auteur=lien.id_auteur AND lien.id_article=$id_article ".
	"GROUP BY auteurs.id_auteur ORDER BY auteurs.nom";
$result_ = spip_query($query_);
$nombre_auteurs = spip_num_rows($result_);

if ($nombre_auteurs) {
	
	echo "<div class='liste' style='clear:both'>";
	
	echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
	
	// Lire les auteurs qui nous interessent
// et memoriser la liste des lettres initiales
//

$retour=generer_url_ecrire("gerer_liste", "id_article=$id_article");

$max_par_page = 30;
if ($debut > $nombre_auteurs - $max_par_page)
	$debut = max(0,$nombre_auteurs - $max_par_page);
$debut = intval($debut);

$i = 0;
$auteurs=array();
while ($auteur = spip_fetch_array($result_)) {
	if ($i>=$debut AND $i<$debut+$max_par_page) {
		if ($auteur['statut'] == '0minirezo')
			$auteur['restreint'] = spip_num_rows(
				spip_query("SELECT * FROM spip_auteurs_rubriques
				WHERE id_auteur=".$auteur['id_auteur']));
			$auteurs[] = $auteur;
	}
	$i++;

	
		$lettres_nombre_auteurs ++;
		$premiere_lettre = strtoupper(spip_substr(extraire_multi($auteur['nom']),0,1));
		if ($premiere_lettre != $lettre_prec) {
#			echo " - $auteur[nom] -";
			$lettre[$premiere_lettre] = $lettres_nombre_auteurs-1;
		}
		$lettre_prec = $premiere_lettre;
	
}



// reglage du debut
$max_par_page = 30;
if ($debut > $nombre_auteurs - $max_par_page)
	$debut = max(0,$nombre_auteurs - $max_par_page);
$fin = min($nombre_auteurs, $debut + $max_par_page);


// ignorer les $debut premiers
unset ($i);
reset ($auteurs);
while ($i++ < $debut AND each($auteurs));

if ($nombre_auteurs > $max_par_page) {
	echo "<tr bgcolor='white'><td colspan='7'>";
	echo "<div style='background-color:white'>";
	for ($j=0; $j < $nombre_auteurs; $j+=$max_par_page) {
		if ($j > 0) echo " | ";

		if ($j == $debut)
			echo "<b>$j</b>";
		else if ($j > 0)
			echo "<a href=$retour&debut=$j>$j</a>";
		else
			echo " <a href=$retour>0</a>";

		if ($debut > $j  AND $debut < $j+$max_par_page){
			echo " | <b>$debut</b>";
		}

	}
	echo "</font>";
		// affichage des lettres
		
		echo "<div style='font-familly:Verdana,Arial,Sans,sans-serif;font-size=2'>";
		foreach ($lettre as $key => $val) {
			if ($val == $debut)
				echo "<b>$key</b> ";
			else
				echo "<a href=$retour&debut=$val>$key</a> ";
		}
		echo "</div>";
		echo "</div>\n";
		
		echo "</td></tr>\n";
	
	echo "<tr height='5'></tr>";
}

//print_r($auteurs);



if($debut)$retour .="&debut=".$debut;
$les_auteurs=array();	
	$table = '';
	foreach ($auteurs as $row) {
		$vals = '';
		$idi_auteur = $row["id_auteur"];
		$nom_auteur = $row["nom"];
		$email_auteur = $row["email"];
		if ($bio_auteur = attribut_html(propre(couper($row["bio"], 100))))
			$bio_auteur = " TITLE=\"$bio_auteur\"";
		$url_site_auteur = $row["url_site"];
		$statut_auteur = $row["statut"];
		if ($row['messagerie'] == 'non' OR $row['login'] == '') $messagerie = 'non';

		$les_auteurs[] = $idi_auteur;

		 $aff_articles = "('liste','inact')";
		
		
		//print_r($les_auteurs);
		
		$query2 = "SELECT COUNT(articles.id_article) AS compteur ".
			"FROM spip_auteurs_articles AS lien, spip_articles AS articles ".
			"WHERE lien.id_auteur=$idi_auteur AND articles.id_article=lien.id_article ".
			"AND articles.statut IN $aff_articles GROUP BY lien.id_auteur";
		$result2 = spip_query($query2);
		if ($result2) list($nombre_articles) = spip_fetch_array($result2);
		else $nombre_articles = 0;

		$url_auteur = generer_url_ecrire("abonne_edit","id_auteur=$idi_auteur");

		$vals[] = bonhomme_statut($row);

		$vals[] = "<a href=\"$url_auteur\"$bio_auteur>".typo($nom_auteur)."</a>";

		$vals[] = bouton_imessage($idi_auteur);

		
		
		if ($email_auteur) $vals[] =  "<a href='mailto:$email_auteur'>"._T('email')."</a>";
		else $vals[] =  "&nbsp;";

		if ($url_site_auteur) $vals[] =  "<a href='$url_site_auteur'>"._T('info_site_min')."</a>";
		else $vals[] =  "&nbsp;";

		if ($nombre_articles > 1) $vals[] =  $nombre_articles.' listes' ;
		else if ($nombre_articles == 1) $vals[] = '1 liste';
		else $vals[] =  "&nbsp;";

		
			$vals[] =  "<a href='".generer_url_ecrire("gerer_liste","id_article=$id_article&supp_auteur=$idi_auteur#auteurs")."'>"._T('spiplistes:desabonnement')."<img src='img_pack/croix-rouge.gif' alt='X' width='7' height='7' border='0' align='middle'></a>";
		
		
		$table[] = $vals;
	}
	
	
	$largeurs = array('14', '', '', '', '', '', '');
	$styles = array('arial11', 'arial2', 'arial11', 'arial11', 'arial11', 'arial11', 'arial1');
	echo afficher_liste($largeurs, $table, $styles);

	
	echo "</table></div>\n";

	$les_auteurs = join(',', $les_auteurs);
	
	
}


//
// Ajouter un auteur
//

if ($flag_editable) {
	//echo debut_block_invisible("auteursarticle");

	$query = "SELECT * FROM spip_auteurs WHERE ";
	if ($les_auteurs) $query .= "id_auteur NOT IN ($les_auteurs) AND ";
	$query .= "statut!='5poubelle' AND statut!='nouveau' ORDER BY statut, nom";
	$result = spip_query($query);
	
	echo "<table width='100%'>";
	echo "<tr>";

	echo "<td>";
	
	
	if (spip_num_rows($result) > 0) {
		echo "<form action='?exec=gerer_liste&id_article=$id_article#auteurs' METHOD='post'>";
		echo "<span class='verdana1'><b>"._T('spiplistes:abon_ajouter')."</b></span>\n";
		echo "<div><input type='Hidden' name='id_article' value=\"$id_article\">";

		if (spip_num_rows($result) > 80 ) {
			echo "<input type='text' name='cherche_auteur' onClick=\"setvisibility('valider_ajouter_auteur','visible');\" class='fondl' value='' size='20'>";
			echo "<span  class='visible_au_chargement' id='valider_ajouter_auteur'>";
			echo " <input type='submit' name='Chercher' value='"._T('bouton_chercher')."' class='fondo'>";
			echo "</span>";
		}
		else {
			echo "<input type='Hidden' name='ajout_auteur' value='oui'>";
			echo "<SELECT name='nouv_auteur' size='1' STYLE='width:150px;' class='fondl' onChange=\"setvisibility('valider_ajouter_auteur','visible');\">";
			$group = false;
			$group2 = false;

			while ($row = spip_fetch_array($result)) {
				$id_auteur = $row["id_auteur"];
				$nom = $row["nom"];
				$email = $row["email"];
				$statut = $row["statut"];

				$statut=ereg_replace("0minirezo", _T('info_administrateurs'), $statut);
				$statut=ereg_replace("1comite", _T('info_redacteurs'), $statut);
				$statut=ereg_replace("2redac", _T('info_redacteurs'), $statut);

				$premiere = strtoupper(substr(trim($nom), 0, 1));

				if ($connect_statut != '0minirezo')
					if ($p = strpos($email, '@'))
						$email = substr($email, 0, $p).'@...';
				if ($email)
					$email = " ($email)";

				if ($statut != $statut_old) {
					echo "\n<option value=\"x\">";
					echo "\n<option value=\"x\"> $statut";
				}

				if ($premiere != $premiere_old AND ($statut != _T('info_administrateurs') OR !$premiere_old)) {
					echo "\n<option value=\"x\">";
				}

				$texte_option = supprimer_tags(couper("$nom$email", 40));
				echo "\n<option value=\"$id_auteur\">&nbsp;&nbsp;&nbsp;&nbsp;$texte_option";
				$statut_old = $statut;
				$premiere_old = $premiere;
			}

			echo "</SELECT>";
			echo "<span  class='visible_au_chargement' id='valider_ajouter_auteur'>";
			echo " <input type='submit' name='Ajouter' value="._T('bouton_ajouter')." class='fondo'>";
			echo "</span>";
		}
		echo "</div></FORM>";
	}
	
	echo "</td></tr></table>";


//	echo fin_block();
}

fin_cadre_enfonce(false);



//////////////////////////////////////////////////////
// Liste des mots-cles de l'article
//

if ($options == 'avancees' AND $articles_mots != 'non') {
	//formulaire_mots('articles', $id_article, '' , '' ,false,true);
}







//
// Bouton "modifier cet article"
//

if ($flag_editable) {
	echo "\n\n<div align=right><br />";
	
	if ($date_diff >= 0 AND $date_diff < 60 AND $auteur_modif > 0 AND $auteur_modif != $connect_id_auteur) {
		$query_auteur = "SELECT * FROM spip_auteurs WHERE id_auteur='$auteur_modif'";
		$result_auteur = spip_query($query_auteur);
		while ($row_auteur = spip_fetch_array($result_auteur)) {
			$nom_auteur_modif = typo($row_auteur["nom"]);
		}
		icone(_T('icone_modifier_article'), "?exec=liste_edit&id_article=$id_article", "warning-24.gif", "");
		echo "<font face='arial,helvetica,sans-serif' size=1>"._T('texte_travail_article', array('nom_auteur_modif' => $nom_auteur_modif, 'date_diff' => $date_diff))."</font>";
		echo aide("artmodif");
	}
	else {
		icone(_T('spiplistes:modifier_liste'), "?exec=liste_edit&id_article=$id_article", "article-24.gif", "edit.gif");
	}
	
	echo "</div>";
}


echo "</div>";


fin_cadre_relief();

//
// Forums
//
/*
echo "<br /><br />";

$forum_retour = urlencode("?exec=gerer_liste&id_article=$id_article");


echo "\n<div align='center'>";
	icone(_T('icone_poster_message'), "forum_envoi.php3?statut=prive&adresse_retour=".$forum_retour."&id_article=$id_article&titre_message=".urlencode($titre), "forum-interne-24.gif", "creer.gif");
echo "</div>";

echo "<p align='left'>";


$query_forum = "SELECT COUNT(*) AS cnt FROM spip_forum WHERE statut='prive' AND id_article='$id_article' AND id_parent=0";
$result_forum = spip_query($query_forum);
$total = 0;
if ($row = spip_fetch_array($result_forum)) $total = $row["cnt"];

if (!$debut) $debut = 0;
$total_afficher = 8;
if ($total > $total_afficher) {
	echo "<div class='serif2' align='center'>";
	for ($i = 0; $i < $total; $i = $i + $total_afficher){
		$y = $i + $total_afficher - 1;
		if ($i == $debut)
			echo "<font size=3><b>[$i-$y]</b></font> ";
		else
			echo "[<a href='articles.php3?id_article=$id_article&debut=$i'>$i-$y</a>] ";
	}
	echo "</div>";
}



$query_forum = "SELECT * FROM spip_forum WHERE statut='prive' AND id_article='$id_article' AND id_parent=0 ORDER BY date_heure DESC LIMIT $debut,$total_afficher";
$result_forum = spip_query($query_forum);
afficher_forum($result_forum, $forum_retour);


if (!$debut) $debut = 0;
$total_afficher = 8;
if ($total > $total_afficher) {
	echo "<div class='serif2' align='center'>";
	for ($i = 0; $i < $total; $i = $i + $total_afficher){
		$y = $i + $total_afficher - 1;
		if ($i == $debut)
			echo "<font size=3><b>[$i-$y]</b></font> ";
		else
			echo "[<a href='articles.php3?id_article=$id_article&debut=$i'>$i-$y</a>] ";
	}
	echo "</div>";
}

*/

echo "</div>\n";


if ($ok_nouveau_statut || $reindexer) {
	@flush();
	terminer_changement_statut();
}

////

// MODE EDIT LISTE FIN ---------------------------------------------------------

$spiplistes_version = "SPIP-listes 1.9b1";
echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$spiplistes_version."<p>" ;

fin_page();
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
