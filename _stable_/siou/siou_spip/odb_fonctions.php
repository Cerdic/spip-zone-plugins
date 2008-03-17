<?php
///////////// #CANDIDAT
function siou_spip_candidat($id_saisie, $annee) {
   $query = "SELECT sex.sexe, pre.prefixe, nom,  prenoms"
          . " FROM odb_candidats can"
          . " LEFT JOIN odb_ref_prefixe pre on pre.id=can.prefixe"
          . " LEFT JOIN odb_ref_sexe sex on sex.id=can.sexe"
          . " WHERE id_saisie=$id_saisie AND annee=$annee"
          ;
   $result = spip_query($query);
   if ($row = spip_fetch_array($result)) {
      $sexe = $row['sexe'];
      $sexe=$sexe=='M'?'M.':'Mlle';
      $prefixe = stripslashes($row['prefixe']);
      $nom=stripslashes($row['nom']);
      $prenoms=stripslashes($row['prenoms']);
      return "$sexe $prefixe <b>$nom</b> $prenoms";
   } else return "Aucun candidat ne correspond";
}

function balise_CANDIDAT($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_candidat($id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

///////////// #DLDN (date et lieu de naissance)
function siou_spip_dldn($id_saisie, $annee) {
   $query = "SELECT ldn, ne_en, ne_le, ne_vers FROM odb_candidats"
          . " WHERE id_saisie=$id_saisie AND annee=$annee"
          ;
   $result = spip_query($query);
   if ($row = spip_fetch_array($result)) {
      $ne_en = $row['ne_en'];
      $ne_le = $row['ne_le'];
      $ne_vers = $row['ne_vers'];
      $ldn = stripslashes($row['ldn']);
      if($ne_en>0) $ddn="En $ne_en";
      elseif($ne_vers>0) $ddn="Vers $ne_vers";
      else {
         $tDate=explode('-',$ne_le);
         $annee=$tDate[0];
         $mois=$tDate[1];
         $jour=$tDate[2];
         $ddn="$jour/$mois/$annee";
      }
      $ddn="<b>$ddn</b> $ldn";
      return $ddn;
   } else return "DLDN introuvable";
}

function balise_DLDN($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_dldn($id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

///////////// #CENTRE (centre de composition)
function siou_spip_centre($id_saisie, $annee) {
   $query = "SELECT cen.etablissement centre"
          . " FROM odb_candidats can, odb_repartition rep, odb_ref_etablissement cen"
          . " WHERE can.id_saisie=$id_saisie AND can.id_table=rep.id_table AND can.annee=$annee and rep.annee=$annee"
          . " AND rep.id_etablissement=cen.id"
          ;
   $result = spip_query($query);
   if ($row = spip_fetch_array($result)) {
      return stripslashes($row['centre']);
   } else return "Information indisponible";
}

function balise_CENTRE($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_centre($id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

///////////// referentiels
function siou_spip_ref($champ, $ref, $id_saisie, $annee) {
   $query = "SELECT $ref.$ref"
          . " FROM odb_candidats can, odb_ref_$ref $ref"
          . " WHERE id_saisie=$id_saisie AND annee=$annee AND $ref.id=can.$champ"
          ;
   $result = spip_query($query);
   while ($row = spip_fetch_array($result)) {
       $ret = stripslashes($row[$ref]);
       return $ret;
   }
}

///////////// #EPS
function balise_EPS($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_ref('eps', 'eps', $id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

///////////// #LV1
function balise_LV1($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_ref('lv1','lv', $id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

///////////// #LV2
function balise_LV2($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_ref('lv2','lv', $id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

///////////// #EF1
function balise_EPF1($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_ref('ef1','ef', $id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

///////////// #LV1
function balise_EPF2($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_ref('ef2','ef', $id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

///////////// #ETABLISSEMENT_ORIGINE
function balise_ETABLISSEMENT_ORIGINE($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_ref('etablissement','etablissement', $id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

///////////// #SERIE_TXT
function balise_SERIE_TXT($p) {
   $annee=champ_sql('annee',$p);
   $id_saisie=champ_sql('id_saisie',$p);
	$p->code = "siou_spip_ref('serie','serie', $id_saisie, $annee)";
	$p->statut = 'html';
	return $p;
}

////////////////////////////////////////////////// #RESULTAT
function siou_spip_resultat($id_table, $annee) {
   $query = "SELECT delib1, delib2, delib3"
          . " FROM odb_decisions decis"
          . " WHERE id_table='$id_table' AND annee=$annee"
          ;
   $result = spip_query($query);
   if ($row = spip_fetch_array($result)) {
      foreach(array('delib1','delib2','delib3') as $col) $$col=$row[$col];
      if($delib3=='Passable' || $delib3=='Reserve') $delib=$delib3;
      elseif(in_array(strtolower($delib2),array('passable','abien','bien','tbien'))) $delib=$delib2;
      else $delib=$delib1;
      if($delib=='Reserve') $delib="<b>$delib</b>";
      return $delib;
   } else return "En cours";
}

function balise_RESULTAT($p) {
   $id_table=champ_sql('id_table',$p);
   $annee=champ_sql('annee',$p);
   $p->code = "siou_spip_resultat($id_table, $annee)";
	$p->statut = 'html';
	return $p;
}

?>
