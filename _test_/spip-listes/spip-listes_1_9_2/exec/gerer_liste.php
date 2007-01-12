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
include_spip('inc/lang');
include_spip('inc/affichage');
include_spip('base/spip-listes');


function exec_gerer_liste()
{

global $connect_statut;
global $connect_toutes_rubriques;
global $connect_id_auteur;
global $type;
global $new;
global $connect_statut;

global $id_liste;
global $modifier_message;
global $titre;
global $texte,$pied_page;


global $statut_nouv;
global $flag_auteur,$creer_auteur,$ajout_auteur,$supp_auteur,$cherche_auteur,$nouv_auteur,$valider_ajouter_auteur;
global $ok_nouveau_statut,$changer_lang;

global $Valider_auto,$Modifier;
global $auto;
global $changer_extra,$email_envoi,$patron,$periode,$sujet_message;
global $envoyer_direct;

global $debut;
 
$nomsite=lire_meta("nom_site"); 
$urlsite=lire_meta("adresse_site"); 

 
// Admin SPIP-Listes
echo debut_page("Spip listes", "redacteurs", "spiplistes");

if ($connect_statut != "0minirezo" ) {
	echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
	echo fin_page();
	exit;
}

if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
	$statut_auteur=$statut;
	spip_listes_onglets("messagerie", "Spip listes");
}




// Creer une liste -----------------------------------------
////

if ($id_liste==0) {
	if ($new=='oui') {

		if ($titre=='') $titre = _T('spiplistes:liste_sans_titre');

		spip_query("INSERT INTO spip_listes (statut, date, lang) VALUES ('inact', NOW(),'$langue_new')");
		$id_liste = spip_insert_id();
		//Auteur de la liste (moderateur)
		spip_query("DELETE FROM spip_auteurs_listes WHERE id_liste = $id_liste");
		spip_query("INSERT INTO spip_auteurs_listes (id_auteur, id_liste) VALUES ($connect_id_auteur, $id_liste)");
		//abonner le moderateur a sa liste
		spip_query("DELETE FROM spip_abonnes_listes WHERE id_liste = $id_liste");
		spip_query("INSERT INTO spip_abonnes_listes (id_auteur, id_liste) VALUES ($connect_id_auteur, $id_liste)");
		
		
	} 
	
	
}



debut_gauche();

echo debut_boite_info();

echo '<div align="center">
<font face="Verdana,Arial,Sans,sans-serif" size="1"><b>'._T('spiplistes:liste_numero').'&nbsp;:</b></font>
<br><font face="Verdana,Arial,Sans,sans-serif" size="6"><b>'.$id_liste.'</b></font>
</div>';

echo fin_boite_info();

spip_listes_raccourcis();

creer_colonne_droite();


debut_droite("messagerie");


//////////////////////////////////////////////////////
// Determiner les droits d'edition de la liste
//

$query = "SELECT statut, titre, maj FROM spip_listes WHERE id_liste=$id_liste";
$result = spip_query($query);
if ($row = spip_fetch_array($result)) {
	$statut_article = $row['statut'];
	$titre_article = $row['titre'];
	$maj = $row['maj'];
}
else {
	$statut_article = '';
}

$query = "SELECT * FROM spip_auteurs_listes WHERE id_liste=$id_liste AND id_auteur=$connect_id_auteur";
$result_auteur = spip_query($query);

//
// Droits mieux structuré que ca ?
//

$flag_auteur = (spip_num_rows($result_auteur) > 0);
$flag_editable = ($flag_auteur AND ($statut_article == 'inact' OR $statut_article == 'liste' OR $statut_article == 'poublist'));


//
// Appliquer les modifications sur la liste
//

$ok_nouveau_statut = false;

function terminer_changement_statut() {
	global $ok_nouveau_statut, $statut_nouv, $statut_ancien, $id_liste, $reindexer;

	if ($ok_nouveau_statut) {
		//calculer_rubriques();
		if ($statut_nouv == 'publie' AND $statut_ancien != $statut_nouv) {
			include_spip('inc/mail');
			envoyer_mail_publication($id_liste);
		}
		
	}

	
}


//Modifier le statut de la liste
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
		$query = "UPDATE spip_listes SET statut='$statut_nouv' WHERE id_liste=$id_liste";
		$result = spip_query($query);

	}
}

//modifier la date
if ($jour && $flag_editable) {
	if ($annee == "0000") $mois = "00";
	if ($mois == "00") $jour = "00";
	$query = "UPDATE spip_listes SET date='$annee-$mois-$jour' WHERE id_liste=$id_liste";
	$result = spip_query($query);
	calculer_rubriques();
}




// Enregistrer les modifs sur la liste

if ($titre && !$ajout_forum && $flag_editable) {
	$titre = addslashes(corriger_caracteres($titre));
	$descriptif = addslashes(corriger_caracteres($descriptif));
	$texte = addslashes(corriger_caracteres($texte));
	$pied_page = addslashes(corriger_caracteres($pied_page));


	$query = "UPDATE spip_listes SET titre=\"$titre\", descriptif=\"$descriptif\", texte=\"$texte\", pied_page=\"$pied_page\" $add_extra WHERE id_liste=$id_liste";
	$result = spip_query($query);


	// afficher le nouveau titre dans la barre de fenetre
	$titre_article = stripslashes($titre);

}

if($changer_lang){
	$query = "UPDATE spip_listes SET lang=\"$changer_lang\" WHERE id_liste=$id_liste";
	$result = spip_query($query);
}

// prendre en compte les modifs sur les extras
if($Valider_auto){


        if($auto == "oui"){
        $query = "UPDATE spip_listes SET message_auto='oui' WHERE id_liste=$id_liste";
		$result = spip_query($query);
        if($maj =="0000-00-00 00:00:00"){
        $query = "UPDATE spip_listes SET maj=NOW() WHERE id_liste=$id_liste";
	    $result = spip_query($query);
	    }
        }
         elseif($auto == "non"){
             $query = "UPDATE spip_listes SET message_auto='non', maj='0000-00-00 00:00:00' WHERE id_liste=$id_liste";
			 $result = spip_query($query);
			 }

			 
		if(email_valide($email_envoi)){
         $query = "UPDATE spip_listes SET email_envoi='$email_envoi' WHERE id_liste=$id_liste";
		 $result = spip_query($query);
         }
         


   if(($changer_extra == "oui") AND ($auto == "oui") ){
	// On recupere les extras


      $query = "UPDATE spip_listes SET patron='$patron', periode='$periode', titre_message='$sujet_message' WHERE id_liste=$id_liste";
	  $result = spip_query($query);
        
        if($envoyer_direct){
        $majnouv = (time() - ($periode * 3600*24));
        $query = "UPDATE spip_listes SET maj=FROM_UNIXTIME($majnouv), periode='$periode' WHERE id_liste=$id_liste";
	    $result = spip_query($query);
        }

      
      }
}



//
// Lire la liste
//

$query = "SELECT * FROM spip_listes WHERE id_liste='$id_liste'";
$result = spip_query($query);

if ($row = spip_fetch_array($result)) {
	$id_liste = $row["id_liste"];
	$titre = $row["titre"];
	$titre_message = $row["titre_message"];
	$pied_page = $row["pied_page"];
	$texte = $row["texte"];
	$date = $row["date"];
	$statut_article = $row["statut"];
	$maj_nouv = $row["maj"];
	$email_envoi=$row["email_envoi"];
	$message_auto = $row["message_auto"];
	$periode = $row["periode"];
	$patron = $row["patron"];
	$lang = $row["lang"];
}


changer_typo('','article'.$id_liste);

	
echo debut_cadre_relief();
echo "<center>";

//
// Titre, surtitre, sous-titre
//

if($statut == 'liste') $logo_statut = 'puce-verte.gif';
if($statut == 'inact') $logo_statut = 'puce-blanche.gif';
if($statut == 'poublist') $logo_statut = 'puce-blanche.gif';



echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
echo "<tr width='100%'><td width='100%' valign='top'>";


	gros_titre($titre, $logo_statut);

	echo "<div style='margin:10px 0px 10px 0px'>";
	echo justifier(propre($texte));
	echo "</div>";
	
	
echo "</td>";


if ($flag_editable) {
	echo "<td><img src='img_pack/rien.gif' width=5></td>\n";
	echo "<td align='center'>";

		icone(_T('spiplistes:modifier_liste'), "?exec=liste_edit&id_liste=$id_liste", "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/reply-to-all-24.gif", "edit.gif");

	echo "</td>";
}
echo "</tr></table>\n";


echo fin_cadre_relief();




//////////////////////////////////////////////////////
// Modifier le statut de la liste
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
	echo debut_cadre_relief("racine-site-24.gif");
  	echo "<form action='?exec=gerer_liste&id_liste=$id_liste' METHOD='get'>";
	
	echo "<input type='Hidden' name='exec' value='gerer_liste'>";

        echo "<input type='Hidden' name='id_liste' value=\"$id_liste\">";

	echo "<b>"._T('spiplistes:Cette_liste_est').": </b> ";

	echo "<select name='statut_nouv' size='1' class='fondl' onChange='change_bouton(this)'>";

	echo "<option" . mySel("inact", $statut_article) ." style='background-color: white'>"._T('spiplistes:statut_interne')."\n";
	echo "<option" . mySel("liste", $statut_article) . " style='background-color: #B4E8C5'>"._T('spiplistes:statut_publique')."\n";
	echo "<option" . mySel("poublist", $statut_article) . " style='background:url(img_pack/rayures-sup.gif)'>"._T('texte_statut_poubelle')."\n";

	echo "</select>";

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
	echo "</form>";	
	
	echo "<div style='margin:10px 0px 10px 0px'>";
	echo menu_langues('changer_lang', $lang , '<strong>Langue :</strong>&nbsp;','', '');
	echo "</div>";
	
	//regler email d'envoi de la liste

	echo "<form action='?exec=gerer_liste&id_liste=$id_liste' method='post'>";
		 
	
$email_defaut = entites_html(lire_meta("email_webmaster"));
$email_envoi = (email_valide($email_envoi)) ? $email_envoi : $email_defaut ;
		
		echo "<strong>";
		echo _T('spiplistes:retour')."</strong><br />";

		echo "<p>"._T('spiplistes:adresse')."</p>";
		echo "<input type='text' name='email_envoi' value=\"".$email_envoi."\" size='20' class='fondl'>&nbsp;";
		
	
	if($id_liste)
	echo "<input type='hidden' name='id_liste' value='$id_liste'>";
	if($new)
	echo "<input type='hidden' name='new' value='$new'>";
	echo "<input type='submit' name='Valider_auto' value='"._T('bouton_valider')."' class='fondo'>";
	
	
	echo "</form>";

	
	echo fin_cadre_relief();

}






echo debut_cadre_relief("../"._DIR_PLUGIN_SPIPLISTES."/img_pack/stock_timer.gif");
     
echo "<form action='?exec=gerer_liste&id_liste=$id_liste' METHOD='post'>";
		 
	// programmer un courrier automatique
echo "<h3>"._T('spiplistes:program')."</h3>";
   

echo "<table border=0 cellspacing=1 cellpadding=3 width=\"100%\">";
	
echo "<tr><td background='img_pack/rien.gif' align='$spip_lang_left' class='verdana2'>";
if ($message_auto != "oui") {
echo _T('spiplistes:non_program');
}
else {
     if(($changer_extra == "oui") AND ($auto == "oui") )
     echo "<h2>"._T('spiplistes:date_act')."</h2>" ;
     
     echo "<h3> Sujet du courrier automatique : $titre_message</h3>";
     
     echo _T('spiplistes:env_esquel')." <em>".$patron."</em> " ;
	
				
    echo "<br />"._T('spiplistes:Tous_les')."  <b>".$periode."</b>  "._T('info_jours') ;
	
	
           $dernier_envoi =  strtotime($maj_nouv)  ;

            $sablier = (time() - $dernier_envoi) ;
            
           
           $proch = round(  (( (24*3600*$periode) - $sablier) / (3600*24)) ) ;
            $last = round(  ($sablier / (3600*24)) ) ;
            echo "<br />Dernier envoi il y a <b>$last</b> "._T('spiplistes:jours')."<br />";
           if($proch != 0) {
            echo "<br />"._T('spiplistes:prochain_envoi_prevu_dans')."<b>$proch</b> "._T('spiplistes:jours')."<br />";
            }
            else {
                 	echo "<br />"._T('spiplistes:prochain_envoi_aujd')."<br />";
            }

}
		


  

echo "</td></tr>";


echo "<tr><td background='img_pack/rien.gif' align='$spip_lang_left' class='verdana2'>";
	
        
        if ($message_auto != "oui") {
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
		
		$sujet_message = ($titre_message=='') ? $titre." de ".$nomsite : $titre_message ;
		
		echo "<ul style='list-style-type:none;'>";
               echo "<li>Sujet : <input type='titre_message' name='sujet_message' value='".$sujet_message."' size='50' class='fondl'> </li>" ;
               
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
			 ($patron == $titre_option) ? $selected = "selected='selected" : $selected ="" ;

                        echo "<option ".$selected." value='".$titre_option."'>".$titre_option."</option>\n";
			}
					}
				echo "</select>";
        		closedir($dh);
   		  		}
		}
		
		echo "</li>";
		
		

		echo "<li>"._T('spiplistes:Tous_les')." <input type='text' name='periode' value='".$periode."' size='4' class='fondl'> "._T('info_jours')."</li>" ;
	
        	if(!$envoyer_direct){
                echo " <li><input type='checkbox' class='checkbox' name='envoyer_direct' id='box' class='fondl' /><label for='box'>"._T('spiplistes:env_maint')."</label></li>";

                }

		echo "</ul><br />";
	
		
		echo "<br /><input type='radio' name='auto' value='non' id='auto_non'>";
		echo " <label for='auto_non'>"._T('spiplistes:prog_env_non')."</label> ";

	}
	echo "</td></tr>\n";
	
	echo "<tr><td style='text-align:$spip_lang_right;'>";
	if($id_liste)
	echo "<input type='hidden' name='id_liste' value='$id_liste'>";
	if($new)
	echo "<input type='hidden' name='new' value='$new'>";
	echo "<input type='submit' name='Valider_auto' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</td></tr>";
	echo "</table>\n";
	
	echo "</form>";
		


echo fin_cadre_relief();


//
// Liste des abonnes
//

////////////////////////////////////////////////////
// Gestion des auteurs
//

// Creer un nouvel abonne et l'ajouter

if ($creer_auteur AND $connect_statut=='0minirezo'){
	$creer_auteur = addslashes($creer_auteur);
	$query_creer = "INSERT INTO spip_auteurs (nom, statut) VALUES (\"$creer_auteur\", '1comite')";
	$result_creer = spip_query($query_creer);

	$nouv_auteur = spip_insert_id();
	$ajout_auteur = true;
}

//
// Appliquer les modifications sur les abonnes
//


if ($supp_auteur && $flag_editable) {
	$query="DELETE FROM spip_abonnes_listes WHERE id_auteur='$supp_auteur' AND id_liste='$id_liste'";
	$result=spip_query($query);
}




echo "<a name='auteurs'></a>";
echo debut_cadre_enfonce("auteur-24.gif", false, "",  _T('spiplistes:abon').aide ("artauteurs"));

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
	echo debut_boite_info();
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
				echo " | <a href=\"".generer_url_ecrire("gerer_liste","id_liste=$id_liste&ajout_auteur=oui&nouv_auteur=$new_auteur#auteurs")."\">"._T('lien_ajouter_auteur')."</a>";

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

	

	echo fin_boite_info();
	echo "<p>";

}

if ($ajout_auteur && $flag_editable) {
	if ($nouv_auteur > 0) {
		$query="DELETE FROM spip_abonnes_listes WHERE id_auteur='$nouv_auteur' AND id_liste='$id_liste'";
		$result=spip_query($query);
		$query="INSERT INTO spip_abonnes_listes (id_auteur,id_liste) VALUES ('$nouv_auteur','$id_liste')";
		$result=spip_query($query);
		//attribuer un format de réception si besoin (ancien auteur)
		$extra_format=get_extra($nouv_auteur,"auteur");
		if(!$extra_format["abo"]){
		$extra_format["abo"] = "html";
		set_extra($nouv_auteur,$extra,'auteur');
		}
	
	}

	if (lire_meta('activer_moteur') == 'oui') {
		include_spip ('inc/indexation');
		indexer_article($id_liste);
	}
}

//
// Afficher les abonnes
//

//
// Liste des abonnes a la liste
//

$query_ = "SELECT * FROM spip_auteurs AS auteurs, spip_abonnes_listes AS lien ".
	"WHERE auteurs.id_auteur=lien.id_auteur AND lien.id_liste=$id_liste ".
	"GROUP BY auteurs.id_auteur ORDER BY auteurs.nom";
$result_ = spip_query($query_);
$nombre_auteurs = spip_num_rows($result_);

if ($nombre_auteurs) {
	
	echo "<div class='liste' style='clear:both'>";
	
	echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
	
	// Lire les auteurs qui nous interessent
// et memoriser la liste des lettres initiales
//

$retour=generer_url_ecrire("gerer_liste", "id_liste=$id_liste");

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
		
		$query2 = "SELECT COUNT(articles.id_liste) AS compteur ".
			"FROM spip_abonnes_listes AS lien, spip_listes AS articles ".
			"WHERE lien.id_auteur=$idi_auteur AND articles.id_liste=lien.id_liste ".
			"AND articles.statut IN $aff_articles GROUP BY lien.id_auteur";
		$result2 = spip_query($query2);
		if ($result2) list($nombre_articles) = spip_fetch_array($result2,SPIP_NUM);
		else $nombre_articles = 0;

		$url_auteur = generer_url_ecrire("abonne_edit","id_auteur=$idi_auteur");

		$vals[] = bonhomme_statut($row);

		$vals[] = "<a href=\"$url_auteur\"$bio_auteur>".typo($nom_auteur)."</a>";

		//$vals[] = bouton_imessage($idi_auteur);

		
		
		if ($email_auteur) $vals[] =  "<a href='mailto:$email_auteur'>"._T('email')."</a>";
		else $vals[] =  "&nbsp;";

		if ($url_site_auteur) $vals[] =  "<a href='$url_site_auteur'>"._T('info_site_min')."</a>";
		else $vals[] =  "&nbsp;";

		if ($nombre_articles > 1) $vals[] =  $nombre_articles.' listes' ;
		else if ($nombre_articles == 1) $vals[] = '1 liste';
		else $vals[] =  "&nbsp;";

		
			$vals[] =  "<a href='".generer_url_ecrire("gerer_liste","id_liste=$id_liste&supp_auteur=$idi_auteur#auteurs")."'>"._T('spiplistes:desabonnement')."<img src='img_pack/croix-rouge.gif' alt='X' width='7' height='7' border='0' align='middle'></a>";
		
		
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
		echo "<form action='?exec=gerer_liste&id_liste=$id_liste#auteurs' method='post'>";
		echo "<span class='verdana1'><b>"._T('spiplistes:abon_ajouter')."</b></span>\n";
		echo "<div><input type='Hidden' name='id_liste' value=\"$id_liste\">";

		if (spip_num_rows($result) > 80 ) {
			echo "<input type='text' name='cherche_auteur' onClick=\"setvisibility('valider_ajouter_auteur','visible');\" class='fondl' value='' size='20'>";
			echo "<span  class='visible_au_chargement' id='valider_ajouter_auteur'>";
			echo " <input type='submit' name='Chercher' value='"._T('bouton_chercher')."' class='fondo'>";
			echo "</span>";
		}
		else {
			echo "<input type='Hidden' name='ajout_auteur' value='oui'>";
			echo "<select name='nouv_auteur' size='1' STYLE='width:150px;' class='fondl' onChange=\"setvisibility('valider_ajouter_auteur','visible');\">";
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

			echo "</select>";
			echo "<span  class='visible_au_chargement' id='valider_ajouter_auteur'>";
			echo " <input type='submit' name='Ajouter' value="._T('bouton_ajouter')." class='fondo'>";
			echo "</span>";
		}
		echo "</div></form>";
	}
	
	echo "</td></tr></table>";


//	echo fin_block();
}

echo fin_cadre_enfonce(false);


/// fin abonnes



if ($ok_nouveau_statut || $reindexer) {
	@flush();
	terminer_changement_statut();
}

////

// MODE EDIT LISTE FIN ---------------------------------------------------------

echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."<p>" ;

    echo fin_gauche(), fin_page();
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
