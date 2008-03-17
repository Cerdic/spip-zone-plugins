<?php
global $odb_referentiel,$odb_mapping;
define('PAYS','benin');
$odb_referentiel=array(
"odb_candidats" => array(
   "Num_Saisie" => "INTEGER",
   "Num_Table"=>"VARCHAR",
	"Old_Num_Table" => "VARCHAR",
   "Annee"=>"YEAR",
   "Serie" => "refSerie",
   "Prefixe" => "refPrefixe",
   "Nom" => "VARCHAR",
   "Prenoms" => "VARCHAR",
   "Ne_le" => "DATE",
   "Ne_en" => "YEAR",
   "Ne_vers" => "YEAR",
   "Ville_Naissance" => "VARCHAR",
   "Pays_Naissance" => "refPays",
   "Sexe" => "refSexe",
   "Nationalite" => "refPays",
   "Quartier_Residence" => "VARCHAR",
   "LV1" => "refLV",
   "LV2" => "refLV",
   "EPS" => "refEPS",
   "EF1" => "refEF",
   "EF2" => "refEF",
   "Ville" => "refVille",
   "Departement" => "refDepartement",         //'RIENDUTOUT', //
   "Etablissement" => "Et.",
   "Ajourne" => "VraiFaux",
   "Date_naiss_invalide" => "VraiFaux",
   "MAJ"=>"TIMESTAMP",
   "GUESS" => array(
      "Num_Anonyme" => "VARCHAR",
      "Centre" => "refSalle",
      "Salle" => "INTEGER"
   )
),
"odb_notes" => array(
   "Num_Anonyme" => "VARCHAR",
   "Serie" => "refSerie",
   "Num_Jury" => "INTEGER"
   )
);
$odb_mapping=array(
"odb_candidats"=>array(
   "id_saisie"=>"Num_Saisie",
   "id_table"=>"Num_Table",
   "id_table_old"=>"Old_Num_Table",
   "annee"=>"Annee",
   "serie"=>"Serie",
   "prefixe"=>"Prefixe",
   "nom"=>"Nom",
   "prenoms"=>"Prenoms",
   "ne_le"=>"Ne_le",
   "ne_en"=>"Ne_en",
   "ne_vers"=>"Ne_vers",
   "ldn"=>"Ville_Naissance",
   "pdn"=>"Pays_Naissance",
   "sexe"=>"Sexe",
   "nationalite"=>"Nationalite",
   "quartier_res"=>"Quartier_Residence",
   "lv1"=>"LV1",
   "lv2"=>"LV2",
   "eps"=>"EPS",
   "ef1"=>"EF1",
   "ef2"=>"EF2",
   "ville"=>"Ville",
   "departement"=>"Departement",
   "etablissement"=>"Etablissement",
   "ajourne"=>"Ajourne",
   "non_inscrit"=>"Date_naiss_invalide",
   "maj"=>"MAJ"
   )
);

// rcupre le rfrentiel dans le tableau $tab_referentiel__
// $ref : nom de la table de referentiel sans le prefixe 'odb_ref_'
// $retour :
//   - 'id' pour retourner tableau des id tris par valeur
//   - 'valeur' pour retourner un tableau des valeurs tries par id
//   - 'tout' pour retourner les 2 ci-dessus
function getReferentiel($ref,$retour='tout') {
   static $tab_referentiel__;
   if(!isset($tab_referentiel__[$ref])) { // histoire de pas retourner en base si d&eacute;j pomp&eacute;
      switch(strtolower($ref)) {
         case 'etablissement':
            $sql="SELECT id, id_departement, etablissement FROM odb_ref_$ref ORDER BY id_departement, etablissement";
            break;
         case 'centre':
            $sql="SELECT id, id_departement, etablissement, annee_centre from odb_ref_etablissement WHERE annee_centre>0 order by id_departement, etablissement";
            break;
         case 'ville':
            $sql="SELECT id, id_departement, $ref from odb_ref_$ref order by id_departement, $ref";
            break;
         default:
            $sql = "SELECT id, $ref FROM odb_ref_$ref ORDER BY $ref";
      }
      $result=odb_query($sql,__FILE__,__LINE__);
      if(mysql_num_rows($result)==0) die (KO." - Rien n'a t trouv dans le rfrentiel $ref'");
      while($row=mysql_fetch_array($result)) {
         $id=$row["id"];
         $ref2=$ref=='centre'?'etablissement':$ref;
         $valeur=unicode2charset(charset2unicode(trim($row[$ref2])));
         switch (strtolower($ref)) {
            case 'etablissement':
            case 'ville':
               $id_departement=$row['id_departement'];
               if($retour=='tout' || $retour=='id')
                  $tab_referentiel__[$ref][$id_departement][$valeur]=$id;
               if($retour=='tout' || $retour=='valeur')
                  $tab_referentiel__[$ref][$id_departement][$id]=$valeur;
               break;
            case 'centre':
               $id_departement=$row['id_departement'];
               if($retour=='tout' || $retour=='id')
               $tab_referentiel__[$ref][$id_departement][$valeur]=$id;
               if($retour=='tout' || $retour=='valeur') {
                  $tab_referentiel__[$ref][$id_departement][$id]['libelle']=$valeur;
                  $tab_referentiel__[$ref][$id_departement][$id]['annee_centre']=$row['annee_centre'];
               }
               break;
            default:
               if($retour=='tout' || $retour=='id')
                  $tab_referentiel__[$ref][$valeur]=$id;
               if($retour=='tout' || $retour=='valeur')
                  $tab_referentiel__[$ref][$id]=$valeur;
         }
      }
   }
   asort($tab_referentiel__[$ref]);
   return $tab_referentiel__[$ref];
}

/**
 * Met les decisions a jour
 *
 * @param int $annee
 * @param int $jury : jury dont il faut mettre les decicions a jour (tous par defaut)
 * @param int $iPrecision : precision de la moyenne (1 par defaut)
 */
function odb_maj_decisions($annee,$jury=0,$iPrecision=3,$deliberation=1) {
	if($jury>0) {
		$from_jury=", odb_repartition rep";
		$where_jury=" AND rep.id_table=notes.id_table and rep.annee = $annee and rep.jury=$jury";
	}
	if($deliberation==1) {
		// supprime les notes d'eps des candidats dispenses (logiquement pour corrrection des cas reserves)
		$tSql[]="DELETE from odb_notes notes using odb_notes notes, odb_candidats can, odb_ref_eps eps\n".
		" where can.id_table=notes.id_table and can.annee=$annee and notes.annee=$annee and can.eps=eps.id and eps.eps!='Apte' and notes.id_matiere=-3";
		$tSql[]="UPDATE odb_notes notes $from_jury SET notes.note='0' WHERE type='Divers' and note<0 $where_jury and notes.annee=$annee";
		$tSql[]="REPLACE INTO odb_decisions( id_table, id_anonyme,`annee` , `moyenne` , coeff, `delib1` ) (\n".
		"SELECT notes.id_table, notes.id_anonyme, notes.annee, if(min(note)<0,-1,ROUND(sum( coeff * note ) / sum( coeff ),$iPrecision)) moy,sum( coeff ) coeff , null \n".
		"FROM odb_notes notes $from_jury\n WHERE notes.annee = $annee and notes.type!='Divers' AND notes.type!='Oral' $where_jury\n GROUP BY notes.id_table, notes.annee \n);";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.moyenne = ROUND(moyenne,1) where moyenne<9 $where_jury and notes.annee=$annee";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.delib1 = 'Absent' WHERE moyenne = -1 $where_jury and notes.annee=$annee";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.delib1 = 'Ajourne' WHERE 0<=moyenne and moyenne < 5 $where_jury and notes.annee=$annee";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.delib1 = 'Refuse' WHERE 5<=moyenne and moyenne < 9 $where_jury and notes.annee=$annee";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.delib1 = 'Admissible' WHERE moyenne >= 9 $where_jury and notes.annee=$annee";
		//TODO rendre parametrable : on refuse les candidats ayant eu un 0 et moins de 10
		$tSql[]="update odb_decisions decis, odb_notes notes $from_jury\n SET delib1='Refuse'\n".
		"WHERE decis.delib1 = 'Admissible' AND decis.id_table = notes.id_table\n AND notes.note=0 AND notes.type!='Divers' AND notes.type!='Oral' AND moyenne<10\n".
		" AND decis.annee=$annee AND notes.annee=$annee $where_jury";
		foreach($tSql as $sql) {
			//echo "<pre>$sql</pre>\n";
			odb_query($sql,__FILE__,__LINE__);
		}
	} elseif($deliberation==2) {
		$sql="SELECT notes.id_table, note, id_matiere from odb_notes notes, odb_decisions decis $from_jury where decis.id_table=notes.id_table and delib2='' and notes.type='Divers' $where_jury";
		$result=odb_query($sql,__FILE__,__LINE__);
		while($row=mysql_fetch_array($result)) {
			foreach(array('id_matiere','id_table','note') as $col) $$col=$row[$col];
			$tNote[$id_table]+=$note;
			if($id_matiere==-3) $tEps[$id_table]=true;
		}
		if(is_array($tNote)) {
			foreach($tNote as $id_table=>$points) {
				if($tEps[$id_table]) $sql="UPDATE odb_decisions notes SET moyenne=ROUND((moyenne*coeff+$points)/(coeff+1),$iPrecision), coeff=coeff+1 where notes.id_table='$id_table'";
				else $sql="UPDATE odb_decisions notes SET moyenne=ROUND(moyenne+$points/coeff,$iPrecision) where notes.id_table='$id_table'";
				//echo "<br/>$sql";
				odb_query($sql,__FILE__,__LINE__);
			}
		}
		$tSql[]="UPDATE odb_decisions decis, odb_notes notes $from_jury SET decis.delib2 = 'Reserve'\n".
		" WHERE notes.id_table = decis.id_table $where_jury\n and decis.annee=$annee and notes.annee=$annee and notes.id_matiere=-3 and notes.note=0";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.delib2 = 'Oral'\n WHERE moyenne >= 9 and moyenne<10 AND notes.delib2!='Reserve' $where_jury and notes.annee=$annee";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.delib2 = 'Passable'\n WHERE moyenne >= 10 and moyenne<12 AND notes.delib2!='Reserve' $where_jury and notes.annee=$annee";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.delib2 = 'ABien'\n WHERE moyenne >= 12 and moyenne<14 AND notes.delib2!='Reserve' $where_jury and notes.annee=$annee";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.delib2 = 'Bien'\n WHERE moyenne >= 14 and moyenne<16 AND notes.delib2!='Reserve' $where_jury and notes.annee=$annee";
		$tSql[]="UPDATE odb_decisions notes $from_jury SET notes.delib2 = 'TBien'\n WHERE moyenne >= 16 AND notes.delib2!='Reserve' $where_jury and notes.annee=$annee";
		foreach($tSql as $sql) {
			odb_query($sql,__FILE__,__LINE__);
			//echo mysql_affected_rows()." lignes :<pre>$sql</pre>\n";
		}
	} elseif ($deliberation==3) {
		$sql="SELECT notes.id_table, note, id_matiere, notes.coeff\n from odb_notes notes, odb_decisions decis $from_jury\n where decis.id_table=notes.id_table and delib2='Oral' and delib3='-' and notes.type='Oral' $where_jury";
		//die("<pre>$sql");
		$result=odb_query($sql,__FILE__,__LINE__);
		$tCR=array();
		while($row=mysql_fetch_array($result)) {
			foreach(array('coeff','id_table','note') as $col) $$col=$row[$col];
			if($note<=0) {
				$tCR[$id_table]=true; // cas reserve
				$tNote[$id_table]+=0;
				$tCoeff[$id_table]+=$coeff;
				//echo "$id_table $note/20 $coeff - $tCoeff[$id_table] -  RESERVE<br/>";
			} else {
				$tCoeff[$id_table]+=$coeff;
				$tNote[$id_table]+=(int)$note*$coeff;
				//echo "$id_table $note/20 $coeff<br/>";
			}
			
			//echo "$id_table $note/20 $coeff - $tCoeff[$id_table]<br/>";
		}
		if(is_array($tNote)) {
			foreach($tNote as $id_table=>$points) {
				$coeff=$tCoeff[$id_table];
				if($tCR[$id_table]) {
					$sql="UPDATE odb_decisions notes SET delib3='Reserve', moyenne=ROUND((moyenne*coeff+$points)/(coeff+$coeff),$iPrecision), coeff=coeff+$coeff\n where notes.id_table='$id_table'";
					//echo "$id_table RESERVE<br/>";
				} else {
					$sql="UPDATE odb_decisions notes SET delib3='Passable', moyenne=ROUND((moyenne*coeff+$points)/(coeff+$coeff),$iPrecision), coeff=coeff+$coeff\n where notes.id_table='$id_table'";
				}
				//echo "<br/>$sql";
				odb_query($sql,__FILE__,__LINE__);
				
			}
		}
		$sql="UPDATE odb_decisions notes $from_jury SET delib3='Reserve'\n where delib2='Reserve' and delib1='Admissible' $where_jury";
		odb_query($sql,__FILE__,__LINE__);
		//echo mysql_affected_rows()." lignes :<pre>$sql</pre>\n";

	} else die(KO." - Deliberation inattendue : $deliberation");
}

// V&eacute;rifie si la valeur est dans le r&eacute;f&eacute;rentiel
// Sorties : ["isFound"] : odb_referentiel.[id|valeur] si trouv&eacute; (d&eacute;pend de $chercherValeurEtPasId), false sinon
//           ["valeursPossibles"] : valeurs possibles pour ce r&eacute;f&eacute;rentiel
function isInReferentiel ($valeurOuId,$referentiel,$chercherValeurEtPasId) {
   static $aErreurs = array();
   static $tab_referentiel = array();
   if(!isset($tab_referentiel['departements']))
      $tab_referentiel['departements']=getReferentiel('departement','tout');
   if(trim($referentiel)=="" || trim($chercherValeurEtPasId)=="")
      die ("Valeur/ID [$valeurOuId]<br/>R&eacute;f&eacute;rentiel [$referentiel]<br/>Chercher Valeur (et pas Id) [$chercherValeurEtPasId]\n");
   if(!is_array($tab_referentiel[$referentiel]))
      $tab_referentiel[$referentiel]=getReferentiel($referentiel,'tout');
   $isFound=false;
   //$valeurs_possibles="<A NAME='$referentiel".'_'.$valeurOuId."'></A>\n";
   if(!is_array($tab_referentiel[$referentiel]))
      $valeurs_possibles="<li>R&eacute;f&eacute;rentiel <b>[$referentiel]</b> introuvable</li>\n";
   else {
      foreach($tab_referentiel[$referentiel] as $valeurRef => $idRef) {
         if(!$aErreurs[$referentiel][$valeurOuId])
            if(!is_numeric($valeurRef)) {
               $valeurs_possibles.="\t<li>$valeurRef [$idRef]</li>\n";
               if($chercherValeurEtPasId) {
                  if(strtolower(supprimeAccents($valeurOuId))==strtolower(supprimeAccents($valeurRef)))
                     $isFound=$idRef;
               } else {
                  if($valeurOuId==$idRef)
                     $isFound=$valeurRef;
               }
            }
            elseif(is_array($idRef)) {
               //echo "<pre>$referentiel";print_r($tab_referentiel[$referentiel]);echo "</pre>";
               $id_dept=$valeurRef;
               $tab_la_valeur=$idRef;
               //foreach($tab_referentiel[$referentiel] as $id_dept => $tab_la_valeur) {
                  $valeurs_possibles .="</ul>\n&Eacute;tablissements <b>".$tab_referentiel['departements'][$id_dept]."</b> [$id_dept]<ul>\n";
                  foreach($tab_la_valeur as $la_valeur => $le_id) {
                     if(!is_numeric($la_valeur))
                        $valeurs_possibles.="\t<li>$la_valeur [$le_id]</li>\n";
                     if($chercherValeurEtPasId) {
                        //echo '<br/>'.strtolower(supprimeAccents($valeurOuId)).'='.strtolower(supprimeAccents($la_valeur));
                        if(strtolower(supprimeAccents($valeurOuId))==strtolower(supprimeAccents($la_valeur)))
                           $isFound=$le_id;
                     } else {
                        if($valeurOuId==$le_id)
                           $isFound=$la_valeur;
                     }
                  }
               //}
               //die("<ul>$valeurs_possibles</ul>");
            }
      }
   }
   if (!$isFound) {
      if(!$aErreurs[$referentiel][$valeurOuId]) {
         $aErreurs[$referentiel][$valeurOuId]=true;
      } else {
         $valeurs_possibles="Erreur d&eacute;j&agrave; rencontr&eacute;e : ".$referentiel
                           ."[<A HREF='#".$referentiel.'_'.$valeurOuId
                           ."'>$valeurOuId</A>]\n"
                           ;
      }
   }
   $isInRef["valeursPossibles"]=$valeurs_possibles;
   $isInRef["isFound"]=$isFound;
   return $isInRef;
}
 
/**
 * Cree les OPTIONS tirees du referentiel passe en parametre
 *
 * @param string $referentiel : referentiel
 * @param string $valeur_defaut : valeur selectionnee par defaut
 * @param string $label : devient $referentiel si vide
 * @param string $id_departement : obligatoire pour le referentiel centre ; recommande pour les referentiels etablissement, ville, deliberation
 * @return string : les champs OPTION
 */
function formOptionsRefInSelect ($referentiel,$valeur_defaut,$label='',$id_departement='') {
   //echo "$referentiel,$valeur_defaut,$label,$id_departement<hr/>";
   global $tab_referentiel__,$debug;

   static $pb = array();
   static $cpt_bidon=0;

   unset($tab_referentiel);
   if($referentiel=='centres') $tab_referentiel=getReferentiel('centre','id');
   elseif($referentiel=='villes') $tab_referentiel=getReferentiel('ville','id');
   elseif($referentiel=='etablissements') $tab_referentiel=getReferentiel('etablissement','id');
   else $tab_referentiel=getReferentiel($referentiel,'id');

   if($label=="") $label=ucwords($referentiel);

   switch(strtolower($referentiel)) {
      case 'etablissements':
      case 'centres':
      case 'villes':
         if($id_departement=='') if ($debug) echo "<u>/!\</u> id_departement n'a pas t saisi pour un $referentiel  l'appel de ".__FUNCTION__;
         //echo'<hr/><pre>tab_referentiel';print_r($tab_referentiel);echo'</pre>';
         $ref_dept=getReferentiel('departement','id');
         ksort($ref_dept);
         //echo'<hr/><pre>ref_dept';print_r($ref_dept);echo'</pre>';
         $str.=formOptionsInSelect("-=[$label]=-",0,$valeur_defaut);
         foreach($ref_dept as $dept => $id_dept) {
            if(is_string($dept)) {
               $str.="<optgroup label='$dept'>\n";
               ksort($tab_referentiel[$id_dept]);
               foreach($tab_referentiel[$id_dept] as $valeur => $id) {
                  if(is_string($valeur))
                     $str.=formOptionsInSelect($valeur,$id,$valeur_defaut);
               }
            }
         }
         break;
      case 'etablissement':
      	if($id_departement=='') die(KO." <b>id_departement</b> n'a pas t saisi pour un <b>&eacute;tablissement</b> &agrave; l'appel de <i>".__FUNCTION__.'</i>');
      	$isDepartement=true;
      case 'centre':
      	if(!$isDepartement) {
				if($id_departement=='') die(KO." <b>id_departement</b> n'a pas t saisi pour un <b>centre</b> &agrave; l'appel de <i>".__FUNCTION__.'</i>');
				$isCentre=true;
			}
      case 'ville':
         if($id_departement=='') if ($debug) echo "<u>/!\</u> id_departement n'a pas t saisi pour une <b>ville</b> &agrave; l'appel de ".__FUNCTION__;
         $isDepartement=true;
         $tab_ref=$tab_referentiel[$id_departement];
         if(count($tab_ref)==0) {
            if(!$pb[$referentiel]) {
               $pb[$referentiel]=true;
               $msg= "Aucun(e) <b>$label</b> n'a &eacute;t&eacute; trouv&eacute;(e) dans le contexte, <br/><i>Conseil : </i>\n";
               if($referentiel=='ville') {
                  $msg.= "Modifiez le <A HREF='".generer_url_ecrire('odb_ref')."&table=odb_ref_ville&step2=manuel'>r&eacute;f&eacute;rentiel <b>$label</b></a><br/>\n";
               } elseif($referentiel=='centre') {
                  $msg.= "Commencez par attribuer des centres en choisissant une ann&eacute;e pour chaque &eacute;tablissement qui est centre de composition<br/>";
               } else {
                  $msg.= "Commencez par r&eacute;soudre ce probl&egrave;me<br/>";
               }
               echo boite_important($msg,'<!>');
            }
         //echo'<pre>';print_r($tab_referentiel);echo'</pre>';
         } else {
            unset($tab_referentiel);
            $tab_referentiel=$tab_ref;
            if($cpt_bidon++ <5) {
               //echo "<pre>--- tab_referentiel $referentiel $valeur_defaut ---";print_r($tab_referentiel);echo '</pre>';
            }
         }
      default:
         ksort($tab_referentiel);
         //echo"<pre>";print_r($tab_referentiel);echo"</pre>";
         $str.=formOptionsInSelect("-=[$label]=-",0,$valeur_defaut);
         if(count($tab_referentiel>20)) $estGrand=true; else $estGrand=false;
         $formSelect='';
         foreach($tab_referentiel as $valeur => $id) {
         	if(strtolower(supprimeAccents($valeur))==PAYS) $str.=formOptionsInSelect($valeur,$id,$valeur_defaut);
            if(is_string($valeur)) {
               if($estGrand) {
                  $lettre=$valeur[0];
                  if($lettre!=$oldLettre)
                     $formSelect.="</optgroup>\n<optgroup label='".strtoupper($lettre)."'>\n";
               }
               if(strlen($valeur)>29) $valeur_aff=substr($valeur, 0, 29).'...';
               else $valeur_aff=$valeur;
               $formSelect.=formOptionsInSelect($valeur_aff,$id,$valeur_defaut);
               $oldLettre=$valeur[0];
            }
         }
         
         $str.=$formSelect;
   }
   return $str;
   //echo "<hr>$str</hr>";
}

// cree une ligne (2 colonnes) avec liste provenant du r&eacute;f&eacute;rentiel
function formSelectRefInTR($label,$name,$referentiel,$valeur_defaut,$html="",$id_departement='') {
   $str=formSelectTR1($label,$name,$html);
   if($referentiel=="") die (KO." - Param&egrave;tre r&eacute;f&eacute;rentiel mal pass&eacute;  ".__FUNCTION__." dans ".__FILE__." ligne ".__LINE__);
   $str.=formOptionsRefInSelect(strtolower($referentiel),$valeur_defaut,$label,$id_departement);
   $str.=formSelectTR2();
 
   return $str;
}

/**
 * cree une liste SELECT a partir des resultats d'une requete SQL pour un referentiel donne
 *
 * @param string $label : libelle du 1er OPTION
 * @param string $name
 * @param string $sql : requete a executer
 * @param string $myChamp : nom du champ (colonne SQL) a afficher
 * @param string $referentiel : referentiel dans lequel recuperer le texte a afficher dans la liste pour l'id donne
 * @param string $id_defaut : identifiant a selectionner par defaut
 * @param string $html : code HTML optionnel
 * @return string
 */
function formSelectQueryRef($label, $name, $sql, $myChamp, $referentiel, $id_defaut, $html='') {
   $sqlRef="SELECT id, $referentiel from odb_ref_$referentiel order by $referentiel";
   $result=odb_query($sqlRef,__FILE__,__LINE__);
   $tab_ref=array();
   while($row = mysql_fetch_array($result)) {
      $tab_ref[$row['id']]=$row[$referentiel];
   }

   $str='';
   $str.="<SELECT NAME='$name' $html>\n";
   $selected=(trim($id_defaut)=='')?'SELECTED':'';
   $str.="<OPTION VALUE='' $selected>-=[$label]=-</OPTION>\n";
   $result=odb_query($sql,__FILE__,__LINE__);
   while($row = mysql_fetch_array($result)) {
      $id=$row[$myChamp];
      $champ=$tab_ref[$id];
      $selected=($id==$id_defaut)?'SELECTED':'';
      $str.= "<OPTION VALUE='$id' $selected>$champ</OPTION>\n";
   }
   $str.="</SELECT>\n";
   return $str;
}

/**
 * cree une liste SELECT a partir des resultats d'une requete SQL
 *
 * @param string $label : libelle du 1er OPTION
 * @param string $name
 * @param string $sql : requete a executer (doit comporter un champ id et un champ $myChamp
 * @param string $myChamp : nom du champ (colonne SQL) a afficher
 * @param string $id_defaut : identifiant a selectionner par defaut
 * @param string $html : code HTML optionnel
 * @param string $myValue : nom du champ a mettre dans le value 
 * @return string
 */
function formSelectQuery($label, $name, $sql, $myChamp, $defaut, $html='', $myValue='') {
   $str='';
   $str.="<SELECT NAME='$name' $html>\n";
   $selected=(trim($defaut)=='')?'SELECTED':'';
   $str.="<OPTION VALUE='' $selected>-=[$label]=-</OPTION>\n";
   $result=odb_query($sql,__FILE__,__LINE__);
   while($row = mysql_fetch_array($result)) {
      $champ=$row[$myChamp];
      if($myValue=='') $value=$champ;
      else $value=$row[$myValue];
      $selected=($value==$defaut)?'SELECTED':''; 
      $str.= "<OPTION VALUE='$value' $selected>$champ</OPTION>\n";
   }
   $str.="</SELECT>\n";
   return $str;
}

//affiche les raccourcis ODB (attention : ecrit directement dans le flux cause spip)
function odb_raccourcis($ref_courrant) {
   debut_raccourcis();
		if($ref_courrant!="odb_ref")
			icone_horizontale (_L('R&eacute;f&eacute;rentiel ODB'), generer_url_ecrire("odb_ref"), "../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png");
		if($ref_courrant!="odb_saisie")
			icone_horizontale (_L('Saisie ODB'), generer_url_ecrire("odb_saisie"), "../"._DIR_PLUGIN_ODB_SAISIE."/img_pack/siou_carre.png");
		if($ref_courrant!="odb_repartition")
			icone_horizontale (_L('R&eacute;partition candidats'), generer_url_ecrire("odb_repartition"), "../"._DIR_PLUGIN_ODB_REPARTITION."/img_pack/siou_carre.png");
   fin_raccourcis();

}

/**
 * Introspection du referentiel pour l'annee specifiee
 * Consiste a verifier l'integrite du referentiel
 * Doit etre lancee depuis une page qui prevoit l'utilisation des sessions, telle que odb_param
 * 
 * @param string $annee : annee a introspecter
 * @return int : nbErreurs recensees et affichees
 */
function odb_introspection($annee) {
   global $tab_referentiel;
   if(in_array(getStatutUtilisateur(),array('Admin','Encadrant'))) 
   	$whereStatut='';
   else 
   	$whereStatut=" AND login='".$GLOBALS['auteur_session']['login']."'";
   
   $nbErreurs=0;
   //mettre annee en cours si annee nulle
   $sql="select count(*) from odb_candidats where annee=0 $whereStatut";
   $result=odb_query($sql,__FILE__,__LINE__);
   $row=mysql_fetch_array($result);
   $nb_rows=(int)$row[0];
   if($num_rows>0) {
      $sql="UPDATE odb_candidats SET annee=$annee WHERE annee=0 $whereStatut";
      $result=odb_query($sql,__FILE__,__LINE__);
      echo boite_important("<b>$num_rows</b> candidats avaient une ann&eacute;e incorrecte, remplac&eacute;e par <b>$annee</b>");
   }

   //affecter departement lorsque departement nul
   $sql="SELECT id_saisie, ville, etablissement from odb_candidats where annee=$annee and departement=0  $whereStatut order by ville, id_saisie";
   $result=odb_query($sql,__FILE__,__LINE__);
   $num_rows=mysql_num_rows($result);
   $avertissement=array();
   $cpt_avertissement=0;
   unset($tab_res);
   if($num_rows>0) {
   	echo "\n<!-- DEPARTEMENT NUL -->\n";
      while($row=mysql_fetch_array($result)) {
         $resultats=array('id_saisie','ville','etablissement');
         foreach($resultats as $col) $$col=$row[$col];

         //trouver le departement
         if(strlen($tab_res['ville'][$ville])==0) {
            // on n'a pas encore identifie le departement de cette ville ou il est different de celui de l'etablissement
            foreach($tab_referentiel['ville'] as $dept_ville => $tab_ville) {
               if(isset($tab_ville[$ville])) {
                  // la ville existe dans le referentiel de ce departement
                  $tab_res['ville'][$ville]=$dept_ville;
                  $departement=$dept_ville;
                  if(!isset($tab_referentiel['etablissement'][$dept_ville][$etablissement])) {
                     if(!isset($tab_res['eta'][$etablissement]))
                        foreach($tab_referentiel['etablissement'] as $dept_eta => $tab_eta) {
                           if(isset($tab_eta[$etablissement])) {
                              $tab_res['eta'][$etablissement]['dept']=$tab_referentiel['departement'][$dept_eta];
                              $tab_res['eta'][$etablissement]['eta']=$tab_eta[$etablissement];
                           }
                        }
                     $cpt_avertissement++;
                     unset($tab_res['ville'][$ville]); // $dept_ville!=$dept_eta donc on refera le test prochaine fois
                     $departement=0; // le departement reste inconnu
                     $avertissement[$cpt_avertissement]="<td><A HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee&step2=odb_candidats&identifiant=id_saisie&id=$id_saisie'>$id_saisie</A></td><td>".$tab_referentiel['ville'][$dept_ville][$ville]." (<b>".$tab_referentiel['departement'][$dept_ville]."</b>)</td><td>".$tab_res['eta'][$etablissement]['eta']." (<b>".$tab_res['eta'][$etablissement]['dept']."</b>)</td>";
                  }
               }
            }
         } else {
            // on a deja identifie ce departement et dept_ville==dept_eta
            $departement=$tab_res['ville'][$ville];
         }
         $sql="UPDATE odb_candidats SET departement=$departement WHERE id_saisie=$id_saisie $whereStatut";
         if($departement>0)
            odb_query($sql,__FILE__,__LINE__);
      }
      $nbErreurs+=$num_rows;

      $num_rows=($num_rows-$cpt_avertissement);
      $msg="<b>$num_rows</b> candidats contenaient un d&eacute;partement invalide et ont &eacute;t&eacute; corrig&eacute;s";
      if(count($avertissement)>0) {
      	print_r($avertissement);
         $titre="<b>$cpt_avertissement erreurs graves</b> n'ont pas &eacute;t&eacute; corrig&eacute;s (ville et &eacute;tablissement dans d&eacute;partements diff&eacute;rents)";
         $thead="<th><small>Candidat</small></th><th><small>Ville (d&eacute;partement)</small></th><th><small>&Eacute;tablissement (d&eacute;partement)</small></th>";
         $msg.=odb_html_table($titre,$avertissement,$thead);
		}

      echo boite_important("<span>$msg</span>");
   }

   // verification referentiels (identifiants >0)
   foreach(array('departement','ville','etablissement','serie','sexe','nationalite','ldn','pdn','nom','prenoms','eps') as $ref) {
      unset($lien);
      $sql="SELECT id_saisie from odb_candidats where ($ref='0' or $ref='') and annee=$annee $whereStatut";
      //echo "$sql<br>";
      $result=odb_query($sql,__FILE__,__LINE__);
      $num_rows=mysql_num_rows($result);
      if($num_rows>0) {
         while($row=mysql_fetch_array($result)) {
            $id_saisie=$row['id_saisie'];
            $lien[]="<A HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee&step2=odb_candidats&identifiant=id_saisie&id=$id_saisie'>$id_saisie</A>\n";
         }
         $liens=implode(', ',$lien);

         echo boite_important("Le champ <b>$ref</b> est invalide pour <b>$num_rows</b> candidat(e)s.<br/><small>Cliquez sur leur num&eacute;ro de saisie pour effectuer la correction :<br/>$liens</small>");
      }
   }
   $nbErreurs+=$num_rows;

   // Passer les id_table inexistants à 0
      unset($lien);
      $sql="SELECT count(*) from odb_candidats where id_table='' and annee=$annee $whereStatut";
      //echo "$sql<br>";
      $result=odb_query($sql,__FILE__,__LINE__);
      $num_rows=mysql_result($result,0,0);
      if($num_rows>0) {
         $sql="UPDATE odb_candidats set id_table='0' where id_table='' $whereStatut";
         odb_query($sql,__FILE__,__LINE__);
         echo boite_important("Le num&eacute;ro de table &eacute;tait invalide pour <b>$num_rows</b> candidat(e)s et a &eacute;t&eacute; automatiquement r&eacute;initialis&eacute;");
      }
   $nbErreurs+=$num_rows;

   // verification doublons
   $sql="SELECT nom, prenoms, ne_le, ne_en, ne_vers, count( prenoms ) AS nb\n"
         . " FROM odb_candidats\n"
         . " WHERE annee=$annee $whereStatut\n"
         . " GROUP BY nom, prenoms, ne_le, ne_en, ne_vers\n"
         . " ORDER BY nb DESC, nom ASC, prenoms ASC "
         ;
   $result=odb_query($sql,__FILE__,__LINE__);
   $doublons="";
   $cpt_types_doublons=0;
   $cpt_nb_doublons=0;
   while($row=mysql_fetch_array($result)) {
      $nb=$row['nb'];
      if($nb>1) {
         $cpt_types_doublons++;
         $cpt_nb_doublons+=$nb;
         //$doublons.="<tr class='tr_liste'>";
         $sql2="SELECT id_saisie\n from odb_candidats\n where annee=$annee $whereStatut";
         foreach(array('nom','prenoms','ne_le','ne_en','ne_vers') as $col) {
            $$col=$row[$col];
            $doublons[$cpt_types_doublons].="<td>".$$col."</td>";
            $sql2.=" AND $col='".addslashes($$col)."'";
         }
         $doublons[$cpt_types_doublons].="<td><small>$nb : </small>";
         $result2=odb_query($sql2,__FILE__,__LINE__);
         unset($doublon);
         $cpt=0;
         while($row2=mysql_fetch_array($result2)) {
         	$id_saisie=$row2['id_saisie'];
            $doublon[]="<A HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee&step2=odb_candidats&identifiant=id_saisie&id=".$row2['id_saisie']."'>$id_saisie</A>";
            if($cpt++>0) $doublons_auto[]=$id_saisie;
         }
         if(is_array($doublon))
         	$doublons[$cpt_types_doublons].=implode(", ", $doublon);
         $doublons[$cpt_types_doublons].="</td>";
      }
   }
   if(is_array($doublons)) {
      $cpt_nb_doublons-=$cpt_types_doublons;
      $msg="<span>Les <b>$cpt_nb_doublons</b> doublons suivants ont &eacute;t&eacute; d&eacute;tect&eacute;s.";
      $titre="Veuillez corriger <b>le premier de chaque liste svp</b>";
      $thead="<th><small>Nom</small></th><th><small>Pr&eacute;noms</small></th><th><small>N&eacute;(e) le</small></th><th><small>N&eacute;(e) en</small></th><th><small>N&eacute;(e) vers</small></th><th><small>N&deg; saisie</small></th>";
      $msg.=odb_html_table($titre,$doublons,$thead);
      
      if(isAdmin())
         $msg.= "<small><dl id='doublons' style='cursor:help;'><dt>Correction automatique (pour l'administrateur)</dt><dd><i>Supprimer les id_saisie suivants</i><br/><pre>DELETE from odb_candidats WHERE annee=$annee AND id_saisie in(</pre>".implode(', ',$doublons_auto)."<br/>)</dd></dl></small>"
				. "</span>\n"
				;
      echo boite_important($msg);
      $nbErreurs+=$cpt_nb_doublons;
      	$jquery= <<<FINSCRIPT
$(document).ready(function() {
	$('#doublons').find('dd').hide().end().find('dt').click(function() {
		var suivant = $(this).next();
		suivant.slideToggle();
	});
});
FINSCRIPT;
		echo putJavascript($jquery);
   }

   //Verification ville etablissement !=0
   $sql = 'SELECT eta . id , etablissement , departement FROM odb_ref_etablissement eta , odb_ref_departement dep '
        . ' WHERE id_ville = 0 '
        . ' and dep . id = eta . id_departement'
        . ' order by departement, etablissement'
        ;
   $result=odb_query($sql,__FILE__,__LINE__);
   $nb_rows=mysql_num_rows($result);
   if($nb_rows>0) {
      unset($tr);
      $thead="<th>D&eacute;partement</th><th>&Eacute;tablissement</th>";
      while($row=mysql_fetch_array($result)) {
         $id=$row['id'];
         $eta=$row['etablissement'];
         $dep=$row['departement'];
         $tr[]="<td><small>$dep</small></td><td><small>$eta (#$id)</small></td>";
      }
      $msg=odb_html_table("Les <b>$nb_rows</b> <b>&eacute;tablissements</b> suivants ont une <b>ville</b> invalide",$tr,$thead);
      echo "$msg<br/>";
   }
   $nbErreurs+=$num_rows;

   //Verification departement candidat = departement de son etablissement
   $sql = 'select id_saisie, id_table, eta.etablissement, depcan.departement deptCan, depeta.departement deptEta'
        . ' from odb_ref_etablissement eta, odb_candidats can, odb_ref_departement depcan, odb_ref_departement depeta'
        . ' where eta.id=can.etablissement'
        . ' and depeta.id=eta.id_departement'
        . ' and depcan.id=can.departement'
        . ' and can.departement!=eta.id_departement'
        . " and can.annee=$annee"
        . $whereStatut
        ;
   $result=odb_query($sql,__FILE__,__LINE__);
   $nb_rows=mysql_num_rows($result);
   $colonnes=array('id_saisie','id_table','etablissement','deptCan','deptEta');
   if($nb_rows>0) {
      unset($tr);
      $cpt=0;
      $thead="<th><small>#Saisie</small></th><th><small>#Table</small></th><th><small>&Eacute;tablissement</small></th><th><small>D&eacute;partement<br>candidat</small></th><th><small>D&eacute;partement<br>&eacute;tabissement</small></th>";
      while($row=mysql_fetch_array($result)) {
         foreach($colonnes as $col)
            $$col=$row[$col];
         $id_saisie="<A HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee&step2=odb_candidats&identifiant=id_saisie&id=$id_saisie'>$id_saisie</A>";
         $cpt++;
         foreach($colonnes as $col)
            $tr[$cpt].="<td><small>".$$col."</small></td>";
         //$_SESSION['sql']['maj_departements'][]="UPDATE odb_candidats SET = WHERE = AND etablissement=$id_eta AND annee=$annee;\n";
      }
      $msg=odb_html_table("Les <b>$nb_rows</b> candidats suivants ont un <b>d&eacute;partement</b> invalide et doivent &ecirc;tre corrig&eacute;s",$tr,$thead)
         ;
      echo $msg;
   }
   $nbErreurs+=$num_rows;

   //Verification ville candidat = ville de son etablissement
   $sql = 'SELECT dep.departement, eta.etablissement, eta.id id_etablissement, can.ville id_ville_can ,eta.id_ville id_ville_eta, vilcan.ville ville_candidat, vileta.ville ville_eta, count(*) nb'
        . ' FROM odb_candidats can, odb_ref_etablissement eta, odb_ref_ville vilcan, odb_ref_departement dep, odb_ref_ville vileta'
        . ' WHERE eta.id = can.etablissement'
        . ' AND dep.id = can.departement'
        . ' AND vilcan.id = can.ville'
        . ' AND vileta.id = eta.id_ville'
        . ' AND can.ville <> eta.id_ville'
        //. " and id_table!='0'"
        . " AND eta.etablissement not like 'CL%'"
        . $whereStatut
        . ' group by departement, etablissement, ville_candidat, ville_eta'
        . ' order by departement, etablissement, ville_candidat, ville_eta, id_table'
        ;
   $result=odb_query($sql,__FILE__,__LINE__);
   $nb_rows=mysql_num_rows($result);
   $colonnes=array('departement','etablissement','ville_candidat','ville_eta','serie','nb');
   if($nb_rows>0) {
      unset($tr);
      $cpt=0;
      $thead="<th><small>D&eacute;partement</small></th><th><small>&Eacute;tablissement</small></th><th><small>Ville<br>candidat</small></th><th><small>Ville<br>&eacute;tabissement</small></th><th><small>S&eacute;rie</small></th><th><small>Nombre</small></th>";
      while($row=mysql_fetch_array($result)) {
         $id_ville_can=$row['id_ville_can'];
         $id_ville_eta=$row['id_ville_eta'];
         $id_eta=$row['id_etablissement'];
         foreach($colonnes as $col)
            $$col=$row[$col];
         if(substr_count(strtolower($etablissement),'cl')==0) {
            // ce n'est pas un candidat libre
            $cpt++;
            foreach($colonnes as $col)
               $tr[$cpt].="<td><small>".$$col."</small></td>";
            $_SESSION['sql']['maj_villes'][]="UPDATE odb_candidats SET ville=$id_ville_eta WHERE ville=$id_ville_can AND etablissement=$id_eta AND annee=$annee $whereStatut;\n";
            //echo "<br>$cpt : $sql";
            //odb_query($sql,__FILE__,__LINE__);
         } else $nb_rows--;
      }
      $msg=odb_html_table("Les candidats des <b>$nb_rows</b> lignes suivantes avaient une <b>ville</b> invalide et peuvent &ecirc;tre corrig&eacute;s",$tr,$thead);
      if(isAdmin()) $msg.= "<A HREF='".generer_url_ecrire('odb_param')."'>Cliquez ici pour les corriger</A> <small>(vous devez le faire depuis le module <A HREF='".generer_url_ecrire('odb_param')."'>param&egrave;tres</A>)</small><br/>";
      echo $msg;
   }
   $nbErreurs+=$nb_rows;

   //Verification langues (series A1, A2, B)
   $sql="SELECT id_saisie from odb_candidats where annee=$annee $whereStatut and (0";
   $lien=array();
   foreach($tab_referentiel['serie'] as $idSerie=>$refSerie) {
      if($refSerie=='A1' || $refSerie=='A2' || $refSerie=='B')
         $sql.=" OR serie=$idSerie";
   }
   $sql.=") and (lv1=0 or lv2=0 or lv1=lv2) order by id_saisie";
   $result=odb_query($sql,__FILE__,__LINE__);
   $nb_rows=mysql_num_rows($result);
   if($nb_rows>0) {
      while($row=mysql_fetch_array($result)) {
         $id_saisie=$row['id_saisie'];
         $lien[]="<A HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee&step2=odb_candidats&identifiant=id_saisie&id=$id_saisie'>$id_saisie</A>";
      }
      $liens=implode(', ',$lien);
      $msg="Les <b>$nb_rows</b> candidats de s&eacute;rie A1, A2 ou B suivants n'ont pas 2 langues vivantes diff&eacute;rentes."
         . "<br/><small>Cliquez sur leur numro de saisie pour effectuer la correction :<br/>$liens</small>\n"
         ;
      echo boite_important($msg);
   }
   $nbErreurs+=$nb_rows;

   //Verification annee de naissance
   $sql = 'select id_saisie, year( ne_le ) + CAST( ne_en AS unsigned ) + CAST( ne_vers AS unsigned ) ann '
        . ' from odb_candidats'
        . " where annee=$annee $whereStatut and (year( ne_le ) + CAST( ne_en AS unsigned ) + CAST( ne_vers AS unsigned )=0 "
        . ' or year( ne_le ) + CAST( ne_en AS unsigned ) + CAST( ne_vers AS unsigned )>year(now())-10)'
        . ' order by id_saisie'
        ;
   $result=odb_query($sql,__FILE__,__LINE__);
   $nb_rows=mysql_num_rows($result);
   if($nb_rows>0) {
      while($row=mysql_fetch_array($result)) {
         $id_saisie=$row['id_saisie'];
         $lien[]="<A HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee&step2=odb_candidats&identifiant=id_saisie&id=$id_saisie'>$id_saisie</A>";
      }
      $liens=implode(', ',$lien);
      $msg="Les <b>$nb_rows</b> candidats suivants ont une <b>date de naissance</b> invalide :<br/>"
         . "<small>Cliquez sur leur numro de saisie pour effectuer la correction :<br/>$liens</small>\n"
         ;
      echo boite_important($msg);
   }
   $nbErreurs+=$num_rows;

   //synchro id_table
   $sql = 'update odb_candidats can, odb_repartition rep'
        . ' set can.id_table=rep.id_table'
        . ' where can.id_saisie=rep.id_saisie'
        . " and can.annee=$annee"
        . " and rep.annee=$annee"
        . $whereStatut
        ;
   odb_query($sql,__FILE__,__LINE__);
   $num_rows=mysql_affected_rows();
   if($num_rows>0)
      echo boite_important("La synchronisation des num&eacute;ros de table &eacute;tait incorrecte pour <b>$num_rows</b> candidats");
   $nbErreurs+=$num_rows;

   return $nbErreurs;
}

?>
