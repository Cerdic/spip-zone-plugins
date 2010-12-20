<?php
/*******************GEDCOM*******************************/
function genespip_TraitementDate2($mois)
{
 $split = split(' ',$mois);

 $mois_eng = Array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
 $mois_fr = Array('01','02','03','04','05','06','07','08','09','10','11','12');

 /* Remplacement */
 $mois = str_replace($mois_fr, $mois_eng, $mois);

 return ($mois);
}

function genespip_Traitementmot2($mot)
{
 $split = split(' ',$mot);

 $mots_eng = Array('BEF','AFT','ABT','EST',NULL);
 $mots_fr = Array('<','>','~','~','=');

 /* Remplacement */
 $mot = str_replace($mots_fr, $mots_eng, $mot);

 return ($mot);
}

//Conversion de date français Gedcom
function genespip_dategedfr($precision,$date) {
if ($precision!=NULL or $precision!="="){$precision=genespip_Traitementmot2($precision)." ";}else{$precision=NULL;}
    $split = split('-',$date); 
    if ($split[0]=="0000"){$annee=NULL;}else{$annee = $split[0];}
    if ($split[1]=="00"){$mois=NULL;}else{$mois = $split[1]." ";}
    if ($split[2]=="00"){$jour=NULL;}else{$jour = $split[2]." ";}
return $precision.$jour.genespip_TraitementDate2($mois).$annee;
}

function genespip_evt($type_evt,$id_individu) {
  $result_evt = sql_select('*', 'spip_genespip_evenements,spip_genespip_type_evenements', 'id_individu=$id_individu and spip_genespip_evenements.id_type_evenement=spip_genespip_type_evenements.id_type_evenement and spip_genespip_evenements.id_type_evenement=$type_evt');
  while ($evt = spip_fetch_array($result_evt)) {
$indi .= "1 ".$evt['type_evenement']."\r\n";
$indi .= "2 DATE ".genespip_dategedfr($evt['precision_date'],$evt['date_evenement'])."\r\n";
   if ($evt['id_lieu']!=1){
    $result_lieu = sql_select('*', 'spip_genespip_lieux', 'id_lieu='.$evt['id_lieu']);
    while ($lieu = spip_fetch_array($result_lieu)) {
$indi .= "2 PLAC ".utf8_decode($lieu['ville']).",".$lieu['code_departement'].",".utf8_decode($lieu['departement']).",".utf8_decode($lieu['region']).",".$lieu['pays']."\r\n";
    }
   }
  }
return $indi;
}
//creation table famille temp
function genespip_famille() {
	$sql = sql_create("spip_genespip_famtempo", 
			array(
			"id_fam" => "INT NOT NULL AUTO_INCREMENT PRIMARY KEY",
			"fam" => "TEXT NOT NULL",
			"type" => "TEXT NOT NULL",
			"id_individu" => "INT NOT NULL",
			"date_evt" => "TEXT NOT NULL",
			"place_evt" => "TEXT NOT NULL",
			)
			array(
			"PRIMARY KEY" => "id_fam",
			)
		);
}
function genespip_famille_remplir() {
//entrees HUSB et WIFE
  //selection des hommes dans la bd
  $result_individu = sql_select('*', 'spip_genespip_individu', 'poubelle!=1 and sexe=0');
     while ($I = spip_fetch_array($result_individu)) {
        $result_evt = sql_select('*', 'spip_genespip_evenements', 'id_type_evenement=3 and id_individu='.sql_quote($I['id_individu']));
           while ($evt = spip_fetch_array($result_evt)) {
            $date_evt = "2 DATE ".genespip_dategedfr($evt['precision_date'],$evt['date_evenement']);
             if ($evt['id_lieu']!=1){
              $result_lieu = sql_select('*', 'spip_genespip_lieux', 'id_lieu='.$evt['id_lieu']);
              while ($lieu = spip_fetch_array($result_lieu)) {
               $place_evt = "2 PLAC ".utf8_decode($lieu['ville']).",".$lieu['code_departement'].",".utf8_decode($lieu['departement']).",".utf8_decode($lieu['region']).",".$lieu['pays'];
              }
             }
            $num_fam=$evt['id_individu']."-".$evt['id_epoux'];
            $insert = sql_insert("spip_genespip_famtempo", "(fam, type, id_individu)", "('".sql_quote($num_fam)."', 'HUSB', ".sql_quote($evt['id_individu']).")");
            $insert = sql_insert("spip_genespip_famtempo", " (fam, type, id_individu, date_evt, place_evt)", "('".sql_quote($num_fam)."', 'WIFE', ".sql_quote($evt['id_epoux'])." ,'".sql_quote($date_evt)."', '".sql_quote($place_evt)."')");
            $date_evt=NULL;
            $place_evt=NULL;
            }
     }
//entrees CHIL
  $result_individu = sql_select('*', 'spip_genespip_individu', 'poubelle!=1');
     while ($I = spip_fetch_array($result_individu)) {
   // a pere et mere > num fam = pere-mere
      if ($I['pere']!=0 and $I['mere']!=0){
       $insert = sql_insert("spip_genespip_famtempo (fam, type, id_individu)", "('".sql_quote($I['pere'])."-".sql_quote($I['mere'])."', 'CHIL', ".sql_quote($I['id_individu']).")");
      }
   // a pere uniquement > num fam = pere
      elseif ($I['pere']!=0 and $I['pere']==0){
       $insert = sql_insert("spip_genespip_famtempo (fam, type, id_individu)", "(".sql_quote($I['pere']).", 'CHIL', ".sql_quote($I['id_individu']).")");
      }
   // a mere uniquement > num fam = mere
      elseif ($I['pere']!=0 and $I['pere']==0){
       $insert = sql_insert("spip_genespip_famtempo (fam, type, id_individu)", "(".sql_quote($I['pere']).", 'CHIL', ".sql_quote($I['id_individu']).")");
      }
     }
}

function genespip_gedcom_export() {
set_time_limit(0);
genespip_famille();
genespip_famille_remplir();
$chemin = _DIR_PLUGIN_GENESPIP."gedcom/";
$fic = "gedcom-".date("Y-m-d-H-i-s").".ged";
$handle = fopen($chemin.$fic, "x+");
echo "<a href='".$chemin.$fic."' target='_blank'>$fic</a><br />";
//Entete GedCOM GeneSPIP
$entete .="0 HEAD\r\n";
$entete .="1 SOUR GENESPIP\r\n";
$entete .="2 VERS 1\r\n";
$entete .="2 NAME GENESPIP\r\n";
$entete .="2 CORP CR\r\n";
$entete .="3 ADDR www.genespip.fr\r\n";
$entete .="1 DATE ".date("d-m-Y")."\r\n";
$entete .="2 TIME ".date("H:i:s")."\r\n";
$entete .="1 GEDC\r\n";
$entete .="2 VERS 5.5\r\n";
$entete .="2 FORM LINEAGE-LINKED\r\n";
$entete .="1 CHAR ANSEL\r\n";
$entete .="1 PLAC\r\n";
$entete .="2 FORM town , Area code , County , Region , Country\r\n";
fwrite($handle, $entete);
//INDIVIDU
$pointeurnote=0;
  $result_individu = sql_select("*", "spip_genespip_individu", "poubelle!=1");
  while ($I = spip_fetch_array($result_individu)) {
   if ($I['sexe']==1){$sexe="F";}else{$sexe="M";}
    $indi .= "0 @I".$I['id_individu']."@ INDI\r\n";
    $indi .= "1 NAME ".utf8_decode($I['prenom'])."/".$I['nom']."/\r\n";
    $indi .= "2 GIVN ".utf8_decode($I['prenom'])."\r\n";
    $indi .= "2 SURN ".utf8_decode($I['nom'])."\r\n";
    $indi .= "1 SEX ".$sexe."\r\n";
    $indi .= genespip_evt(1,$I['id_individu']);
    $indi .= genespip_evt(2,$I['id_individu']);
     if ($I['metier']!=NULL){$indi .= "1 OCCU ".utf8_decode($I['metier'])."\r\n";}
     if ($I['adresse']!=NULL){$indi .= "1 RESI ".utf8_decode($I['adresse'])."\r\n";}
//déclaration famille des parents
    $result_fams = sql_select("*", "spip_genespip_famtempo", "id_individu=".sql_quote($I['id_individu'])." and type=CHIL");
     while ($FAMS = spip_fetch_array($result_fams)) {
      $indi .= "1 FAMC @F".$FAMS['fam']."@\r\n";
     }
//déclaration famille du couple
    $result_fams=sql_select("*", "spip_genespip_famtempo", "id_individu=".$I['id_individu']." and type=HUSB or id_individu=".sql_quote($I['id_individu'])." and type=WIFE");
     while ($FAMS = spip_fetch_array($result_fams)) {
      $indi .= "1 FAMS @F".$FAMS['fam']."@\r\n";
     }
    if ($I['note']!=NULL){
     $pointeurnote=$pointeurnote+1;
     $indi .= "1 NOTE @NI".$pointeurnote."@\r\n";
     $indi .= "0 @NI".$pointeurnote."@ NOTE\r\n";
$note = str_replace("\n", "1 CONT ", $I['note']);
     $indi .= "1 CONT ".utf8_decode($note)."\r\n";
    }
   fwrite($handle, $indi);
   $indi=NULL;
  }

//FAMILLE
  $result_fams = sql_select("fam", "spip_genespip_famtempo", "fam");
  while ($FAMS = spip_fetch_array($result_fams)) {
   $fam .= "0 @F".$FAMS['fam']."@ FAM\r\n";
   $result_membre_fams = sql_select("*", "spip_genespip_famtempo", "fam=".sql_quote(_request("fam")));
    while ($FAMS_M = spip_fetch_array($result_membre_fams)) {
     $fam .= "1 ".$FAMS_M['type']." @I".$FAMS_M['id_individu']."@\r\n";
      if ($FAMS_M['type']=="WIFE"){
      $fam .= "1 MARR\r\n";
      if ($FAMS_M['date_evt']!=NULL){$fam .= $FAMS_M['date_evt']."\r\n";}
      if ($FAMS_M['place_evt']!=NULL){$fam .= $FAMS_M['place_evt']."\r\n";}
      }
    }
  fwrite($handle, $fam);
  $fam=NULL;
  }
fwrite($handle, "0 TRLR");
fclose($handle);
echo _T('genespip:export_termine')."<br />";
//Suppression table famtempo
$supptempo=sql_drop_table("spip_genespip_famtempo");
}
?>
