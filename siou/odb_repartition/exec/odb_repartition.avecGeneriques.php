<?php
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN.'inc-referentiel.php');
include_once(DIR_ODB_COMMUN.'inc-odb.php');
include_once(_DIR_PLUGIN_ODB_REPARTITION.'exec/inc-traitements.php');

define('MAX_LIGNES',500); //nb de lignes affichées max

setlocale(LC_TIME, "fr_FR");

global $debug, $txt_gauche, $txt_debug;
$debug=false;

$txt_gauche="";// texte boite de gauche
$txt_="";// texte boite de droite
$txt_debug=""; // texte debug
define('OK',"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define('KO',"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

/** Affiche la capacite de chaque centre
 * @param string $annee : annee
 * @param int $idDepartement : id_departement (filtre facultatif)
 * @return string : affichage
 */
function capaciteCentres($annee, $idDepartement=0) {
   global $tab_referentiel;
   if($idDepartement>0)
      $whereDept="AND eta.id_departement=$idDepartement";
   $sql = 'SELECT * , eta . id_ville , capacite, nb_salles, nb_salles * capacite capacite_type , nb_salles * capacite - nb_repartis dispo '
        . ' FROM odb_ref_etablissement eta , odb_ref_salle salle '
        . ' LEFT JOIN ( '
        . ' SELECT id_salle , count( * ) nb_repartis '
        . ' FROM odb_repartition '
        . " WHERE annee = $annee "
        . ' GROUP BY id_salle '
        . ' ) rep ON salle . id = rep . id_salle '
        . ' WHERE eta . id = salle . id_etablissement '
        . " $whereDept"
        . ' ORDER BY id_ville , id_etablissement , salle'
        ;
        //echo $sql;
   $result=odb_query($sql,__FILE__,__LINE__);
   $nb_rows=mysql_num_rows($result);
   while($row=mysql_fetch_array($result)) {
      $id_departement=(int)$row['id_departement'];
      $departement=$tab_referentiel['departement'][$id_departement];
      $id_salle = $row['id'];
      $etablissement=$row['etablissement'];
      $id_ville=$row['id_ville'];
      $ville=$tab_referentiel['ville'][$id_departement][$id_ville];
      $salle=$row['salle'];
      if($row['dispo']=='') $row['dispo']=$row['capacite_type'];
      $champs=array('salle','nb_salles','capacite','nb_repartis','capacite_type','dispo');
      foreach($champs as $champ)
         $tab_repart[$departement][$ville][$etablissement][$salle][$champ]=$row[$champ];
   }


   $str= "<table width='100%'>\n<tr>\n<th><small>".$cpt_bidon++."</small></th><th><small>Dept</small></th><th><small>Ville</small></th><th><small>Centre</small></th><th><small>Salle</small></th><th><small>Capacit&eacute;</small></th><th><small>Dispo</small></th><th><small>&Agrave; r&eacute;partir<br/>dans salle en cours</small></th></tr>\n";
   ksort($tab_repart);
   foreach($tab_repart as $dept=>$tab) {
      foreach($tab as $ville=>$tab1)
         foreach($tab1 as $centre=>$tab2) {
            foreach($tab2 as $salle=>$tab3) {
            	foreach(array('dispo','capacite','nb_salles','capacite_type') as $col)
            		$$col=$tab3[$col];
               if($capacite_type!=$dispo) $dispo_aff="<span style='color:rgb(".round((1-$dispo/$capacite_type)*255,0).",0,0);'><b>$dispo</b></span>";
               else $dispo_aff=$dispo;
               if($dept!=$old_dept) $departement="<b>$dept</b>";
               else $departement=$dept;
               if($ville!=$old_ville) $ville_="<b>$ville</b>";
               else $ville_=$ville;
               $salleEnCours=$capacite-(($capacite_type-$dispo)%$capacite);
               $str.= "<tr class='tr_liste'><td><small>".$cpt_bidon++."</small></td><td><small>$departement</small></td><td><small>$ville_</small></td><td><small>$centre</small></td><td title='$nb_salles salles $salle : $capacite places'><small>$salle ($capacite)</small></td>"
                  . "<td><small>$capacite_type</small></td><td><small>$dispo_aff</small></td><td><small>$salleEnCours</small></td></tr>\n"
                  ;
               $old_dept=$dept;
               $old_ville=$ville;
            }
         }
   }
   $str.= "</table>\n";
   return $str;
}

/** Repartition des candidats ODB
 * 
 * @author <a href='mailto:cedric [at] protiere [dot] com'>Cedric PROTIERE</a>
 * @version 1.1
 */
function exec_odb_repartition() {
global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping;

include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");
$annee=date("Y");

$array_ref=array('departement','etablissement','ef','lv','eps','prefixe','serie','sexe','ville','pays');
foreach($array_ref as $ref)
   $tab_referentiel[$ref]=getReferentiel($ref,'tout');

debut_page(_T('R&eacute;partition des candidats'), "", "");
echo "<br /><br />";
gros_titre(_T('Office Du Baccalaur&eacute;at'));
$tab_auteur=$GLOBALS["auteur_session"];

if($tab_auteur['statut']!="0minirezo") {
   $isAdmin=true;

   $etab=$tab_auteur['nom_site'];

   foreach($tab_referentiel['etablissement'] as $key => $val)
      if($val==$etab) {
         $tab_auteur['id_etablissement']=$key;
      }
   if ($debug) echo "etablissement $etab (".$tab_auteur['id_etablissement'].")<br/>\n";
} else
   $isAdmin=true;

if ($debug) {
   echo "<A HREF='#fin_debug'>Sauter les infos de debug</A>\n";
   /*echo "<hr/>Auteur<pre style='text-align:left;'>";
   print_r($tab_auteur);
   echo "</pre><hr/>";*/
   echo "_REQUEST<pre style='text-align:left;'>";
   print_r($_REQUEST);
   echo "</pre><hr/>";
   /*echo "tab_referentiel<pre style='text-align:left;'>";
   print_r($tab_referentiel);
   echo "</pre><hr/>";*/
   echo "<A NAME='fin_debug'></A>\n";
}

debut_cadre_relief( "", false, "", $titre = _T('R&eacute;partition des candidats'));
//debut_boite_info();
echo '<br>';

$REFERER=$_SERVER['HTTP_REFERER'];
$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];

echo "<IMG SRC='"._DIR_PLUGIN_ODB_REPARTITION."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";
isAutorise(array('Admin'));

$etablissement_txt=$_REQUEST['etablissement_txt'];
if(strlen($_POST['ok_particulier'])>0) {
//////////////////////////////////////////////// validation du formulaire : cas particulier
$annee=$_REQUEST['annee'];
$nombre=$_REQUEST['nombre'];
$envoi_centre=$_REQUEST['envoi_centre'];
$nombre=$_REQUEST['nombre'];
if($nombre=='*') $nombre='0';

foreach($_POST as $key=>$val)
if(substr_count($key,'repart_')>0) {
   $$key=$val;
   $colonne=substr($key,strlen('repart_'));
   //echo "$key=$val ($colonne)<br/>\n";
   if($val>0) $par[$colonne]=$val;
}

$repartition=repartirCandidats($par,'',$envoi_centre,$etablissement_txt,$annee,$nombre);
$nb_rows=$repartition['nb_rows'];
/*
debut_gauche();
   debut_boite_info();
      echo "<p>R&eacute;partition de <b>$nb_rows</b> candidats</p>\n"
         ;
   fin_boite_info();
   odb_raccourcis('');
creer_colonne_droite();
debut_droite();
*/
   debut_cadre_relief("", false, "", $titre = _T("R&eacute;partition de $nb_rows candidats $annee dans le $etablissement_txt"));
      debut_boite_info();
      echo $repartition['msg_info'];
      fin_boite_info();
      echo "<br/>\n";
      echo $repartition['msg_repartition'];
   fin_cadre_relief();
/*
debut_boite_info();
   echo capaciteCentres($annee, $repart_departement);
fin_boite_info();
*/
$inputSerie="<SELECT NAME='repart_serie' class='forml'>".formOptionsRefInSelect('serie',$repart_serie)."</SELECT>\n";
$inputDepartement="<SELECT NAME='repart_departement' class='forml'>".formOptionsRefInSelect('departement',$repart_departement)."</SELECT>\n";
$inputCentre="<SELECT NAME='envoi_centre' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$envoi_centre,'',$idDept)."</SELECT>\n";
} elseif(strlen($_POST['ok_generique'])>0 || strlen($_REQUEST['from_ville']>0) || strlen($_REQUEST['from_etablissement']>0)) {
//////////////////////////////////////////////// validation du formulaire : cas generique
$annee=$_REQUEST['annee'];
$from_ville=$_REQUEST['from_ville'];
$from_serie=$_REQUEST['from_serie'];
$from_etablissement=$_REQUEST['from_etablissement'];
$envoi_centre=$_REQUEST['envoi_centre'];
$etablissement_txt=$_REQUEST['etablissement_txt'];
$envoi_nombre=$_REQUEST['envoi_nombre'];
$repartirIcon="<img src='"._DIR_PLUGIN_ODB_REPARTITION."img_pack/repartir.png' alt='R&eacute;partir' align='absmiddle'>\n";

if($from_etablissement>0 || $from_ville>0) {
   // affectation manuelle
   if($from_serie>0) $from['ville']=$from_ville;
   if($from_serie>0) $from['serie']=$from_serie;
   if($from_etablissement>0) $from['etablissement']=$from_etablissement;
   $repartition=repartirCandidats($from,'',$envoi_centre,$etablissement_txt,$annee,$envoi_nombre);
   $msg_info=$repartition['msg_repartition'];
   $msg_info.="<hr size=1/>\n".$repartition['msg_info'];
}
$tpsDebut=time();
$sql_centres="select eta.id id_etablissement, eta.etablissement, eta.id_ville, ville_eta.ville ville_eta, eta.id_centre, centre.etablissement, centre.id_ville id_ville_centre, ville_centre.ville ville_centre, eta.id_departement, departement.departement, eta.annee_centre"
            ." from odb_ref_etablissement eta, odb_ref_ville ville_eta, odb_ref_ville ville_centre, odb_ref_etablissement centre, odb_ref_departement departement"
            ." where eta.id_centre = centre.id"
            ." and eta.id_ville=ville_eta.id"
            ." and centre.id_ville=ville_centre.id"
            ." and eta.id_departement=departement.id"
            ." order by departement, ville_centre"
            ;

$sql_synthese = 'select centre.id_departement, departement.departement, count(*) nb_eta,'
        . ' eta.id_centre, centre.etablissement centre, centre.id_ville id_ville_centre, ville_centre.ville ville_centre'
        . ' from odb_ref_etablissement eta, odb_ref_ville ville_eta, odb_ref_ville ville_centre,'
        . ' odb_ref_etablissement centre, odb_ref_departement departement'
        . ' where eta.id_centre = centre.id'
        . ' and eta.id_ville=ville_eta.id'
        . ' and centre.id_ville=ville_centre.id'
        . ' and eta.id_departement=departement.id'
        . ' group by centre'
        . ' order by departement, nb_eta desc, ville_centre'
        ;
        
$sql_plusieurs_eta_dans_meme_ville='SELECT eta.id_departement, departement, id_ville, ville, count( * ) nb_eta'
        . ' FROM odb_ref_etablissement eta, odb_ref_ville vil, odb_ref_departement dep'
        . ' WHERE annee_centre >0'
        . ' AND eta.id_departement = dep.id'
        . ' AND eta.id_ville = vil.id'
        . ' GROUP BY ville'
        . ' ORDER BY departement, nb_eta DESC , ville'
        ;
        //echo $sql_plusieurs_eta_dans_meme_ville;
$result=mysql_query($sql_plusieurs_eta_dans_meme_ville) or die (KO." - Erreur dans la requete <pre>$sql_plusieurs_eta_dans_meme_ville</pre>\n".mysql_error());
$num_rows=mysql_num_rows($result);
$colonnes=array('id_departement','departement','nb_eta','id_ville','ville');
while($row=mysql_fetch_array($result)) {
   foreach($colonnes as $col) {
      $$col=$row[$col];
   }
   foreach(array('id_departement','nb_eta','id_ville') as $col) {
      //if($nb_eta>1)
         $tab_synthese_nEta[$departement][$ville][$col]=$$col;
      //elseif($nb_eta==1)
      // $tab_synthese_1Eta[$departement][$ville][$col]=$$col;
   }
}
$sql_aucun_centre_dans_cette_ville=
          'select eta.id_departement, dep.departement, id_ville, vil.ville, count(*) nb_eta'
        . ' from odb_ref_etablissement eta, odb_ref_departement dep, odb_ref_ville vil'
        . ' where eta.id_departement=dep.id and eta.id_ville=vil.id'
        . ' and id_ville not in'
        . ' (select distinct id_ville from odb_ref_etablissement where annee_centre>0 order by id_ville)'
        . ' group by departement, ville'
        . ' order by departement, nb_eta desc, ville'
        ;
//echo $sql_aucun_centre_dans_cette_ville;
$result=mysql_query($sql_aucun_centre_dans_cette_ville) or die (KO." - Erreur dans la requete <pre>$sql_aucun_centre_dans_cette_ville</pre>\n".mysql_error());
$num_rows=mysql_num_rows($result);
$colonnes=array('id_departement','departement','nb_eta','id_ville','ville');
while($row=mysql_fetch_array($result)) {
   foreach($colonnes as $col) {
      $$col=$row[$col];
   }
   foreach(array('id_departement','nb_eta','id_ville') as $col) {
      $tab_synthese_aucun_centre[$departement][$ville][$col]=$$col;
   }
}
//print_r($tab_synthese_aucun_centre);
//////////// cas 1 : villes contenant plusieurs centres
// on récupère le tableau des séries pour connaitre celle qui a le plus de candidats au sein de chaque ville
$sql = 'SELECT departement, can.ville id_ville, vil.ville, can.serie id_serie, ser.serie, count( * ) nbCan'
     . ' FROM odb_candidats can, odb_ref_serie ser, odb_ref_ville vil'
     . " WHERE id_table = '0'"
     . " AND annee = $annee"
     . ' AND ser.id = can.serie'
     . ' and can.ville=vil.id'
     . ' GROUP BY departement, vil.ville, serie'
     . ' ORDER BY departement, vil.ville, nbCan DESC'
     ;
//echo "series : $sql<hr>";
$result=odb_query($sql,__FILE__,__LINE__);
$num_rows=mysql_num_rows($result);
while($row=mysql_fetch_array($result)) {
   $departement=$tab_referentiel['departement'][$row['departement']];
   $id_ville=$row['id_ville'];
   $ville=$row['ville'];
   $serie=$row['serie'];
   $id_serie=$row['id_serie'];
   $nbCan=(int)$row['nbCan'];
   if($nbCan>0) {
      $tab_serie[$departement][$ville][$serie]['id_serie']=$id_serie;
      $tab_serie[$departement][$ville][$serie]['nbCan']=$nbCan;
   } 
}
mysql_free_result($result);
// on récupère le tableau des disponibilités dans chaque centre - cf odb_stats_siou
$sql = 'SELECT * , eta . id_ville , nb_salles * capacite capacite_type , nb_salles * capacite - nb_repartis dispo '
     . ' FROM odb_ref_etablissement eta , odb_ref_salle salle '
     . ' LEFT JOIN ( '
     . '  SELECT id_salle , count( * ) nb_repartis '
     . '  FROM odb_repartition '
     . "  WHERE annee = $annee "
     . '  GROUP BY id_salle '
     . ' ) rep ON salle . id = rep . id_salle '
     . ' WHERE eta . id = salle . id_etablissement '
     . ' ORDER BY id_ville , id_etablissement , salle'
     ;
     //echo $sql;
$result=odb_query($sql,__FILE__,__LINE__);
$nb_rows=mysql_num_rows($result);
while($row=mysql_fetch_array($result)) {
   $id_departement=(int)$row['id_departement'];
   $departement=$tab_referentiel['departement'][$id_departement];
   $id_salle = $row['id'];
   $etablissement=$row['etablissement'];
   $id_ville=$row['id_ville'];
   $ville=$tab_referentiel['ville'][$id_departement][$id_ville];
   $salle=$row['salle'];
   if($row['dispo']=='') $row['dispo']=$row['capacite_type'];
   //echo "<hr>$id_salle - $id_etablissement - $departement $id_dept - $etablissement $id_etablissement";
   $champs=array('salle','nb_salles','capacite','nb_repartis','capacite_type','dispo');
   foreach($champs as $champ)
      $tab_repart[$departement][$ville][$etablissement][$salle][$champ]=$row[$champ];
   /*echo "<hr>$etablissement $salle<pre>";
   print_r($tab_repart[$departement][$ville][$etablissement][$salle]);
   echo "</pre>\n";*/
}
$sql_rep_eta_centre=
         'SELECT eta.id_departement, dep.departement, eta.id_ville, ville.ville, eta.id id_etablissement, eta.etablissement,'
        . ' eta.id_centre, centre.etablissement centre, vilcentre.ville ville_centre,'
        . ' count(*) nb_candidats'
        . ' FROM odb_ref_etablissement eta, odb_ref_departement dep, odb_ref_ville ville,'
        . ' odb_candidats can, odb_ref_etablissement centre, odb_ref_ville vilcentre'
        . ' WHERE can.etablissement=eta.id'
        . ' and centre.id=eta.id_centre'
        . ' and vilcentre.id = centre.id_ville'
        . ' and eta.id_departement = dep.id'
        . ' AND eta.id_ville = ville.id'
        . ' AND eta.annee_centre =0'
        . " AND can.annee=$annee"
        . " AND can.id_table='0'"
        . ' GROUP BY can.etablissement'
        . ' ORDER BY departement, ville, etablissement'
        ;
//echo "sql_rep_eta_centre $sql_rep_eta_centre<hr>";
$result=mysql_query($sql_rep_eta_centre) or die (KO." - Erreur dans la requete <pre>$sql_rep_eta_centre</pre><br/>".mysql_error());
$nb_rows=mysql_num_rows($result);
$colonnes=array('id_departement', 'departement', 'id_ville', 'ville', 'id_etablissement', 'etablissement', 'id_centre', 'centre','id_ville_centre','ville_centre','nb_candidats');
while($row=mysql_fetch_array($result)) {
   foreach($colonnes as $col)
      $$col=$row[$col];
   foreach(array('id_departement','id_ville','id_etablissement','centre','id_centre','id_ville_centre','ville_centre','nb_candidats') as $col)
      $tab_repart_eta_centre[$departement][$ville][$etablissement][$col]=$$col;
}

if($debug) {
   echo "<table>\n<tr>\n<th><small>".$cpt_bidon++."</small></th><th><small>Dept</small></th><th><small>Ville</small></th><th><small>Centre</small></th><th><small>Salle</small></th><th><small>Capacit&eacute;</small></th><th><small>Dispo</small></th></tr>\n";
   ksort($tab_repart);
   foreach($tab_repart as $dept=>$tab) {
      foreach($tab as $ville=>$tab1)
         foreach($tab1 as $centre=>$tab2) {
            foreach($tab2 as $salle=>$tab3) {
               $dispo=$tab3['dispo'];
               $capacite_type=$tab3['capacite_type'];
               if($capacite_type!=$dispo) $dispo="<span style='color:rgb(".round((1-$dispo/$capacite_type)*255,0).",0,0);'><b>$dispo</b></span>";
               if($dept!=$old_dept) $departement="<b>$dept</b>";
               else $departement=$dept;
               if($ville!=$old_ville) $ville_="<b>$ville</b>";
               else $ville_=$ville;
               echo "<tr class='tr_liste'><td><small>".$cpt_bidon++."</small></td><td><small>$departement</small></td><td><small>$ville_</small></td><td><small>$centre</small></td><td><small>$salle</small></td>"
                  . "<td><small>$capacite_type</small></td><td><small>$dispo</small></td></tr>\n"
                  ;
               $old_dept=$dept;
               $old_ville=$ville;
            }
         }
   }
   echo "</table>\n";
}
$tab_dispos=array();
// on attribue les numeros de table
if($msg_info=='') {
   $msg_info.="<ul>\n<li><A HREF='#villes1centre'>Villes comportant au moins un centre de composition</A></li>\n";
   $msg_info.="<li><A HREF='#villes0centre'>Villes ne comportant aucun centre de composition</A></li>\n</ul>\n";
}
$msg.="<a name='villes1centre'></A>\n<h1>Villes comportant au moins un centre de composition</h1>";
foreach($tab_synthese_nEta as $departement=>$tab1) {
   $msg.="<h2>$departement</h2>\n";
   $id_departement=$tab_referentiel['departement'][$departement];
   foreach($tab1 as $ville => $tab_ville) {
      $nbEta=$tab_ville['nb_eta'];
      $id_ville=$tab_ville['id_ville'];
      $msg.="<a name='Ville_$id_ville'></a><h3>$ville ($nbEta &eacute;tablissement$pluriel)</h3>";
      $dispoTotal=0;
      $id_departement=$tab_ville['id_departement'];
      if($nbEta>1) $pluriel='s'; else $pluriel='';
      $msg.= "<small>$departement (#$id_departement) - $ville (#$id_ville)</small>"
           . "<table width='100%' style='border: 1px solid gray;'>\n<tr>\n<th>&Eacute;tablissements</th><th>Candidats</th></tr>\n<tr>\n";
      $msg.="<td valign=top style='border: 1px dotted lightgray;'><table width='100%'>\n<tr>\n\t"
          . "<th><small>Centre</small></th>\n\t<th><small>R&eacute;partis</small></th>\n\t<th><small>Dispo</small></th>\n\t<th><small>Dispo<br/>Centre</small></th>\n</tr>\n";
      if(is_array($tab_repart[$departement][$ville]))
         foreach($tab_repart[$departement][$ville] as $centre=>$tab_centre) {
            $dispoCentre=0;$cptTypesDansCentre=0;
            foreach($tab_centre as $salle => $tab_tmp) {
               $dispoCentre+=(int)$tab_tmp['dispo'];
               $cptTypesDansCentre++;
            }
            foreach($tab_centre as $salle => $tab_tmp) {
               $dispo=$tab_tmp['dispo'];
               $nb_repartis=$tab_tmp['nb_repartis'];
               $capacite_type=$tab_tmp['capacite_type'];
               if($capacite_type!=$dispo) {
                  $dispoAff="<b>$dispo</b>";
                  $nb=round((1-$dispo/$capacite_type)*100,0);
                  $style="style='color:white; background-color:rgb(".($nb+128).",".(128-$nb).",".(128-$nb).");'";
               }
               else {
                  $dispoAff=$dispo;
                  $style="";
               }
               $msg.="<tr class='tr_liste'>\n\t"
                   . "<td><small>$centre <small>[$salle]</small></small></td>\n\t"
                   . "<td><small>$nb_repartis</small></td>\n\t"
                   . "<td $style><small>$dispoAff</small></td>\n\t"
                   ;
               if($centre!=$old_centre) {
                  if($cptTypesDansCentre==1) $style="style='color:#aaa;'";
                  else $style="style='font-weight:bold;'";
                  $msg.="<td $style rowspan=$cptTypesDansCentre><small>$dispoCentre</small></td>\n";
               }
               $msg.="</tr>\n";
               $dispoTotal+=(int)$dispo;
               $old_centre=$centre;
               $tab_dispos['centre'][$ville][$centre]=$dispoCentre;
            }
         }
      $tab_dispos['ville'][$ville]=$dispoTotal;
      $msg.="</table><hr size=1/>= <b>$dispoTotal places disponibles</b></td>"
          . "<td valign=top style='border: 1px dotted lightgray;'>\n<table width='100%'>\n"
          . "<tr>\n\t<th><small>S&eacute;rie</small></th><th><small>Candidats</small></th>\n</tr>\n"
          ;
      $totalCanVille=0;
      $cptVilleSerie=0;
      if(is_array($tab_serie[$departement][$ville])) {
         foreach($tab_serie[$departement][$ville] as $serie=>$tab_ser) {
            $cptVilleSerie++;
            $id_serie=$tab_ser['id_serie'];
            $nbCan=$tab_ser['nbCan'];
            $msg.="<tr class='tr_liste'>\n<td><small><b>$serie</b> (#$id_serie)</small></td><td><small>";
            //echo "<small>$departement $ville $serie $nbCan<br></small>";
            if($cptVilleSerie==1) {
               $isChoix=false;
               $old_dispo=0;$dispoMax=0;
               if(is_array($tab_dispos['centre'][$ville]))
               foreach($tab_dispos['centre'][$ville] as $centre=>$dispo) {
                  if($dispo>$dispoMax) {
                     $dispoMax=$dispo;
                     $dispoMaxCentre=$centre;
                  }
                  $old_dispo=$dispo;
               }
               $old_dispo=$dispoMax;
               if(is_array($tab_dispos['centre'][$ville]))
                  foreach($tab_dispos['centre'][$ville] as $centre=>$dispo) {
                     if($nbCan<=$dispo && $dispo<=$old_dispo) {
                        $dispoChoix=$dispo;
                        $dispoChoixCentre=$centre;
                        $isChoix=true;
                     }
                     if($old_dispo<$dispo) $old_dispo=$dispo;
                  }
               if($isChoix) {
                  // cas idéal : on a trouvé le plus petit centre capable d'accueillir les nbCan candidats
                  // il faut vérifier si chaque salle a suffisamment de place
                  $dispoSalleOld=$dispoChoix;
                  $dispoSalleMax=0;
                  $is1salleSuffit=false;
                  foreach($tab_repart[$departement][$ville][$dispoChoixCentre] as $salle=>$tab) {
                     $dispoSalle=$tab['dispo'];
                     if($dispoSalle>$dispoSalleMax) {
                        $dispoSalleMax=$dispoSalle;
                     }
                     if($nbCan<=$dispoSalle && $dispoSalle<=$dispoSalleOld) {
                        $is1salleSuffit=true;
                     }
                  }
                  if($is1salleSuffit) $nbEnvoi=$nbCan;
                  else $nbEnvoi=$dispoSalleMax;
                  $envoi_centre=$dispoChoixCentre;
                  $id_centre=$tab_referentiel['etablissement'][$id_departement][$envoi_centre];
               } else {
                  // cas où pas une salle ne convient : on en envoit le maximum dans la plus grande salle
                  $is1salleSuffit=false;
                  $dispoSalleMax=0;
                  $dispoSalleOld=$dispoMax;
                  if(is_array($tab_repart[$departement][$ville][$dispoMaxCentre])) {
                     foreach($tab_repart[$departement][$ville][$dispoMaxCentre] as $salle=>$tab) {
                        $dispoSalle=$tab['dispo'];
                        if($dispoSalle>$dispoSalleMax) {
                           $dispoSalleMax=$dispoSalle;
                        }
                        if($nbCan<=$dispoSalle && $dispoSalle<=$dispoSalleOld) {
                           $is1salleSuffit=true;
                        }
                     }
                  }
                  if($is1salleSuffit) $nbEnvoi=$nbCan; // ne devrait pas arriver sinon devrait etre dispoChoix
                  else $nbEnvoi=$dispoSalleMax;
                  //$nbEnvoi=$dispoMax;
                  $envoi_centre=$dispoMaxCentre;
                  $id_centre=$tab_referentiel['etablissement'][$id_departement][$envoi_centre];
               }

               $msg.= "$nbCan <a href=\"javascript:document.forms['form_repartition'].from_ville.value=$id_ville;\n"
                   . "document.forms['form_repartition'].from_serie.value=$id_serie;\n"
                   . "document.forms['form_repartition'].from_etablissement.value=0;\n"
                   . "document.forms['form_repartition'].envoi_centre.value=$id_centre;\n"
                   . "document.forms['form_repartition'].etablissement_txt.value='$envoi_centre';\n"
                   . "document.forms['form_repartition'].envoi_nombre.value=$nbEnvoi;\n"
                   . "document.forms['form_repartition'].ok_generique.value='manuel';\n"
                   . "document.forms['form_repartition'].action+='#Ville_$id_ville';\n"
                   . "document.forms['form_repartition'].submit();\n"
                   . "\" title='R&eacute;partir $nbEnvoi candidats de s&eacute;rie $serie &agrave; $envoi_centre ($ville)'>$repartirIcon</a>"
                   ;
            } else $msg.=$nbCan;
            $msg.="</small></td>\n</tr>\n";

            $totalCanVille+=(int)$nbCan;
         }
      }
      $msg.="</table><hr size=1/>= <b>$totalCanVille candidats</b></td></tr>\n</table>\n";
      $nbCanRepartis=0;
      if($totalCanVille>$dispoTotal) {
         $msg.=KO." - impossible de r&eacute;partir $totalCanVille candidats dans une ville capable d'en accueillir seulement $dispoCentre<br/>Veuillez proc&eacute;der &agrave; des r&eacute;partitions par cas particuliers ou ajouter un centre de composition &agrave; $ville";
      }
   }
}
$msg.="<A NAME='villes0centre'></A><h1>Villes n'ayant aucun centre de composition</h1>\n";
$msg.="<form name='form_sans_centre' class='spip_xx-small'>\n";
foreach($tab_synthese_aucun_centre as $departement=>$tab1) {
   $msg.= "<h2>$departement</h2>\n";
   foreach($tab1 as $ville=>$tab2) {
      $id_ville=$tab2['id_ville'];
      $id_departement=$tab2['id_departement'];

      $nbEta=$tab2['nb_eta'];
      if($nbEta>0) {
         if($nbEta>1) $pluriel='s'; else $pluriel='';
         $msg.="<A name='Ville0_$id_ville'></A><h3 title='D&eacute;partement $id_departement - Ville $id_ville'>$ville ($nbEta &eacute;tablissement$pluriel)</h3>\n";
         $msg.="<table width='100%' style='border:1px solid gray;'>\n<tr>\n<td valign='top' style='border:1px dotted lightgray;'>\n";
         $msg.="<table width='100%'>\n<tr>\n";
         foreach(array('D&eacute;partement','&Eacute;tablissement','Centre','Ville','Dispo') as $titre)
            $msg.="<th><small>$titre</small></th>\n";
         $msg.="</tr>\n";
         $nb_candidats=0;
         if(is_array($tab_repart_eta_centre[$departement][$ville])) {
            foreach($tab_repart_eta_centre[$departement][$ville] as $etablissement=>$tab_rep_1) {
               foreach(array('id_centre','centre','id_departement','id_ville','ville_centre','id_etablissement','nb_candidats') as $col) {
                  $$col=$tab_rep_1[$col];
               }
               $dispoCentre=$tab_dispos['centre'][$ville_centre][$centre];
               $nbEnvoi=min((int)$nb_candidats,(int)$dispoCentre);
               $repartir="<a href=\"javascript:document.forms['form_repartition'].from_ville.value=0;\n"
                   . "document.forms['form_repartition'].from_etablissement.value=$id_etablissement;\n"
                   . "document.forms['form_repartition'].from_serie.value=0;\n"
                   . "document.forms['form_repartition'].envoi_centre.value=$id_centre;\n"
                   . "document.forms['form_repartition'].etablissement_txt.value='$centre';\n"
                   . "document.forms['form_repartition'].ok_generique.value='manuel';\n"
                   . "document.forms['form_repartition'].envoi_nombre.value=$nbEnvoi;\n"
                   . "document.forms['form_repartition'].action+='#Ville0_$id_ville';\n"
                   . "document.forms['form_repartition'].submit();\n"
                   . "\" title='R&eacute;partir $nbEnvoi candidats dans $centre'>$repartirIcon</a>"
                   ;
               $lib_etablissement=substr($etablissement, 0, 12).'&#133;';
               $lib_etablissement.=$repartir;
               //$inputCentre="<SELECT NAME='centre_$id_centre' onChange=\"document.forms['form_repartition'].envoi_centre.value=this.value;\" class='fondo'>".formOptionsRefInSelect('centre',$id_centre,'Centre',$id_departement)."</SELECT>\n";
               $lib_centre=substr($centre, 0, 7).'&#133;';
               $msg.="<tr class='tr_liste'><td><small>$departement</small></td><td nowrap><small title='$etablissement (#$id_etablissement) - $nb_candidats candidats'>$lib_etablissement</small></td><td nowrap><small title='$centre (#$id_centre)'>$lib_centre</small></td><td><small>$ville_centre</small></td><td><small>$dispoCentre</small></td></tr>\n";
            }
         }
         $msg.="</table>\n</td>\n<td valign='middle' align='center' style='border:1px dotted lightgray;'>\n";
         $msg.="<small><b>$nb_candidats</b> candidats";
         if($nb_candidats>0) {
            $tmp="<br/><b>$dispoCentre</b> places dispo";
            if($dispoCentre<$nb_candidats)
               $tmp="<font color='red'>$tmp</font>";
            $msg.=$tmp;
         }
         $msg.="</small></td>\n</tr>\n</table>\n";
      }
   }
}
$msg.="</form>\n";
               
debut_gauche();
   debut_boite_info();
      echo "<p>R&eacute;partition des candidats"
         . "<hr size=1>Cas g&eacute;n&eacute;rique</p>\n"
         ;
   fin_boite_info();
   odb_raccourcis('');
creer_colonne_droite();
debut_droite();
   debut_boite_info();
      echo $msg_info;
   fin_boite_info();
   debut_cadre_relief("", false, "", $titre = _T("R&eacute;partition des candidats $annee - cas g&eacute;n&eacute;rique"));
      echo $msg;
   fin_cadre_relief();
$inputCentre="<SELECT NAME='envoi_centre' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$envoi_centre)."</SELECT>\n";
} 
if(strlen($_REQUEST["step3"])>0) {
//////////////////////////////////////////////// step 3 : affichage répartition dépt/série par établissement
$annee=$_REQUEST['annee'];
$idDept=$_REQUEST['idDept'];
$nbCandidats=$_REQUEST['nbCandidats'];
$step3=$_REQUEST['step3'];
$inputHidden="<INPUT type='hidden' name='step3' value='$step3'/>";
$idSerie=$_REQUEST['idSerie'];
$departement=$tab_referentiel['departement'][$idDept];
$serie=$tab_referentiel['serie'][$idSerie];
$inputSerie="<SELECT NAME='repart_serie' class='forml'>".formOptionsRefInSelect('serie',$idSerie,'S&eacute;rie')."</SELECT>\n";
$inputDepartement="<SELECT NAME='repart_departement' class='forml'>".formOptionsRefInSelect('departement',$idDept)."</SELECT>\n";
$inputCentre="<SELECT NAME='envoi_centre' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centre',$envoi_centre,'',$idDept)."</SELECT>\n";
$inputEtablissement="<SELECT NAME='repart_etablissement' class='forml'>".formOptionsRefInSelect('etablissement',$repart_etablissement,'&Eacute;tablissement',$idDept)."</SELECT>\n";
$inputVille="<SELECT NAME='repart_ville' class='forml'>".formOptionsRefInSelect('ville',$repart_ville,'Ville',$idDept)."</SELECT>\n";
   debut_gauche();
      debut_boite_info();
         echo "<p>R&eacute;partition des candidats <b>$departement</b> s&eacute;rie <b>$serie</b> par &eacute;tablissement"
            . "<hr size=1>Liens directs : <ul><li>modifier les <A HREF='".generer_url_ecrire('odb_ref')."&step2=manuel&table=ETA|$idDept|$departement|odb_ref_etablissement'>&eacute;tablissements $departement</A></li>"
            . "<li>Saisir <A HREF='".generer_url_ecrire('odb_saisie')."&filtreSerie=$idSerie&filtreDepartement=$idDept#acces_clic'>ces candidats</A></li>"
            . "</ul></p>\n"
            . "<center style='font-weight:bold;'>"
            . "<A style='font-size:100px;color:#222;' title='R&eacute;partition des $serie' HREF='".generer_url_ecrire('odb_repartition')."&step2=isSerie&idSerie=$idSerie&nbCandidats=&annee=$annee'>"
            . "$serie</A><br/><A style='font-size:30px;color:#555;' title='R&eacute;partition dans $departement' HREF='".generer_url_ecrire('odb_repartition')."&step2=isDept&idDept=$idDept&nbCandidats=&annee=$annee'>"
            . "$departement</A></center>\n"
            ;
      fin_boite_info();
      odb_raccourcis('');
   creer_colonne_droite();
   debut_droite();
      debut_cadre_relief("", false, "", $titre = _T("R&eacute;partition des candidats $annee"));
         $titre="R&eacute;partition ".$tab_referentiel['departement'][$idDept].", s&eacute;rie ".$tab_referentiel['serie'][$idSerie]." ($nbCandidats candidats)";
         $tab[]="<TH>Ville</TH><TH>&Eacute;tablissement</TH><TH><small>Centre<br/>+ proche</small></TH><TH><small>Candidats<br/>&agrave; r&eacute;partir</small></TH><TH>%</TH>";
         $sql="SELECT eta.etablissement, eta.id, eta.id_ville, eta.id_centre, count( * ) nb"
            . " FROM `odb_candidats` can, odb_ref_etablissement eta"
            . " WHERE eta.id = can.etablissement"
            . " AND annee=$annee"
            . " AND serie=$idSerie"
            . " AND departement=$idDept"
            . " AND id_table='0'"
            . " GROUP BY eta.etablissement"
            . " ORDER BY id_ville, eta.etablissement"
            ;
            //echo $sql;
         $result=odb_query($sql,__FILE__,__LINE__);
         $num_rows=mysql_num_rows($result);
         $cptVille=0;$old_ville='';$cpt=0;
         while($row=mysql_fetch_array($result)) {
         	$cpt++;
            $etablissement=$row['etablissement'];
            $idEtablissement=$row['id'];
            $idVille=$row['id_ville'];
            $idCentre=$row['id_centre'];
            $nb_rows=$row['nb'];
            $etablissement=$tab_referentiel['etablissement'][$idDept][$idEtablissement];
            $ville=$tab_referentiel['ville'][$idDept][$idVille];
            $centre=$tab_referentiel['etablissement'][$idDept][$idCentre];
            if(!isset($dispoCentre[$idCentre])) {
               //TODO !!!
            }
            if($cpt>$num_rows || ($old_ville!=$ville && $old_ville!='')) {
            	// nouvelle ville : on affiche le total de cette ville et un lien pour repartir candidats de cette ville
            	$old_idVille=$tab_referentiel['ville'][$idDept][$old_ville];
					$tab[]="<TH colspan=2 title='Ville $old_idVille'><small><div>Total $old_ville</div></small></TH>\n\t"
							."<TH colspan=2><small>"
							."<A HREF=\"javascript:document.forms['form_repartition'].nombre.value=$cptVille;"
							. "document.forms['form_repartition'].ok_particulier.disabled=false;"
							. "document.forms['form_repartition'].ok_generique.disabled=true;"
							. "document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces $cptVille num&eacute;ros de table';"
							. "document.forms['form_repartition'].ok_particulier.focus();"
							. "document.forms['form_repartition'].envoi_centre.value=$old_idCentre;"
							. "document.forms['form_repartition'].repart_ville.value=$old_idVille;"
							. "document.forms['form_repartition'].repart_etablissement.value=0;"
							. "document.forms['form_repartition'].nombre.select();\""
							." TITLE='Cliquez pour acc&eacute;der &agrave; leur r&eacute;partition'>$cptVille candidats</A></small>"
							."</TH>\n\t"
	                  ."<TH>".afficheTaux($cptVille/$nbCandidats,2)."</TH>\n"
							;
					$tab[]="<td colspan=5><hr size=0/></td>";
            	$cptVille=$nb_rows;
				} else {
					$cptVille+=$nb_rows;
				}
            foreach(array('etablissement','ville','centre') as $lieu)
               if(trim($$lieu)=="" )
                  $$lieu=ucfirst($lieu).' <small>['.${"id".ucfirst($lieu)}.']</small>';
            $tab[]="<TD><small>$ville</small></TD>\n\t"
            		."<TD><small><div title='&Eacute;ablissement $idEtablissement'>$etablissement</div></small></TD>\n\t"
                  ."<TD><small><div title='Centre $idCentre'>$centre</div></small></TD>\n\t"
                  ."<TD><small>"
                  ."<A HREF=\"javascript:document.forms['form_repartition'].nombre.value=$nb_rows;"
                  . "document.forms['form_repartition'].ok_particulier.disabled=false;"
                  . "document.forms['form_repartition'].ok_generique.disabled=true;"
                  . "document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces $nb_rows num&eacute;ros de table';"
                  . "document.forms['form_repartition'].ok_particulier.focus();"
                  . "document.forms['form_repartition'].envoi_centre.value=$idCentre;"
                  . "document.forms['form_repartition'].repart_etablissement.value=$idEtablissement;"
                  . "document.forms['form_repartition'].nombre.select();\""
                  ." TITLE='Cliquez pour acc&eacute;der &agrave; leur r&eacute;partition'>$nb_rows</A></small></TD>\n\t"
                  ."<TD>".afficheTaux($nb_rows/$nbCandidats,2)."</TD>\n"
                  ;
            $old_ville=$ville;
            $old_idCentre=$idCentre;
         }
			$tab[]="<TH colspan=2 title='Ville $idVille'><small><div>Total $ville</div></small></TH>\n\t"
					."<TH colspan=2><small>"
					."<A HREF=\"javascript:document.forms['form_repartition'].nombre.value=$cptVille;"
					. "document.forms['form_repartition'].ok_particulier.disabled=false;"
					. "document.forms['form_repartition'].ok_generique.disabled=true;"
					. "document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces $cptVille num&eacute;ros de table';"
					. "document.forms['form_repartition'].ok_particulier.focus();"
					. "document.forms['form_repartition'].envoi_centre.value=$old_idCentre;"
					. "document.forms['form_repartition'].repart_ville.value=$idVille;"
					. "document.forms['form_repartition'].repart_etablissement.value=0;"
					. "document.forms['form_repartition'].nombre.select();\""
					." TITLE='Cliquez pour acc&eacute;der &agrave; leur r&eacute;partition'>$cptVille candidats</A></small>"
					."</TH>\n\t"
               ."<TH>".afficheTaux($cptVille/$nbCandidats,2)."</TH>\n"
					;
			$tab[]="<td colspan=5><hr size=0/></td>";
         echo table_a_la_spip($titre,$tab);
      fin_cadre_relief();
   debut_boite_info();
      echo capaciteCentres($annee, $idDept);
   fin_boite_info();

} elseif(strlen($_REQUEST["step2"])>0) {
//////////////////////////////////////////////// step 2 : affichage répartition du département par série (ou l'inverse selon contexte step1)
$annee=$_REQUEST['annee'];
$idDept=$_REQUEST['idDept'];
$idSerie=$_REQUEST['idSerie'];
$nbCandidats=$_REQUEST['nbCandidats'];
if($nbCandidats=='') {
	$nbCandidats=getNbCandidats($annee,$idDept,$idSerie);
}
$step2=$_REQUEST['step2'];
   debut_gauche();
      debut_boite_info();
         echo "<p>R&eacute;partition des candidats <b>".$tab_referentiel['departement'][$idDept].$tab_referentiel['serie'][$idSerie]."</b>"
            . "<hr size=1><b>Cliquez</b> sur le nombre de candidats pour <b>pr&eacute;remplir le formulaire</b> des cas particulier de r&eacute;partition</p>\n"
            ;
      fin_boite_info();
      odb_raccourcis('');
   creer_colonne_droite();
   debut_droite();
      debut_cadre_relief("", false, "", $titre = _T("R&eacute;partition des candidats $annee"));
         if($step2=='isDept') {
            echo "\n<!-- tri par departement -->\n";
            $inputSerie="<SELECT NAME='repart_serie'>".formOptionsRefInSelect('serie',$repart_serie)."</SELECT>\n";
            $inputDepartement="<SELECT NAME='repart_departement'>".formOptionsRefInSelect('departement',$idDept)."</SELECT>\n";
            $inputVille="<SELECT NAME='repart_ville' class='forml'>".formOptionsRefInSelect('ville',$repart_ville,'Ville',$idDept)."</SELECT>\n";
            $inputCentre="<SELECT NAME='envoi_centre' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centre',$envoi_centre,'',$idDept)."</SELECT>\n";
            $inputEtablissement="<SELECT NAME='repart_etablissement' class='forml'>".formOptionsRefInSelect('etablissement',$repart_etablissement,'&Eacute;tablissement',$idDept)."</SELECT>\n";
            $departement=$tab_referentiel['departement'][$idDept];
            $titre="R&eacute;partition $departement par s&eacute;rie ($nbCandidats candidats)";
            $triPar="S&eacute;rie";
            $ref=$tab_referentiel['serie'];
            $isDept=true;
         } elseif (strlen($idSerie>0)) {
            echo "\n<!-- tri par serie -->\n";
            $inputSerie="<SELECT NAME='repart_serie' class='forml'>".formOptionsRefInSelect('serie',$idSerie)."</SELECT>\n";
            $inputDepartement="<SELECT NAME='repart_departement' class='forml'>".formOptionsRefInSelect('departement',$repart_departement)."</SELECT>\n";
		      $inputVille="<SELECT NAME='repart_ville' class='forml'>".formOptionsRefInSelect('villes',$repart_ville)."</SELECT>\n";
            $inputCentre="<SELECT NAME='envoi_centre' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$envoi_centre)."</SELECT>\n";
            $inputEtablissement="<SELECT NAME='repart_etablissement' class='forml'>".formOptionsRefInSelect('etablissements',$repart_etablissement,'&Eacute;tablissement')."</SELECT>\n";
            $serie=$tab_referentiel['serie'][$idSerie];
            $titre="R&eacute;partition s&eacute;rie $serie par d&eacute;partement ($nbCandidats candidats)";
            $triPar="D&eacute;partement";
            $ref=$tab_referentiel['departement'];
            $isDept=false;
         } else die(KO." - Impossible de d&eacute;terminer le tri");

         $tab[]="<TH>$triPar</TH><TH><small>Nombre de candidats<br>&agrave; r&eacute;partir</small></TH><TH>%</TH>";
         foreach($ref as $id => $valeur) {
            if(is_numeric($id)) {
               if($isDept)
                  $idSerie=$id;
               else
                  $idDept=$id;
               $sql="SELECT count(*) FROM odb_candidats can WHERE departement=$idDept AND annee=$annee and serie=$idSerie and id_table='0'";
               //echo "$id - $valeur<pre>$sql</pre>";
               $result=odb_query($sql,__FILE__,__LINE__);
               $row=mysql_fetch_array($result);
               $nb_rows=(int)$row[0];

               if($nb_rows>0)
                  $lien="<b><A HREF='".generer_url_ecrire('odb_repartition')."&step3=manuel&idDept=$idDept&idSerie=$idSerie&nbCandidats=$nb_rows&annee=$annee'>$valeur</A></b>";
               else $lien=$valeur;
               if($nb_rows>0) {
                  $nb_rows_txt="<A HREF=\"javascript:document.forms['form_repartition'].nombre.value=$nb_rows;"
                     . "document.forms['form_repartition'].ok_particulier.disabled=false;"
                     . "document.forms['form_repartition'].ok_generique.disabled=true;"
                     . "document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces $nb_rows num&eacute;ros de table';"
                     . "document.forms['form_repartition'].ok_particulier.focus();"
                     . "document.forms['form_repartition'].repart_serie.value=$idSerie;"
                     . "document.forms['form_repartition'].repart_departement.value=$idDept;"
                     . "document.forms['form_repartition'].nombre.select();\""
                     ." TITLE='Cliquez pour acc&eacute;der &agrave; leur r&eacute;partition'>$nb_rows</A>";
						$tab[]="<TD>$lien</TD>"
								."<TD>$nb_rows_txt</TD>"
								."<TD>".afficheTaux($nb_rows/$nbCandidats,4 )."<small>(".round(100*$nb_rows/$nbCandidats,2)."%)</small></TD>\n"
								;
					}
               else $nb_rows_txt=$nb_rows;
            }
         }
         echo table_a_la_spip($titre,$tab);
      fin_cadre_relief();
} 
if(!((strlen($_POST['ok_generique'])>0 || strlen($_REQUEST['from_ville']>0) || strlen($_REQUEST['from_etablissement']>0))) && $step2=='' && $step3=="") {
//////////////////////////////////////////////// step 1 : affichage répartition géo / département
$annee=isset($_REQUEST['annee'])?$_REQUEST['annee']:date("Y");
if(!isset($_REQUEST['ok_particulier'])) {
   debut_gauche();
      odb_raccourcis('odb_repartition');
   creer_colonne_droite();
   debut_droite();
}
      $inputSerie="<SELECT NAME='repart_serie' class='forml'>".formOptionsRefInSelect('serie',$repart_serie)."</SELECT>\n";
      $inputDepartement="<SELECT NAME='repart_departement' class='forml'>".formOptionsRefInSelect('departement',$repart_departement)."</SELECT>\n";
      $inputVille="<SELECT NAME='repart_ville' class='forml'>".formOptionsRefInSelect('villes',$repart_ville)."</SELECT>\n";
      $inputCentre="<SELECT NAME='envoi_centre' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$envoi_centre)."</SELECT>\n";

      odb_introspection($annee);

      debut_cadre_relief("", false, "", $titre = _T("R&eacute;partition des candidats $annee"));
         $sql="SELECT count(*) FROM odb_candidats can WHERE annee=$annee";
         $result=odb_query($sql,__FILE__,__LINE__);
         $row=mysql_fetch_array($result);
         $nbCandidats=(int)$row[0];
         $titre="R&eacute;partition g&eacute;ographique B&eacute;nin ($nbCandidats candidats)";
         $tab[]="<TH>D&eacute;partement</TH><TH><small>Nombre de candidats<br/>&agrave; r&eacute;partir</small></TH><TH>%</TH>";
         foreach($tab_referentiel['departement'] as $idDept => $dept) {
            if(is_numeric($idDept)) {
               $sql="SELECT count(*) FROM odb_candidats can WHERE departement=$idDept AND annee=$annee and id_table='0'";
               $result=odb_query($sql,__FILE__,__LINE__);
               $row=mysql_fetch_array($result);
               $nb_rows=(int)$row[0];

               if($nb_rows>0) $lien="<b><A HREF='".generer_url_ecrire('odb_repartition')."&step2=isDept&idDept=$idDept&nbCandidats=$nb_rows&annee=$annee'>$dept</A></b>";
               else $lien=$dept;
               if($nb_rows>0) {
                  $nb_rows_txt="<A HREF=\"javascript:document.forms['form_repartition'].nombre.value=$nb_rows;"
                     . "document.forms['form_repartition'].ok_particulier.disabled=false;"
                     . "document.forms['form_repartition'].ok_generique.disabled=true;"
                     . "document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces $nb_rows num&eacute;ros de table';"
                     . "document.forms['form_repartition'].ok_particulier.focus();"
                     . "document.forms['form_repartition'].repart_serie.value='0';"
                     . "document.forms['form_repartition'].repart_departement.value=$idDept;"
                     . "document.forms['form_repartition'].nombre.select();\""
                     ." TITLE='Cliquez pour acc&eacute;der &agrave; leur r&eacute;partition'>$nb_rows</A>";
						$tab[]="<TD>$lien</TD>"
								."<TD>$nb_rows_txt</TD>"
								."<TD>".afficheTaux($nb_rows/$nbCandidats,4 )."<small>(".round(100*$nb_rows/$nbCandidats,2)."%)</small></TD>\n"
								;
						}
               else $nb_rows_txt=$nb_rows;
            }
         }
         echo table_a_la_spip($titre,$tab);
         echo "<hr size=1>\n";
         unset($tab);
         $titre="R&eacute;partition par s&eacute;rie B&eacute;nin ($nbCandidats candidats)";
         $tab[]="<TH>S&eacute;rie</TH><TH><small>Nombre de candidats<br/>&agrave; r&eacute;partir</small></TH><TH>%</TH>";
         foreach($tab_referentiel['serie'] as $idSerie => $serie) {
            if(is_numeric($idSerie)) {
               $sql="SELECT count(*) FROM odb_candidats can WHERE serie=$idSerie AND annee=$annee and id_table='0'";
               $result=odb_query($sql,__FILE__,__LINE__);
               $row=mysql_fetch_array($result);
               $nb_rows=(int)$row[0];

               if($nb_rows>0)
                  $lien="<b><A HREF='".generer_url_ecrire('odb_repartition')."&step2=isSerie&idSerie=$idSerie&nbCandidats=$nb_rows&annee=$annee'>$serie</A></b>";
               else $lien=$serie;
               if($nb_rows>0) {
                  $nb_rows_txt="<A HREF=\"javascript:document.forms['form_repartition'].nombre.value=$nb_rows;"
                     . "document.forms['form_repartition'].ok_particulier.disabled=false;"
                     . "document.forms['form_repartition'].ok_generique.disabled=true;"
                     . "document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces $nb_rows num&eacute;ros de table';"
                     . "document.forms['form_repartition'].ok_particulier.focus();"
                     . "document.forms['form_repartition'].repart_serie.value=$idSerie;"
                     . "document.forms['form_repartition'].repart_departement.value=0;"
                     . "document.forms['form_repartition'].nombre.select();\""
                     ." TITLE='Cliquez pour acc&eacute;der &agrave; leur r&eacute;partition'>$nb_rows</A>";
						$tab[]="<TD>$lien</TD>"
								."<TD>$nb_rows_txt</TD>"
								."<TD>".afficheTaux($nb_rows/$nbCandidats,4 )."<small>(".round(100*$nb_rows/$nbCandidats,2)."%)</small></TD>\n"
								;
					}
               else $nb_rows_txt=$nb_rows;
            }
         }
         echo table_a_la_spip($titre,$tab);

      fin_cadre_relief();
   $inputEtablissement="<SELECT NAME='repart_etablissement' class='forml'>".formOptionsRefInSelect('etablissements',$repart_etablissement,'&Eacute;tablissement')."</SELECT>\n";
}
$nombre=isset($_REQUEST['nombre'])?$_REQUEST['nombre']:0;
$inputLv1="<SELECT NAME='repart_lv1' class='fondo'>".formOptionsRefInSelect('lv',$repart_lv1,'LV1')."</SELECT>\n";
$inputLv2="<SELECT NAME='repart_lv2' class='fondo'>".formOptionsRefInSelect('lv',$repart_lv2,'LV2')."</SELECT>\n";
$inputEf1="<SELECT NAME='repart_ef1' class='fondo'>".formOptionsRefInSelect('ef',$repart_ef1,'EF1')."</SELECT>\n";
$inputEf2="<SELECT NAME='repart_ef2' class='fondo'>".formOptionsRefInSelect('ef',$repart_ef2,'EF2')."</SELECT>\n";
$inputEps="<SELECT NAME='repart_eps' class='fondo'>".formOptionsRefInSelect('eps',$repart_eps,'EPS')."</SELECT>\n";
echo "<br/>\n<!-- ================== Formulaire repartition ================= -->\n";
debut_boite_info();

echo "<form name='form_repartition' method='POST' action='".generer_url_ecrire('odb_repartition')."' class='forml spip_xx-small'>\n";
echo "<FIELDSET>\n";
echo "<LEGEND>Cas particuliers</LEGEND>\n";
echo "Disposer...\n"
   . "<ul>\n<li><INPUT name='nombre' class='fondo' size=6 maxlength=6 value='0' onKeyUp=\"if(this.value>0 || this.value=='*') {document.forms['form_repartition'].ok_generique.disabled=true;document.forms['form_repartition'].ok_particulier.disabled=false;document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces '+this.value+' num&eacute;ros de table';} else {document.forms['form_repartition'].ok_generique.disabled=false;document.forms['form_repartition'].ok_particulier.disabled=true;document.forms['form_repartition'].ok_particulier.value='Choisissez le nombre de candidats &agrave; r&eacute;partir';}\"> candidats (tapez <b>*</b> pour tous ceux qui correspondent aux crit&egrave;res ci-dessous)</li>\n</ul>\n"
   . "<FIELDSET>\n<LEGEND>Choix recommand&eacute;s</LEGEND>\n"
   . "<table border=0>\n"
   . "<tr><th>issus de série</th><th>$inputSerie</th></tr>\n"
   . "<tr><th>et du d&eacute;partement</th><th>$inputDepartement</th></tr>\n"
   . "<tr><th>et de la ville</th><th>$inputVille</th></tr>\n"
   . "<tr><th>et de l'&eacute;tablissement</th><th>$inputEtablissement</th></tr>\n"
   . "</table>\n</FIELDSET>\n"
   . "<FIELDSET>\n<LEGEND>Autres choix</LEGEND>\n"
   . "<table border=0 width='100%'>\n"
   . "<tr><td>Langues</td><td>$inputLv1 $inputLv2</td></tr>\n"
   . "<tr><td>&Eacute;preuves facultatives</td><td>$inputEf1 $inputEf2</td></tr>\n"
   . "<tr><td>Sport</td><td>$inputEps</td></tr></table>\n"
   . "</FIELDSET>\n"
   . "<table border=0><tr><th>...dans le centre</th><th>$inputCentre</th></tr></table>\n"
   ;
echo "<INPUT TYPE=SUBMIT name='ok_particulier' CLASS='fondo' VALUE='Veuillez choisir un nombre de candidats &agrave; r&eacute;partir' disabled"
   . " onclick=\"if(document.forms['form_repartition'].envoi_centre.value=='0') {alert('Veuillez choisir le centre dans lequel vous souhaitez\\ndisposer ces candidats');document.forms['form_repartition'].envoi_centre.focus();return false;}\"/>\n";
echo "</FIELDSET></FIELDSET><br/>\n";
echo "<FIELDSET>\n";
echo "<LEGEND>Cas g&eacute;n&eacute;riques</LEGEND>\n";
echo "Si vous avez trait&eacute; tous les cas particuliers, vous pouvez :<br/>\n";
$sql="select count(*) from odb_candidats where annee=$annee and id_table='0'";
$result=odb_query($sql,__FILE__,__LINE__);
$row=mysql_fetch_array($result);
$nb_restant=$row[0];
echo "<INPUT TYPE=SUBMIT name='ok_generique' CLASS='fondo' VALUE='G&eacute;n&eacute;rer les $nb_restant num&eacute;ros de table restant' onclick=\"return confirm('Vous souhaitez terminer le processus.\\nEtes-vous bien certain d\'avoir fini la disposition de tous les cas particuliers ?\\n - Series E, F1-F4\\n - Langues mortes\\n - etc')\">\n";
echo "</FIELDSET>\n";
echo "<INPUT TYPE='hidden' NAME='step2' VALUE='$step2'>\n";
echo "<INPUT TYPE='hidden' NAME='step3' VALUE='$step3'>\n";
echo "<INPUT TYPE='hidden' NAME='idDept' VALUE='$idDept'>\n";
echo "<INPUT TYPE='hidden' NAME='idSerie' VALUE='$idSerie'>\n";
echo "<INPUT TYPE='hidden' NAME='idEtablissement' VALUE='$idEtablissement'>\n";
echo "<INPUT TYPE='hidden' NAME='nbCandidats' VALUE='$nbCandidats'>\n";
echo "<INPUT TYPE='hidden' NAME='annee' VALUE='$annee'>\n";
echo "<INPUT TYPE='hidden' NAME='from_ville' VALUE='$from_ville'>\n";
echo "<INPUT TYPE='hidden' NAME='from_serie' VALUE='$from_serie'>\n";
echo "<INPUT TYPE='hidden' NAME='from_etablissement' VALUE='$from_etablissement'>\n";
echo "<INPUT TYPE='hidden' NAME='envoi_nombre' VALUE='$envoi_nombre'>\n";
echo "<INPUT TYPE='hidden' NAME='etablissement_txt' VALUE='$etablissement_txt'>\n";
echo "</form>\n";
fin_boite_info();
fin_cadre_relief();
fin_page();
exit;
}
?>


