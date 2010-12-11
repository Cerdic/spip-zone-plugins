<?php
/*******************GEDCOM*******************************/
function genespip_TraitementDate($mois)
{
 $split = split(' ',$mois);

 $mois_eng = Array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
 $mois_fr = Array('01','02','03','04','05','06','07','08','09','10','11','12');

 /* Remplacement */
 $mois = str_replace($mois_eng, $mois_fr, $mois);

 return ($mois);
}

function genespip_Traitementmot($mot)
{
 $split = split(' ',$mot);

 $mots_eng = Array('BEF','AFT','ABT','EST',' ');
 $mots_fr = Array('<','>','~','~','=');

 /* Remplacement */
 $mot = str_replace($mots_eng, $mots_fr, $mot);

 return ($mot);
}

function genespip_Traitementaccent($texte)
{

 $origine = Array('âe','áe','áa','ðc','ãu','ãi','ãe','ão');
 $convert = Array('&eacute;','è','à','ç','û','î','ê','ô');

 /* Remplacement */
 $texte = str_replace($origine, $convert, $texte);

 return ($texte);
}

function genespip_cree_tabletempo() {
$sql = spip_query('CREATE TABLE `spip_genespip_tempo` ('
        . ' `id_tempo` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, '
        . ' `num_tableau` TEXT NOT NULL, '
        . ' `num_info` INT NOT NULL, '
        . ' `type` TEXT NOT NULL, '
        . ' `info` TEXT NOT NULL'
        . ' )');
}
function genespip_gedcom($fic) {
$date_update=date("Y-m-d H:i:s");
if($_POST['etape']!=NULL){$etape=$_POST['etape'];}else{$etape=1;}
switch ($etape){
case 1:

genespip_cree_tabletempo();
$lines = file ($fic);
$n=0;
$n1=0;
foreach ($lines as $line_num => $line) {
    $split = split(' ',trim($line));
    if ($split[0]==0){$n=$n+1;}
    if ($split[0]==1){$n1=$n1+1;}
    $rang=$n."-".$n1;
    $num_info=$line_num.".".$split[0];
    $detail=$split[0]." ".$split[1];
    $info=preg_replace('/'.$detail.'/','',$line);
    $info=genespip_Traitementaccent($info);
    $info=addslashes(utf8_encode(trim($info)));
    $insert_tempo1="INSERT INTO spip_genespip_tempo (num_tableau ,num_info, type, info)', '('".$rang."', ".trim($split[0]).", '".trim($split[1])."', '".$info."')";
    $insert_tempo1=spip_query($insert_tempo1);
    }
$result_tempo1 = sql_select('*', 'spip_genespip_tempo', 'type=PLAC', 'id_tempo limit 0,10');
echo _T('genespip:info_gedcom_etape2')."<br /><br />";
      echo "<table>";
while ($tempo1 = spip_fetch_array($result_tempo1)) {
      echo "<tr><td>".$tempo1['info']."</td>";
      echo "<form action='$url_action_accueil' method='post'>";
      echo "<td><input type='submit' value='"._T('genespip:choisir')."' class='fondo' /></td></tr>";
      echo "<input type='hidden' name='etape' value='2' />";
      echo "<input type='hidden' name='action' value='gedcom' />";
      echo "<input type='hidden' name='id_tempo' value='".$tempo1['id_tempo']."' value='2' />";
      echo "</form>";
}
      echo "</table>";
break;
case 2:
set_time_limit(0);
//Cr&eacute;ation de la table des lieux
$result_tempo1 = sql_select('*', 'spip_genespip_tempo', 'id_tempo='.sql_quote(_request('id_tempo')));
        echo _T('genespip:info_gedcom_etape3')."<br /><br />";
        echo "<table>";
        echo "<form action='$url_action_accueil' method='post'>";
        while ($tempo1 = spip_fetch_array($result_tempo1)) {
        $j=0;
        $split_plac = split(',',$tempo1['info']);
        $count=count($split_plac);
        while($j < $count){
        echo "<tr>";
        echo "<td><select name='champ".$j."'>";
        echo "<option value=''></option>";
        echo "<option value='ville'>"._T('genespip:ville')."</option>";
        echo "<option value='departement'>"._T('genespip:departement')."</option>";
        echo "<option value='code_departement'>"._T('genespip:num_departement')."</option>";
        echo "<option value='region'>"._T('genespip:region')."</option>";
        echo "<option value='pays'>"._T('genespip:pays')."</option>";
        echo "</select></td><td>".$split_plac[$j]."</td>";
        echo "</tr>";
        $j++;
        }
        echo "<input type='hidden' name='etape' value='3' />";
        echo "<input type='hidden' name='action' value='gedcom' />";
        echo "<input type='hidden' name='count' value='$count' />";
        echo "<tr><td colspan='2'><input type='submit' value='"._T('genespip:valider')."' class='fondo' /></td></tr>";
        echo "</form>";
        }
        echo "</table>";
break;
case 3:
set_time_limit(0);
         $result_tempo1 = sql_select('info', 'spip_genespip_tempo', 'type=PLAC', 'info');
         while ($tempo1 = spip_fetch_array($result_tempo1)) {
         $j=0;
         $ville=NULL;
         $split_plac = split(',',$tempo1['info']);
         while($j < $_POST['count']){
         if ($_POST['champ'.$j]=='ville'){$ville .= $split_plac[$j];}
         if ($_POST['champ'.$j]=='code_departement'){$code_departement=$split_plac[$j];}
         if ($_POST['champ'.$j]=='departement'){$departement=$split_plac[$j];}
         if ($_POST['champ'.$j]=='region'){$region=$split_plac[$j];}
         if ($_POST['champ'.$j]=='pays'){$pays=$split_plac[$j];}
         $j++;
         }
         $req_lieu=sql_insert('spip_genespip_lieux', '(ville, code_departement, departement, region, pays)', '('".$ville."','".$code_departement."','".$departement."','".$region."','".$pays."')');
         $req_lieu=spip_query($req_lieu);
         $id_req_lieu=mysql_insert_id();
         $action_sql = sql_update('spip_genespip_tempo', 'info = '".$id_req_lieu."'', 'type=PLAC and info = '".$tempo1['info']");
         }
      echo "<table>";
      echo "<tr><td>"._T('genespip:table_lieux_cree').".</td>";
      echo "<form action='$url_action_accueil' method='post'>";
      echo "<td><input type='submit' value='"._T('genespip:continuer')."' class='fondo' /></td></tr>";
      echo "<input type='hidden' name='etape' value='4' />";
      echo "<input type='hidden' name='action' value='gedcom' />";
      echo "</form>";
      echo "</table>";
break;
case 4:
set_time_limit(0);
//*********************************
$result_tempo1 = sql_select('*', 'spip_genespip_tempo');
        while ($tempo1 = spip_fetch_array($result_tempo1)) {

$splitpointeur = split('-',$tempo1['num_tableau']);
$pointeur1=$splitpointeur[0];
$pointeur2=$splitpointeur[1];

if ($pointeur1!=1){
//FAM
   if ($tempo1['info']=='FAM'){
   echo "<u>"._T('genespip:famille')."</u><br />";
   }
//INDI
   if ($tempo1['info']=='INDI' and trim($tempo1['type'])!='TYPE'){
 $id_individu=preg_replace('/@/','',trim($tempo1['type']));
 $id_individu=preg_replace('/IND/','',$id_individu);
 $id_individu=preg_replace('/I/','',$id_individu);
      echo "<u>"._T('genespip:individu')." $id_individu</u><br />";
        $result = sql_select('id_individu', 'spip_genespip_individu', 'id_individu='.$id_individu'');
        echo "<font color='#480000'>id_individu=$id_individu</font><br />";
        $pointeurindividu=$pointeur1;
        if (spip_num_rows($result)==0){
        $insert_fiche=sql_insert('spip_genespip_individu', '(id_individu, id_auteur, date_update)', '('".$id_individu."', '".$GLOBALS['connect_id_auteur']."', '".$date_update."')');
        }
   }
//NOTE
   if ($tempo1['info']=='NOTE'){
        echo $id_individu ."/NOTE type:".$tempo1['type'];
        $resultnote = sql_select('*', 'spip_genespip_individu', 'id_individu='.$id_individu'');
        while ($note = spip_fetch_array($resultnote)) {
        $pointeurnote=$pointeur1;
        $id_individu=$note['id_individu'];
        echo "<u>"._T('genespip:note_individu')." $id_individu</u><br />";
        }
   }
//D&eacute;tail FAM et INDI
      switch ($tempo1['type']){
      case "NAME":
        $pointeurNAME=$pointeur1;
        if ($pointeur1==$pointeurindividu and $pointeurNAME!=$pointeuroldNAME and trim($tempo1['info'])!=NULL){
        $pointeuroldNAME=$pointeurNAME;
        $splitNAME = split('/',$tempo1['info']);
        echo "<font color='#480000'> prenom=$splitNAME[0], nom=$splitNAME[1]</font><br />";
        if (trim($splitNAME[1])==NULL){$nom='?';}else{$nom=$splitNAME[1];}
        $action_sql = sql_update('spip_genespip_individu', 'nom = '".$nom."', prenom = '".$splitNAME[0]."'', 'id_individu = '.$id_individu);
        }
      break;
      case "SEX":
          if (trim($tempo1['info'])=='M'){$sexe=0;}elseif(trim($tempo1['info'])=='F'){$sexe=1;}
          echo "<font color='#480000'> "._T('genespip:sexe')."=$sexe (".trim($tempo1['info']).")</font><br />";
        $action_sql = sql_update('GENESPIP_INDIVIDU', 'sexe = ".$sexe."', 'id_individu = '.$id_individu);
      break;
      case "HUSB":
      $pointeurHUSB=$pointeur1;
        $epoux=preg_replace('/@/','',trim($tempo1['info']));
        $epoux=preg_replace('/IND/','',$epoux);
        $epoux=preg_replace('/I/','',$epoux);
        echo "<font color='#480000'> "._T('genespip:epoux')."=$epoux</font><br />";
      break;
      case "WIFE":
      $pointeurWIFE=$pointeur1;
        $epouse=preg_replace('/@/','',trim($tempo1['info']));
        $epouse=preg_replace('/IND/','',$epouse);
        $epouse=preg_replace('/I/','',$epouse);
      if ($pointeur1==$pointeurHUSB){$individu=$epoux;}
        $result = sql_select('*', 'spip_genespp_evenements', 'id_individu='.$individu.' and id_epoux='.$epouse'');
        if (spip_num_rows($result)==0){
        $insert_fiche=sql_insert('spip_genespip_evenements', '(id_individu, id_type_evenement, id_lieu, id_epoux, date_update)', '('".$individu."',3,1, '".$epouse."', '".$date_update."')');
        $insert_fiche=sql_insert('spip_genespip_evenements', '(id_individu, id_type_evenement, id_lieu, id_epoux, date_update)', '('".$epouse."',3,1, '".$individu."', '".$date_update."')');
        }
        echo "<font color='#480000'> "._T('genespip:epouse')."=$epouse</font><br />";
      break;
      case "CHIL":
        $individu=preg_replace('/@/','',trim($tempo1['info']));
        $individu=preg_replace('/IND/','',$individu);
        $individu=preg_replace('/I/','',$individu);
      $enfant=1;
      if ($pointeur1==$pointeurHUSB){$pere=$epoux;}else{$pere=0;}
      if ($pointeur1==$pointeurWIFE){$mere=$epouse;}else{$mere=0;}
        $result = sql_select('id_individu', 'spip_genespip_individu', 'id_individu='.$individu'');
        if (spip_num_rows($result)==0){
        $insert_fiche=sql_insert('spip_genespip_individu', '(id_individu, pere, mere, id_auteur, date_update)', '('".$individu."', '".$epoux."', '".$epouse."', '".$GLOBALS['connect_id_auteur']."', '".$date_update."')');
        }else{
        $action_sql = sql_update('spip_genespip_individu', 'pere = ".$epoux.", mere = ".$epouse." ', 'id_individu = '.$individu);
        }
        $result = sql_select('id_individu', 'spip_genespip_individu', 'id_individu='.$pere'');
        if (spip_num_rows($result)==0){
        $insert_fiche=sql_insert('spip_genespip_individu', '(id_individu, enfant, id_auteur, date_update)', '('".$pere."', '".$enfant."', '".$GLOBALS['connect_id_auteur'].",' '".$date_update."')');
        }else{
        $action_sql = sql_update('spip_genespip_individu', 'enfant = ".$enfant."', 'id_individu = '.$pere);
        }
        $result = sql_select('id_individu', 'spip_genespip_individu', 'id_individu='.$mere'');
        if (spip_num_rows($result)==0){
        $insert_fiche=sql_insert('spip_genespip_individu', '(id_individu, enfant, id_auteur, date_update)', '('".$mere."', '".$enfant."', '".$GLOBALS['connect_id_auteur']."', '".$date_update."')');
        }else{
        $action_sql = sql_update('spip_genespip_individu', 'enfant = ".$enfant."', 'id_individu = '.$mere);
        }
        echo "<font color='#480000'> "._T('genespip:enfant')."=$individu, "._T('genespip:pere')."=$epoux, "._T('genespip:mere')."=$epouse</font><br />";
      break;
      case "MARR":
      $pointeurmarr=$pointeur2;
        echo "<font color='#480000'> "._T('genespip:info_mariage')."&raquo;</font><br />";
      break;
      case "BIRT":
      $pointeurnaissance=$pointeur2;
       echo "<font color='#480000'> "._T('genespip:info_naissance')."&raquo;</font><br />";
      break;
      case "DEAT":
      $pointeurdeces=$pointeur2;
       echo "<font color='#480000'> "._T('genespip:info_deces')."&raquo;</font><br />";
      break;
      case "DATE":
     if ($pointeur2==$pointeurnaissance or $pointeur2==$pointeurdeces or $pointeur2==$pointeurmarr){
         $splitDATE=NULL;
         $precision_date=NULL;
         $date=NULL;
         $splitDATE = split(' ',$tempo1['info']);

        //     0   1    2    3
        //0 = num&eacute;rique
        //cas1 jj  mmm  aaaa
        //cas2 mmm aaaa             -> precision_date=~
        //cas3 aaaa                 -> precision_date=~
        //cas0
        //0 <> num&eacute;rique
        //cas4 PRE jj   mmm  aaaa   -> precision_date=PRE
        //cas5 PRE mmm  aaaa        -> precision_date=PRE
        //cas6 PRE aaaa             -> precision_date=PRE
/*cas0*/ if (trim($tempo1['info'])==NULL or trim($tempo1['info'])==""){
         $date="0000-00-00";
echo "cas0";
          }elseif (is_numeric($splitDATE[0])){
/*cas3*/   if ($splitDATE[1]==NULL){
echo "cas3 split0=".$splitDATE[0]."tempoinfo=".$tempo1['info'];
           $precision_date="~";
           $date=trim($splitDATE[0])."-00-00";
/*cas2*/   }elseif($splitDATE[2]==NULL){
echo "cas2";
           $precision_date="~";
           $mois=genespip_TraitementDate(trim($splitDATE[0]));
           $date=trim($splitDATE[1])."-".$mois."-00";
/*cas1*/   }else{
echo "cas1";
           $mois=genespip_TraitementDate(trim($splitDATE[1]));
           $date=trim($splitDATE[2])."-".$mois."-".trim($splitDATE[0]);
           }
          }else{
           $precision_date=genespip_Traitementmot(trim($splitDATE[0]));
/*cas6*/   if($splitDATE[2]==NULL){
echo "cas6";
           $date=trim($splitDATE[1])."-00-00";
/*cas5*/   }elseif($splitDATE[3]==NULL){
echo "cas5";
           $mois=genespip_TraitementDate(trim($splitDATE[1]));
           $date=trim($splitDATE[2])."-".$mois."-00";
/*cas4*/   }else{
echo "cas4";
           $mois=genespip_TraitementDate(trim($splitDATE[2]));
           $date=trim($splitDATE[3])."-".$mois."-".trim($splitDATE[1]);
           }
          }

        //
        /*if ($splitDATE[1]==NULL){
        $date=trim($splitDATE[0])."-00-00";
        $precision_date=genespip_Traitementmot(trim($splitDATE[0]));
        if(is_numeric($precision_date)){$precision_date="~";}
        }elseif($splitDATE[2]==NULL){
        $mois=genespip_TraitementDate(trim($splitDATE[0]));
        $precision_date=genespip_Traitementmot(trim($mois));
        if(is_numeric($precision_date)){$precision_date="~";}
        $date=trim($splitDATE[1])."-".$mois."-00";
        }else{
        $precision_date="=";
        $mois=genespip_TraitementDate(trim($splitDATE[1]));
        $date=trim($splitDATE[2])."-".$mois."-".trim($splitDATE[0]);
        }*/
     }
      if ($pointeur2==$pointeurnaissance){
        echo "<font color='#480000'>&nbsp;&nbsp;&nbsp;"._T('genespip:naissance')."=$precision_date $date</font><br />";
      $id_type_evenement=1;
        $result = sql_select('*', 'spip_genespip_evenements', 'id_type_evenement="'.$id_type_evenement.'" and id_individu = "'.$id_individu);
        if (spip_num_rows($result)==0){
        $insert_fiche=sql_insert('spip_genespip_evenements', '(id_individu, id_type_evenement, date_evenement, precision_date, id_lieu)', '(".$id_individu.",".$id_type_evenement.",'".$date."', '".$precision_date."',1)');
        }else{
        $action_sql = sql_update('GENESPIP_EVENEMENTS', 'date_evenement = '".$date."', precision_date = '".$precision_date."'', 'id_type_evenement=".$id_type_evenement." and id_individu = '.$id_individu);
        }
      }
      if ($pointeur2==$pointeurdeces){
        echo "<font color='#480000'>&nbsp;&nbsp;&nbsp;"._T('genespip:deces')."=$precision_date $date</font><br />";
      $id_type_evenement=2;
        $result = sql_select('*', 'spip_genespip_evenements', 'id_type_evenement="'.$id_type_evenement.'" and id_individu = "'.$id_individu);
        if (spip_num_rows($result)==0){
        $insert_fiche=sql_insert('spip_genespip_evenements', '(id_individu, id_type_evenement, date_evenement, precision_date, id_lieu)', '(".$id_individu.",'".$id_type_evenement."' ,'".$date."', '".$precision_date."',1)');
        }else{
        $action_sql = sql_update('spip_genespip_evenements', 'date_evenement = '".$date."', precision_date = '".$precision_date."'', 'id_type_evenement=".$id_type_evenement." and id_individu = '.$id_individu);
        }
      }
      if ($pointeur2==$pointeurmarr){
        echo "<font color='#480000'>&nbsp;&nbsp;&nbsp;"._T('genespip:mariage')."=$precision_date $date</font><br />";
      $id_type_evenement=3;
        $action_sql = sql_update('spip_genespip_evenements', 'date_evenement = '".$date."', precision_date = '".$precision_date."'', 'id_type_evenement="'.$id_type_evenement.'" and id_individu = "'.$epoux.'" and id_epoux="'.$epouse);
        $action_sql = sql_update('spip_genespip_evenements', 'date_evenement = '".$date."', precision_date = '".$precision_date."'', 'id_type_evenement="'.$id_type_evenement.'" and id_individu = "'.$epouse.'" and id_epoux="'.$epoux);
      }
      break;
      case "PLAC":
        $id_lieu=$tempo1['info'];
      if ($pointeur2==$pointeurnaissance){
      $id_type_evenement=1;
        $action_sql = sql_update('spip_genespip_evenements', 'id_lieu = '".$id_lieu."'', 'id_type_evenement=".$id_type_evenement." and id_individu = "'.$id_individu);
      }
      if ($pointeur2==$pointeurdeces){
      $id_type_evenement=2;
        $action_sql = sql_update('spip_genespip_evenements', 'id_lieu = '".$id_lieu."'', 'id_type_evenement=".$id_type_evenement." and id_individu = "'.$id_individu);
      }
      if ($pointeur2==$pointeurmarr){
      $id_type_evenement=3;
        $action_sql = sql_update('spip_genespip_evenements', 'id_lieu = '".$id_lieu."'', 'id_type_evenement="'.$id_type_evenement.'" and id_individu = "'.$epoux.'" and id_epoux="'.$epouse);
        $action_sql = sql_update('spip_genespip_evenements', 'id_lieu = '".$id_lieu."'', 'id_type_evenement="'.$id_type_evenement.'" and id_individu = "'.$epouse.'" and id_epoux="'.$epoux);
      }

      break;
      case "NOTE":
        $note=$tempo1['info'];
        if ($pointeur1==$pointeurindividu){
        echo "<b>NOTE</b> <font color='#480000'> "._T('genespip:note')."=(INDIVIDU $id_individu) $note </font><br />";
        //$resultnote = sql_select('*', 'GENESPIP_INDIVIDU', 'id_individu=".$id_individu);
        //while ($noteold = spip_fetch_array($resultnote)) {
        //$note=$noteold['note']."\r".$note;
        //}
        $action_sql = sql_update('spip_genespip_individu', 'note = '".$note."'', 'id_individu = "'.$id_individu);
        }
      break;
      case "CONC":
        $note=$tempo1['info'];
        if ($pointeur1==$pointeurindividu or $pointeur1==$pointeurnote){
        echo "<b>CONC</b> <font color='#480000'> "._T('genespip:note')."=(INDIVIDU $id_individu) $note </font><br />";
        $resultnote = sql_select('*', 'spip_genespip_individu', 'id_individu="'.$id_individu);
        while ($noteold = spip_fetch_array($resultnote)) {
        $note=$noteold['note']." ".$note;
        }
        $action_sql = sql_update('spip_genespip_individu', 'note = '".$note."'', 'id_individu = '.$id_individu);
        $note=NULL;
        }
      break;
      case "CONT":
        $note=$tempo1['info'];
        if ($pointeur1==$pointeurindividu or $pointeur1==$pointeurnote){
        echo "<b>CONT</b> <font color='#480000'> "._T('genespip:note')."=(INDIVIDU $id_individu) $note </font><br />";
        $resultnote = sql_select('*', 'spip_genespip_individu', 'id_individu="'.$id_individu);
        while ($noteold = spip_fetch_array($resultnote)) {
        $note=$noteold['note']." ".$note;
        }
        $action_sql = sql_update('spip_genespip_individu', 'note = '".$note."'', 'id_individu = '".$id_individu"'');
        $note=NULL;
        }
      break;
      case "RESI":
        $adresse=$tempo1['info'];
        if ($pointeur1==$pointeurindividu){
        echo "<font color='#480000'> "._T('genespip:adresse')."= $adresse </font><br />";
        $action_sql = sql_update('GENESPIP_INDIVIDU', 'adresse = '".$adresse."'', 'id_individu = '".$id_individu"'');
        }
      break;

      case "OCCU":
        $occu=$tempo1['info'];
        if ($pointeur1==$pointeurindividu){
        //$splitNAME = split('/',$tempo1['info']);
        echo "<font color='#480000'> "._T('genespip:metier')."= $occu </font><br />";
        $action_sql = sql_update('spip_genespip_individu', 'metier = '".$occu."'', 'id_individu = '".$id_individu"'');
        }
      break;

      default:
        echo $tempo1['num_tableau']."/".$tempo1['num_info']."/".$tempo1['type'].": ".$tempo1['info']."<br />";
      }
}
}
//Suppression des entr&eacute;es avec des noms vides
echo "<br />"._T('genespip:nettoyage_champ_nom').;
$nettoyage=spip_query("DELETE', 'GENESPIP_INDIVIDU', 'nom like ''");
echo "--> <font color='red'>OK</font>";
//Nettoyage champs GENESPIP_LIEUX non utilisés
echo "<br />"._T('genespip:nettoyage_table_lieux').;
            $result = sql_select('*', 'spip_genespip_lieux', 'ville');
            while ($lieux = spip_fetch_array($result)) {
            $resultnb = sql_select('*', 'spip_genespip_evenements', 'id_lieu="'.$lieux['id_lieu']);
            if(spip_num_rows($resultnb)==0){
            spip_query("DELETE', 'GENESPIP_LIEUX', 'id_lieu = ".$lieux['id_lieu']);
            }}
echo "--> <font color='red'>OK</font>";
//Suppression table tempo
echo "<br />"._T('genespip:suppression_table_temporaire')."";
$supptempo=spip_query("DROP TABLE spip_genespip_tempo");
echo "--> <font color='red'>OK</font>";
//MAJ liste patronyme
genespip_maj_liste();
break;
}
}

?>
