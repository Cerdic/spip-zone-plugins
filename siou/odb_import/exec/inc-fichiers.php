<?php

define(MAX_COLONNES_AFFICHEES,10); // nb de colonnes affich&eacute;es max
define(MAX_LIGNES_AFFICHEES,5); // nb de colonnes affich&eacute;es max
//if(_DIR_PLUGIN_ODB_IMPORT==trim("_DIR_PLUGIN_ODB_IMPORT")) die("<U>/!\</U> Constante _DIR_PLUGIN_ODB_IMPORT doit être d&eacute;finie pour appeler ".__FILE__);
global $debug;
// d&eacute;compresse l'archive zip dans le r&eacute;pertoire de destination (true si ok)
function unzip($archive,$destination) {
   global $debug,$txt_debug;
   if($debug) {
      $txt_debug.= "=== unzip ===<BR/>Archive $archive<BR>destination $destination<br/>\n";
   }
   $zip = new PclZip($archive);
   $ok = $zip->extract(
            PCLZIP_OPT_PATH, $destination,
            PCLZIP_OPT_REPLACE_NEWER);
   if ($zip->error_code<0) {
      debut_html();
      echo _TT('tradloader:donnees_incorrectes',
               array('erreur' => $zip->errorInfo()));
      fin_html();
      return false;
   }
   return true;
}

/**
 * Detecte l'extension d'un fichier (qui doit comporter un point)
 * 
 * @param string $fichier : nom du fichier dont il faut detecter l'extension
 * @return string : extension du fichier
 */
function detecte_extension ($fichier) {
   $extension = substr(strrchr($fichier, "."), 1);
   return strtolower($extension);
}

/**
 * Vide un r&eacute;pertoire de ses fichiers (pas ses sous-repertoires)
 * 
 * @param string $rep : chemin du repertoire a vider
 */ 
function vide_repertoire($rep) {

   if ($handle = opendir($rep)) {
      /* Ceci est la façon correcte de traverser un dossier. */
      while (false !== ($fichier = readdir($handle))) {
         if($fichier!="." && $fichier!=".." && $fichier!="archives")
            if (unlink($rep.$fichier))
               $deleted.= "Suppression du fichier temporaire <I>$fichier</I> ".OK."<br/>\n";
            else
               die(KO." - Suppression des fichiers temporaires impossible : $rep $fichier\n");
      }
      closedir($handle);
      return $deleted;
   } else
      die(KO." - Impossible d'ouvrir le r&eacute;pertoire $rep\n");
   /* php5
      $files = scandir($rep);
      foreach($files as $fichier) {
         if($fichier!="." && $fichier!=".." && $fichier!="archives")
            if (unlink($rep.$fichier)) $deleted.= "<BR/>Suppression du fichier temporaire <I>$fichier</I> ".OK."<br/>\n";
            else die(KO." - Suppression des fichiers temporaires impossible : $rep $fichier\n");
      }
   */
}

/**
 * Lit et affiche le contenu d'un fichier csv 
 *
 * @param string $fichier : fichier CSV
 * @param string $table : table MySQL
 * @param int $annee : annee
 * @param boolean $tout_afficher : afficher la table complete ? (false par defaut)
 * @param string $separateur : separateur du fichier CSV (',' par defaut)
 * @param int $ligneDebutImport : premiere ligne a importer (1 par defaut : saute ligne de titre)
 * @return array("html"=>code html (eventuellement limite) affichage,
 * 				 "sql" =>tableau de requetes sql a partir de la ligne $ligneDebutImport (defaut==1 => sauter ligne titre)
 */
function affiche_csv($fichier,$table,$annee,$tout_afficher=false,$separateur=",",$ligneDebutImport=1) {
   global $debug, $txt_debug, $odb_referentiel,$odb_mapping;
   include_once(DIR_ODB_COMMUN."inc-referentiel.php");
   include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");

   if($debug) {
      echo "<div align='left'>\n";
      echo "<pre>odb_referentiel ";
      print_r($odb_referentiel);
      echo "</pre><hr/>";
      echo "<pre>odb_mapping ";
      print_r($odb_mapping);
      echo "</pre><hr/></div>\n";
   }

   $str="";
   $tr_debug="";
   $row_titres="";
   $fatal="";
   if(file_exists($fichier)) {
      $nom_fichier = substr(strrchr($fichier, "/"), 1);
      $flag_lignes_depasse=false;
      $flag_col_depasse=false;
      if($debug)
         $txt_debug .= "=== affiche_csv ===<BR/>Fichier CSV : $fichier<BR>\n";
      $row = -1;
      $handle = fopen($fichier, "r");
      $str.="<div class='verdana2 spip_large'>"
            . vignette('csv','Fichier')
            . "$nom_fichier</div>\n"
            ;
      $str .= "<table class='spip'>\n";
      $estPaire=0;
      $tab_csv["sql"]["debut"]="$table|$annee";

      while (($data = fgetcsv($handle, 1000, $separateur))) {
         $row++;
         $num = count($data);
         if($row>MAX_LIGNES_AFFICHEES && !$tout_afficher)
            $flag_lignes_depasse = true;
         else {
            if($row==0) {
               $td="TH";
               $col1="No";
               $tr_class="row_first";
            } else {
               $td="TD";
               $col1=$row;
               if($estPaire==1)
                  $tr_class="row_even";
               else
                  $tr_class="row_odd";
            }
            $estPaire=$estPaire=1-$estPaire;
            $str.= "   <TR class='$tr_class'><th class='spip'>$col1</th>";
         }

         $sql="";
         $col_dept=0;
         for ($c=0; $c < $num; $c++) {
            if($tout_afficher)
               $valeur=trim($data[$c]);
            else
               $valeur=unicode2charset(charset2unicode(trim($data[$c])));
            if($c>MAX_COLONNES_AFFICHEES-1 && !$tout_afficher)
               $flag_col_depasse=true;
            elseif(!$flag_lignes_depasse)
               $str.= "      <$td class='spip'>$valeur</$td>\n";
            if($row==0) {
               $type="row_titre";
               $row_titre[$c]=$valeur;
               if($row_titre[$c]==$odb_mapping["departement"])
                  $col_dept=$c;
            } else {
               if($col_dept==0)
                  $col_dept=19; //TODO comprendre pk tjs nul
               $champ=$row_titre[$c];
               //$type=$odb_referentiel[$table][$champ];
               if($type=="")
                  die(KO." - Champ <b>[$champ]</b> inconnu, veuillez v&eacute;rifier la ligne d'en-t&ecirc;tes de votre fichier <b>csv</b><br/><i>Note : sans doutes avez-vous une ancienne version de fichier d'import, veuillez vous r&eacute;f&eacute;rer aux <A HREF='".generer_url_ecrire('odb_ref')."&fichier_type=$table'>fichiers types</A></i>\n");
               $txt_debug.="[$row:$c] $champ=$type<br/>\n";
               //echo "val $valeur - type $type - annee $annee - dept ".$data[$col_dept]." - n° ".$data[0];
               //$ref=(substr_count($odb_referentiel[$table][$champ],'ref')>0)?substr($odb_referentiel[$table][$champ],3):$champ;
               if(strlen($aValRef[$champ][$valeur])>0) {
                  // on avait déjà récupéré cette valeur qui est bonne
                  //echo "==&gt; [OK] $champ $valeur<br/>";
                  $valeur=$aValRef[$champ][$valeur];
               } elseif($aErreurs[$champ][$valeur]) {
                  // on avait déjà récupéré cette valeur qui est mauvaise
                  //echo "==&gt; [KO] $champ $valeur<br/>";
                  $fatal.=KO
                       ." - Candidat #<b>".$data[0]."</b> - Erreur d&eacute;j&agrave; rencontr&eacute;e : <b>$champ</b>[<A HREF='#"
                       .$champ.'_'.str_replace(' ','_',$valeur)."'>$valeur</A>]<br/>\n"
                       ;
               } else { // c'est la premiere fois qu'on tombe sur cette valeur : on va lui appliquer les RG.
                  if(trim($data[0])=="") die (KO." - Votre fichier semble corrompu, veuillez v&eacute;rifier les lignes de fin de fichier <b>dans un &eacute;diteur de texte</b> (comme <a href='http://www.flos-freeware.ch/'>Notepad2</a>...)");
                  $retour=reglesGestion($table,$valeur,$champ,$data[$col_dept],$data[0]);
                  $oldValeur=$valeur;
                  $valeur=$retour["valeur"];
                  if(strlen($retour['fatal'])>0) {
                     // la valeur recuperee est mauvaise
                     $aErreurs[$champ][$valeur]=true;
                     $fatal.="<A NAME='$champ".'_'.str_replace(' ','_',$valeur)."'></A>"
                             . $retour["fatal"]
                             ;
                  } else // la valeur recuperee est bonne
                     $aValRef[$champ][$oldValeur]=$valeur;
                  $txt_debug.=$retour["txt_debug"];
               }
            }

            //echo "VALEUR $valeur TYPE $type DEPT".$data[$col_dept];
            $sql.=$valeur;
            if ($c<$num-1)
               $sql.=","; //virgule de fin pour requete sql
         }

         if($row>=$ligneDebutImport) {
            $tab_csv["sql"][$row].=$sql;
         }
         elseif($row==0) {
            $tab_csv["sql"]["titre"]=$sql;
         }
         // affichage des "..." pour colonnes masqu&eacute;es
         if($flag_col_depasse) {
            if(!$flag_lignes_depasse) {
               $str.= "      <$td class='spip'>...</$td>\n";
               $str.= "   </TR>\n";
            }
         }
         // affichage requete sql si debug
         if($debug) {
            if($estPaire==1)
               $bgcolor="#BBDDFF";
            else
               $bgcolor="white";
            $tr_debug.= "   <TR>\n      <TD COLSPAN='$num' bgcolor='$bgcolor'>$sql</TD>\n</TR>\n";
         }
      }
      if($flag_lignes_depasse) {
         $str.="<TR class='row_first'>\n";
         $str.="<TH COLSPAN='$num' align='center'>...Affichage limit&eacute; &agrave; ".MAX_LIGNES_AFFICHEES." lignes sur $row...</TH>";
      }

      $str.= "</TABLE>\n";
      if($flag_col_depass)
         $str.="Colonnes : ".MAX_COLONNES_AFFICHEES."/".$num." - ";
      if($flag_lignes_depass)
         $str.="Lignes : ".MAX_LIGNES_AFFICHEES."/".($row)." - ";
      if($flag_col_depasse || $flag_lignes_depasse)
         $str.="<A HREF='"._DIR_PLUGIN_ODB_IMPORT."/exec/odb_affiche_csv.php?fic=$nom_fichier' TARGET='_BLANK'>Voir la table compl&egrave;te</A>\n";
      $str.="<BR/><BR/>\n";
      fclose($handle);
      $txt_debug.="<TABLE class='spip'>\n\t$tr_debug</TABLE>\n";
   } else {
      $txt_debug.="Fichier [$fichier] introuvable";
      $str=KO." - Fichier <I>$fichier</I> introuvable<BR/>\n";
   }
   $tab_csv["html"]=$str;
   $tab_csv["fatal"]=$fatal;
   return $tab_csv;// 14:05 12/02/2007 html et sql
}

/**
 * reprise de l'import de fichier (process de verification apres upload)
 * 
 * @param string $uploaddir : chemin du fichier CSV existant
 * @param string $table : table MySQL dans laquelle faire l'import
 * @param int $annee : annee 
 * @return string : resultats de l'import (succes / echec) - affiche cause(s) echec le cas echeant 
 */
function reprise_import($uploaddir,$table,$annee) {
   global $debug,$txt_debug;
   $cpt_csv=0;
   if ($handle = opendir($uploaddir)) {
      /* Ceci est la façon correcte de traverser un dossier. */
      while (false !== ($fichier = readdir($handle))) {
         $ext=detecte_extension($fichier);
         if($debug && trim($ext!=""))
            $txt_debug.= "<br/>".vignette($ext)."$fichier - ".round(filesize("$uploaddir/$fichier")/1024,1)."Ko<br/>\n";
         if($ext==csv) {
            $tab_csv = affiche_csv($uploaddir.$fichier,$table,$annee);
            $str.= $tab_csv["html"];
            $tab_sql[$fichier]=$tab_csv["sql"];
            if(strlen($tab_csv["fatal"])>0) {
               $str.='<small>'.$tab_csv["fatal"]."</small><hr height='1'/>\n";
               $disabled="disabled";
               $import_impossible="<b>impossible</b> : erreurs d&eacute;tect&eacute;es";
            } else {
               $disabled='';
               $import_impossible='';
            }
            $cpt_csv++;
         }
      }

      closedir($handle);
   } else
      die(KO." - Impossible d'ouvrir le r&eacute;pertoire $uploaddir");
   if($cpt_csv>0) {
      if($cpt_csv==1)
         $txt="Import du fichier";
      elseif($cpt_csv>1)
         $txt="Import des <b>$cpt_csv</b> fichiers";
      $str .= "<form id='form_step2' enctype='multipart/form-data' action='".generer_url_ecrire('odb_import')."' method='post' class='forml spip_xx-small'>\n";
      $str.="$txt <b>csv</b> de l'archive $import_impossible \n";
      $str.="\t<INPUT TYPE='submit' NAME='step3' VALUE='Importer' class='fondo' $disabled/>\n";
      $cpt_fic=0;
      foreach($tab_sql as $fic => $sql_tab) {
         $cpt_fic++;
         $str.= "\t<INPUT TYPE='hidden' NAME='csvfile|".$cpt_fic."[]' VALUE=\"$fic\"/>\n";
         foreach($sql_tab as $key => $sql_ligne)
            $str.="\t<INPUT TYPE='hidden' NAME='csvfile|".$cpt_fic."[]' VALUE=\"$sql_ligne\"/>\n";
      }
      $str.="</form>\n";
   } else
      $str.=KO.' - Aucun fichier <b>csv</b> détecté dans l\'archive. <br/><small>Êtes-vous sûr(e) que l\'archive contient bien un fichier <b>csv</b> et pas un fichier <b>xls</b> par exemple ?</small>';

   return $str;
}
?>
