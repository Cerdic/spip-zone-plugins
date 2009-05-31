<?php
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/pclzip');
include_once('inc-fichiers.php');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN.'inc-html.php');

define(MAX_FILE_SIZE,700*1024); //taille max du fichier téléchargé


setlocale(LC_TIME, "fr_FR");

global $debug, $txt_gauche, $txt_debug;
$debug=false;
$showpost=false;

if($showpost) {
   echo "_POST :<pre>";print_r($_POST);echo "</pre>\n";
   //echo "GLOBALS :<pre>";print_r($GLOBALS);echo "</pre>\n";
   echo "Etablissement (nom_site) : ".$GLOBALS["auteur_session"]["nom_site"];
}


$txt_gauche="";// texte boite de gauche
$txt_="";// texte boite de droite
$txt_debug=""; // texte debug
define(OK,"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define(KO,"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

// ex&eacute;cut&eacute; automatiquement par le plugin au chargement de la page ?exec=odb_import
function exec_odb_import() {
global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $odb_mapping;

debut_page(_T('Import de fichiers ODB'), "", "");
echo "<br /><br />";
gros_titre(_T('Office Du Baccalaur&eacute;at'));


debut_cadre_relief( "", false, "", $titre = _T('Import de fichiers CSV hors ligne ODB'));
//debut_boite_info();
echo '<br>';

$REFERER=$_SERVER['HTTP_REFERER'];
$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
$URL_SCRIPT="../plugins/odb/odb_import";
$required_ext="zip";


echo "<IMG SRC='$URL_SCRIPT/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";

if(isset($_POST["step3"])) {
//////////////////////////////////////////////// step 3 : import r&eacute;el des fichiers
   foreach($_POST as $key=>$val)
      if(substr_count($key,"csvfile")>0) {
         $val=str_replace('@_quote_@',"'",$val);
         $tab_tmp=explode("|",$key);
         $cle=$tab_tmp[1];
         $tab_fic[$cle]=$val;
      }
   //echo debut_gauche();
   foreach($tab_fic as $val) {
      echo gros_titre("Import de [".$val[0]."]");
      echo debut_boite_info();
      $cpt=0;
      $tab_tmp=explode("|",$val[1]);
      $table=$tab_tmp[0];
      $annee=$tab_tmp[1];
      //echo $val[2]."<br/>\n";
      $sql="SELECT * FROM $table LIMIT 0,1";
      $result=mysql_query($sql) or die (KO." - Requête $sql<br/>".mysql_error());
      $nb_fields=mysql_num_fields($result);

      for ($i=0;$i < $nb_fields;$i++) {
         $chp=mysql_field_name($result, $i);
         if(isset($odb_mapping[$table][$chp]))
            $champ[$i]=$chp;
      }
      $colonnes=implode(",",$champ);
      //echo $val[2];
      foreach($val as $nb => $sql) {
         if($nb>2) {
            $tab_tmp=explode(",",$sql);
            $pk=$tab_tmp[0];//"primary key" : on supprime dans la table temporaire à partir de cette donn&eacute;e avant nouvel import
            $sql_delete="DELETE FROM $table WHERE ".$champ[0]."=$pk AND annee=$annee";
            $sql="INSERT INTO $table ($colonnes) VALUES ($sql,'".date("Y-m-d H:i:s")."')";
            mysql_query($sql_delete) or die (KO." - Suppression impossible : $sql_delete<br/>".mysql_error());
            mysql_query($sql) or die (KO." - Ajout impossible : $sql<br/>".mysql_error());
            if($debug) echo "$sql_delete<br/>$sql<br/><br/>\n";
            $cpt++;
         }
      }
      echo "Ajout de <b>$cpt</b> lignes dans <b>$table</b> pour l'ann&eacute;e <b>$annee</b> ".OK;
      echo fin_boite_info();
   }

} elseif (isset($_POST["reprise"])) {
//////////////////////////////////////////////// step2 - REPRISE : pr&eacute;visualisation import
   $table=$_POST["table"];
   $annee=$_POST["annee"];
   $uploaddir = _DIR_PLUGIN_ODB_IMPORT."upload/";
   $txt_gauche.="Reprise de l'import pr&eacute;c&eacute;dent ".OK."<BR>\n</BR>";
   $txt_droite.=reprise_import($uploaddir,$table,$annee);

   debut_gauche();
      debut_boite_info();
         echo "<b>Pr&eacute;visualisation avant import</b><br/>\nV&eacute;rifiez ici le contenu de vos fichiers <b>csv</b> avant l'import effectif";
      fin_boite_info();
   debut_droite();
      debut_boite_info();
         echo $txt_gauche;
      fin_boite_info();
      echo "<br/>\n";
      debut_cadre_relief("", false, $titre = _T("Import de l'archive <b>$table</b>"));
         echo $txt_droite;
      fin_cadre_relief();
   if($debug){
      debut_cadre_relief("", false, $titre = _T("Import de l'archive <b>$table</b>"));
         echo $txt_debug;
      fin_cadre_relief();
   }

} elseif ($_POST['step2']=='import') {
//////////////////////////////////////////////// step2 : pr&eacute;visualisation import
   $table=$_POST["table"];
   $annee=$_POST["annee"];

   $uploaddir = _DIR_PLUGIN_ODB_IMPORT."/upload/";
   $nom_fichier=basename($_FILES['import_odb']['name']);
   $uploadfile = $uploaddir . $nom_fichier;
   $txt_gauche=vide_repertoire($uploaddir);

   if($debug) {echo "<pre>";print_r($_FILES);echo "</pre>\n";}

   if (move_uploaded_file($_FILES['import_odb']['tmp_name'], $uploadfile)) {
      if (detecte_extension($uploadfile)!="$required_ext") die (KO." - Vous devez charger un fichier <b>$required_ext</b>.<BR/>Veuillez <A HREF='".generer_url_ecrire("odb_import")."'>recommencer</A>");
      $txt_gauche .= "Chargement <I>$nom_fichier</I> ".OK."<BR/>\n";
      if($required_ext=='zip') {
         // si fichier uploadé est zippé
         if(!unzip($uploadfile,$uploaddir)) {
            die (KO." - D&eacute;compression du fichier impossible. Archive corrompue ?<BR/>");
         }
      }
      $txt_gauche.="D&eacute;compression de l'archive zip ".OK."<BR/>\n";
      $txt_droite.= "<FORM NAME='suite_import' method='post' class='forml spip_xx-small'>\n"
                  . "   <INPUT TYPE='hidden' name='table' value='$table'/>\n"
                  . "   <INPUT TYPE='hidden' name='annee' value='$annee'/>\n"
                  . "   <INPUT TYPE='hidden' name='reprise' value='from_step2_import'/>\n"
                  . "   Votre fichier a &eacute;t&eacute; enregistr&eacute; sur le serveur<br/>\n"
                  . "   <div align='right'><INPUT TYPE='submit' name='ok' class='fondo' value=\"Continuer l'import &gt;&gt;&gt;\"/></div>\n"
                  . "</FORM>\n"
                  ;
   } else {
      switch($_FILES['import_odb']['error']) {
         case 1 : $erreur='Le fichier t&eacute;l&eacute;charg&eacute; exc&egrave;de la taille de upload_max_filesize, configur&eacute;e dans le php.ini';
            break;
         case 2 : $erreur='Le fichier t&eacute;l&eacute;charg&eacute; exc&egrave;de la taille de MAX_FILE_SIZE ('.round(MAX_FILE_SIZE/1024,1).' Ko), qui a &eacute;t&eacute; sp&eacute;cifi&eacute;e dans le formulaire HTML.';
            break;
         case 3 : $erreur='Le fichier n\'a &eacute;t&eacute; que partiellement t&eacute;l&eacute;charg&eacute;.';
            break;
         case 4 : $erreur='Aucun fichier n\'a &eacute;t&eacute; t&eacute;l&eacute;charg&eacute;.';
            break;
         case 6 : $erreur='Un dossier temporaire est manquant. ';
            break;
         case 7 : $erreur='&Eacute;chec de l\'&eacute;criture du fichier sur le disque.';
            break;
         case 8 : $erreur='L\'envoi de fichier est arr&ecirc;t&eacute; par l\'extension.';
            break;
      }

      die(KO." - Chargement $uploadfile : <br/>\n$erreur\n");
   }
   if($required_ext=='zip') {
      $annee=date("Y");
      $mois=date("m-").strftime("%B");
      $jour=date("d");
      $url_archives=$uploaddir."archives";
      if (!is_dir("$url_archives/$annee")) mkdir("$url_archives/$annee");
      if (!is_dir("$url_archives/$annee/$mois")) mkdir("$url_archives/$annee/$mois");
      if (!is_dir("$url_archives/$annee/$mois/$jour")) mkdir("$url_archives/$annee/$mois/$jour");
      $url_dest="$url_archives/$annee/$mois/$jour";
      if(is_file("$url_dest/$nom_fichier")) unlink("$url_dest/$nom_fichier");
      if (rename($uploadfile,"$url_dest/$nom_fichier"))
         $txt_gauche.="D&eacute;placement de l'archive $required_ext dans <A HREF='$url_dest'>archives/$annee/$mois/$jour</A> ".OK;
      else
         die(KO." - D&eacute;placement du fichier $nom_fichier vers archives/$annee/$mois/$jour impossible\n");
   }
   debut_gauche();
      debut_boite_info();
         echo "<b>Pr&eacute;visualisation avant import</b><br/>\nV&eacute;rifiez ici le contenu de vos fichiers <b>csv</b> avant l'import effectif";
      fin_boite_info();
   debut_droite();
      debut_boite_info();
         echo $txt_gauche;
      fin_boite_info();
      echo "<br/>\n";
      debut_cadre_relief("", false, $titre = _T("Import de l'archive <b>$table</b>"));
         echo $txt_droite;
      fin_cadre_relief();
   if($debug){
      debut_cadre_relief("", false, $titre = _T("Import de l'archive <b>$table</b>"));
         echo $txt_debug;
      fin_cadre_relief();
   }
} else {
//////////////////////////////////////////////// step 1 : affichage interface import
   debut_gauche();
      odb_raccourcis('odb_import');
   creer_colonne_droite();
   debut_droite();
      debut_cadre_relief("", false, "", $titre = _T('Import de fichier'));
      // affiche formulaire d'import de fichier (peut limiter à l'extension $ext facultative)
      $texte= "Importer archive";
      $url=generer_url_ecrire('odb_import');
      $fichier='import_odb';
      // Le type d'encodage des donn&eacute;es, enctype, DOIT être sp&eacute;cifi&eacute; comme ce qui suit
      echo "<!-- FORMULAIRE $fichier -->\n";
      echo "<form id='form_$fichier' enctype='multipart/form-data' action='$url' method='post' class='forml spip_xx-small'>\n";
      // MAX_FILE_SIZE doit pr&eacute;c&eacute;der le champs input de type file
      echo "\t<input type='hidden' name='MAX_FILE_SIZE' value='".MAX_FILE_SIZE."' />\n";
      echo "\t<label for='table'>Veuillez choisir une table et une ann&eacute;e</label>\n";
      //bouton_radio($nom, $valeur, $titre, $actif = false, $onClick="")
      $liste = array("odb_candidats"=>"Candidats","odb_notes"=>"Notes");
      echo afficher_choix("table","odb_candidats",$liste," ");
      echo "<SELECT name='annee'>\n";
      echo formSelectAnnee(date("Y"));
      echo "</SELECT>\n";
      echo "<BR/>\n";
      // Le nom de l'&eacute;l&eacute;ment input d&eacute;termine le nom dans le tableau $_FILES
      if($required_ext!="") {
         $js="onchange=\"nom=this.value;taille=nom.length;ext=nom[taille-3]+nom[taille-2]+nom[taille-1];if(ext=='$required_ext') document.forms['form_$fichier'].submit_$fichier.disabled=false;else {document.forms['form_$fichier'].submit_$fichier.disabled=true;alert('Vous essayez d\'envoyer un fichier ['+ext+']\\nVeuillez choisir un fichier [$required_ext] svp');}\"";
         $str.=vignette($required_ext,"Veuillez choisir un fichier $required_ext");
      }
      echo "\t<label for='$fichier'>$texte </label>\n";
      echo "\t<input id='$fichier' name='$fichier' type='file' $js/><br/>\n";
      echo "\t<input id='submit_$fichier' class='fondo' type='submit' value='Envoyer le fichier $required_ext' disabled />\n";
      echo "\t<input id='reprise' name='reprise' class='fondo' type='submit' value=\"Reprendre l'import pr&eacute;c&eacute;dent\" />\n";
      echo "\t<input type='hidden' name='step2' value='import' /><br/>\n";
      echo "</form>\n";
      fin_cadre_relief();
}
//fin_boite_info();
fin_cadre_relief();
fin_page();
exit;
}
?>


