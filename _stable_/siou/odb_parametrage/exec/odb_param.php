<?php
session_start();
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');
define('DIR_ODB_COMMUN',_DIR_PLUGINS.'odb/odb_commun/');
define('DIR_ODB_CONTRIB',_DIR_PLUGINS.'odb/odb_contrib/');
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN.'inc-odb.php');

setlocale(LC_TIME, "fr_FR");

global $debug;
$debug=false;

define(OK,"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define(KO,"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

//trouve le prochain élément d'un tableau
function trouveProchain($val,$tableau) {
   //echo "<hr>";
   reset($tableau);
   asort($tableau);
   //echo "<br />$val : ".join('| ',$tableau)."<br />";
   foreach($tableau as $v)
      if($v>$val) 
         return $v;
}

// exécuté automatiquement par le plugin au chargement de la page ?exec=odb_repartition
function exec_odb_param() {
global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping;
$annee=$_REQUEST['annee'];
if($annee=='') $annee=date("Y");

include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");

if(isset($_POST['ok'])) {
   foreach($_POST as $key=>$val) {
      if(substr_count($key,'set|')>0) {
         $tmp=explode('|',$key);
         $param=$tmp[1];
         $tSql[]="UPDATE odb_param SET valeur=COMPRESS('$val') WHERE param='$param'";
      }
   }
   foreach($tSql as $sql)
      odb_query($sql,__FILE__,__LINE__);
}elseif(isset($_POST['deliberer'])) {
	for($delib=1;$delib<=1;$delib++) { 
		foreach($_POST as $key=>$val) {
			if(substr_count($key,"delib$delib")>0) {
				$tTmp=explode('|',$key);
				$jury=$tTmp[1];
				$tDelib[$delib][$jury]=$jury;
				if(leveeAnonymat($annee,$jury)) $tJurysOK[]=$jury;
			}
		}
		$sql="REPLACE into odb_param (param, valeur) VALUES ('_delib".$delib."_$annee',COMPRESS('".serialize($tDelib[$delib])."'))";
		//echo $sql;
		odb_query($sql,__FILE__,__LINE__);
		$msg.='<small>'.OK." - </b> Modifications enregistr&eacute;es pour la 1<sup>&egrave;re</sup> d&eacute;lib&eacute;ration $annee<br/>";
		if(count($tJurysOK)>0) {
			asort($tJurysOK);
			$msg.=OK.' - Anonymat lev&eacute; pour les jurys '.join(', ',$tJurysOK)."</small><br/>\n";
			odb_maj_decisions($annee);
		}
		$msg.="</small>\n";
	}
}

$tParam=getParametresODB();
//echo("<pre>");print_r($tParam);echo"</pre>";
echo "<SCRIPT SRC='".DIR_ODB_CONTRIB."boxover/boxover.js'></SCRIPT>\n";
$imgInfo="<img src='".DIR_ODB_CONTRIB."boxover/info.gif' style='vertical-align:middle'>";

$array_ref=array('departement','etablissement','ef','lv','eps','prefixe','serie','sexe','ville','pays');
foreach($array_ref as $ref)
   $tab_referentiel[$ref]=getReferentiel($ref,'tout');

debut_page(_T('Param&egrave;tres'), "", "");
echo "<br /><br />";
gros_titre(_T('Office Du Baccalaur&eacute;at'));
$tab_auteur=$GLOBALS["auteur_session"];

if ($debug) {
   echo "<A HREF='#fin_debug'>Sauter les infos de debug</A>\n";
   echo "_POST<pre style='text-align:left;'>";
   print_r($_POST);
   echo "</pre><hr/>";
   echo "<A NAME='fin_debug'></A>\n";
}

debut_cadre_relief( "", false, "", $titre = _T('Param&eacute;trage de SIOU'));
//debut_boite_info();
echo '<br>';

$REFERER=$_SERVER['HTTP_REFERER'];
$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];

echo "<IMG SRC='"._DIR_PLUGIN_ODB_REPARTITION."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";

isAutorise(array('Admin'));

if(isset($_GET['reset'])) session_destroy();
//////////////////////////////////////////////// step 2 : session
if(is_array($_SESSION['sql'])) {
   debut_boite_info();
   echo "<h1>Requ&ecirc;te(s)</h1>\n"
      . "En cas de probl&egrave;me : <A HREF='".generer_url_ecrire('odb_param')."&reset'>d&eacute;truire la session</A>\n";
   foreach($_SESSION['sql'] as $nom=>$requetes) {
      echo "<h2>Requ&ecirc;te(s) ".ucfirst(str_replace('_',' ',$nom))."</h2>";
      foreach($requetes as $requete) {
         $cpt_r++;
         odb_query($sql,__FILE__,__LINE__);
      }
      echo OK." - <b>$cpt_r</b> requ&ecirc;te(s) ex&eacute;cut&eacute;e(s) avec succ&egrave;s";
   }
   unset($_SESSION['sql']);
   fin_boite_info();
}
//////////////////////////////////////////////// step 1 : formulaire de parametres
$annee=isset($_REQUEST['annee'])?$_REQUEST['annee']:date("Y");
$serie=isset($_REQUEST['serie'])?$_REQUEST['serie']:0;
$etablissement=isset($_REQUEST['etablissement'])?$_REQUEST['etablissement']:0;
$suppr_num_table=isset($_REQUEST['suppr_num_table'])?$_REQUEST['suppr_num_table']:'';
$centre=isset($_REQUEST['centre'])?$_REQUEST['centre']:0;
$vider=isset($_REQUEST['vider'])?$_REQUEST['vider']:'';
$reset=isset($_REQUEST['reset'])?$_REQUEST['reset']:'';
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
if($msg=='') $msg="Choisissez une action ci-dessous";

if($vider!='') {
   switch($vider) {
      case 'odb_repartition' :
         $msg=OK.' - Les num&eacute;ros de table ont &eacute;t&eacute; r&eacute;initialis&eacute;s';
         if($suppr_num_table!='') {
            $tNumTable=explode("\n",$suppr_num_table);
            foreach($tNumTable as $id_table) {
               $tNumTable2[]="'".trim($id_table)."'";
            }
            $andNumTable='and id_table in ('.implode(',',$tNumTable2).')';
            $post_sql[]="update odb_candidats set id_table='0' where annee=$annee $andNumTable";
         } else $andNumTable='';
         if($serie>0) $andSerie=" and id_saisie in ("
            . "select id_saisie from odb_candidats can"
            . " where annee=$annee"
            . " and serie = $serie"
            . " and id_table<>'0'"
            . ")"
            ;
         else $andSerie='';
         if($etablissement>0) $andEtablissement=" and id_saisie in ("
            . "select id_saisie from odb_candidats can"
            . " where annee=$annee"
            . " and can.etablissement = $etablissement"
            . " and id_table<>'0'"
            . ")"
            ;
         if($centre>0) $andCentre=" and id_etablissement=$centre";
         else $andCentre='';
         $post_sql[]="update odb_candidats set id_table='0' where annee=$annee";
         $post_sql[]="update odb_candidats can, odb_repartition rep"
                     ." set can.id_table = rep.id_table"
                     ." where can.id_saisie = rep.id_saisie"
                     ." and can.annee = $annee"
                     ." and rep.annee = $annee"
                     ;
         break;
   }
   $sql="DELETE FROM $vider where annee=$annee $andSerie $andEtablissement $andCentre $andNumTable";
   //die('<pre>'.$sql.'</pre>');
   odb_query($sql,__FILE__,__LINE__);
   $nb_rows=mysql_affected_rows(); // on force nb de lignes affectées au nb de num table supprimes
   foreach($post_sql as $sql)
      odb_query($sql,__FILE__,__LINE__);
   
   $gauche="<hr size=1/>".ucfirst($vider);
}
if($reset!='') {
   switch($reset) {
      case 'departement' :
         $msg=OK.' - Les d&eacute;partements ont &eacute;t&eacute; r&eacute;initialis&eacute;s';
         $sql="update odb_candidats set departement=0 where annee=$annee";
         break;
   }
   odb_query($sql,__FILE__,__LINE__);
   $gauche="<hr size=1/>".ucfirst(str_replace('_',' ',$reset));
}
if($action!='')
   $gauche="<hr size=1/>".ucfirst(str_replace('_',' ',$action));

if((int)$nb_rows==0) $nb_rows=mysql_affected_rows();
if($nb_rows>0) $gauche.="<hr size=1/><b>$nb_rows</b> enregistrements ont &eacute;t&eacute; affect&eacute;s";
debut_gauche();
   debut_boite_info();
      echo "<b>Param&eacute;trage de SIOU</b>$gauche";
   fin_boite_info();
   odb_raccourcis('');
creer_colonne_droite();
debut_droite();
   debut_cadre_relief("", false, "", $titre = _T("Param&eacute;trage SIOU"));
      if($action!='') {
         switch($action) {
            ///////////////////////////////////////////////////////////////////////
            case 'introspection' :
               $nbErreurs=odb_introspection($annee);
               if($nbErreurs>0)
                  $msg=KO." - SIOU a d&eacute;tect&eacute; <b>$nbErreurs</b> erreurs et les a signal&eacute;es, veuillez les corriger svp.";
               else
                  $msg=OK.' - R&eacute;f&eacute;rentiel SIOU introspect&eacute;';
               break;
            ///////////////////////////////////////////////////////////////////////
            case 'supprimer_notes':
               $idTables=explode("\n",$_POST['suppr_num_table_notes']);
               if(count($idTables>1))
               	foreach($idTables as $k=>$id_table) $idTables[$k]=trim($id_table);
               //else echo "idTables $idTables";
               $suppr_id_matiere=$_POST['id_matiere'];
               $suppr_matiere=$_POST['matiere'];
               $suppr_id_table=$_POST['id_table'];
               if($suppr_id_table!='') {
               	$sql="DELETE FROM odb_notes WHERE annee=$annee and id_table='$suppr_id_table' and id_matiere='$suppr_id_matiere'";
               	odb_query($sql,__FILE__,__LINE__);
               	if(in_array(strtolower($suppr_matiere[0]),array('a','e','i','o','u','y'))) $d="d'";else $d="de ";
               	$msg=boite_important("Candidat <b>$suppr_id_table</b> : note $d<b>$suppr_matiere</b> supprim&eacute;e");
               } else $msg='';
               $sql="SELECT can.id_table, nom, prenoms, ser.serie, type, matiere, notes.id_matiere, note\n".
               " from odb_candidats can, odb_notes notes, odb_ref_matiere mat, odb_ref_serie ser\n".
               " where can.annee=$annee and notes.annee=$annee and can.id_table=notes.id_table and ser.id=can.serie and mat.id=notes.id_matiere\n".
               " and can.id_table in ('".join("','",$idTables)."')\n".
               " order by id_table, type, matiere";
               //echo "<pre>$sql</pre>\n";
					$result=odb_query($sql,__FILE__,__LINE__);
					$colonnes=array('id_table','nom','prenoms','serie','type','id_matiere','matiere','note');
					$cpt=0;
					while($row=mysql_fetch_array($result)) {
						$cpt++;
						$id_table=$row['id_table'];
						$matiere=$row['matiere'];
						if($row['note']<0) $row['note']="<b style='color:#f00;'>Absent</b>";
						foreach($colonnes as $col) $tNotes[$id_table][$matiere][$cpt][$col]=$row[$col];
						$tNotes[$id_table]['candidat']=stripslashes("<b>$row[nom]</b> $row[prenoms] - s&eacute;rie <b>$row[serie]</b>");
					}
					//echo"<pre>";print_r($tNotes);echo"</pre>";
            	$msg.="<h1>Suppression des notes</h1>\n";
            	$msg.="<form name='form_notes' ACTION='?exec=odb_param' METHOD='POST'>\n".
            	"<input type='hidden' name='matiere'/>\n".
            	"<input type='hidden' name='id_matiere'/>\n".
            	"<input type='hidden' name='id_table'/>\n".
            	"<input type='hidden' name='suppr_num_table_notes' value=\"".trim($_POST['suppr_num_table_notes'])."\"/>\n".
            	"<input type='hidden' name='action' value='supprimer_notes'/>\n";
            	if(count($tNotes)>1) $s='s'; else $s='';
            	"<h2>".count($tNotes)." candidat$s</h2>\n";
					foreach($tNotes as $id_table=>$t1) {
						$candidat=$t1['candidat'];
						$msg.="<b>$id_table</b> : $candidat<br/>\n".
						"<table class='spip'>\n<tr><th>Type</th><th>Mati&egrave;re</th><th>Note</th><th>Action</th></tr>\n";
						foreach($t1 as $matiere=>$t2) if($matiere!='candidat') {
							foreach($t2 as $t3)
								$msg.="<tr><td>$t3[type]</td><td>$matiere</td><td>$t3[note]</td>".
								"<td><input type='submit' class='fondo' value='Supprimer' onclick=\"document.forms['form_notes'].id_matiere.value='$t3[id_matiere]';document.forms['form_notes'].matiere.value='$t3[matiere]';document.forms['form_notes'].id_table.value='$id_table';return confirm('Souhaitez vous vraiment supprimer la note\\n$t3[matiere] pour le candidat $id_table ?');\"/></td>".
								"</tr>\n";
						}
						$msg.="</table>\n";
					}
					$msg.="</form>\n";
               break;            
            ///////////////////////////////////////////////////////////////////////
            case 'anonymiser':
               if($tab_auteur['login']!=$tParam['login_anonymes']) die(KO." - Vous n'&ecirc;tes pas autoris&eacute;(e) &agrave; ex&eacute;cuter cette action");
               $sql = 'SELECT count(*) nb_can, id_etablissement, etablissement, departement'
                    . ' FROM odb_repartition rep, odb_ref_etablissement eta, odb_ref_departement dep'
                    . ' WHERE eta.id=rep.id_etablissement AND dep.id=eta.id_departement'
                    . " AND annee=$annee"
                    . ' group by id_etablissement'
                    . ' order by nb_can desc'
                    ;
               //echo $sql;
               $result=odb_query($sql,__FILE__,__LINE__);
               while($row=mysql_fetch_array($result)) {
                  $id_etablissement=$row['id_etablissement'];
                  $departement=$row['departement'];
                  $nbCan=(int)$row['nb_can'];
                  $etablissement=$row['etablissement'];
                  $tRepartition[$departement][$etablissement]=$id_etablissement;
                  $tEta[$id_etablissement]=$nbCan;
                  $aleaI[$id_etablissement]=rand(10000,49999);
                  $aleaP[$id_etablissement]=rand(50000,99999);
               }
               // on initialise un tableau des valeurs interdites
               // on crée un numéro aléatoire et on vérifie ce numéro
               // a chaque fois qu'on trouve une fourchette dont aucune extrémité n'est dans la zone interdite,
               // on accepte le nombre aléatoire et on crée une nouvelle zone interdite
               // (valable car les fourchettes sont rangees par ordre decroissant)
               // on itère tant qu'il faut
               $tInterdit=range(100000,110000);
               foreach($tEta as $id_etablissement=>$nbCan) {
                  $nbImpairs=ceil($nbCan/2);
                  $nbPairs=floor($nbCan/2);
                  $prochainI=0;
                  $prochainP=0;
                  while(in_array($aleaI[$id_etablissement], $tInterdit) or in_array($aleaI[$id_etablissement]+$nbImpairs, $tInterdit)) {
                     $aleaI[$id_etablissement]=rand(10000,49999);
                     //if($aleaI[$id_etablissement]%2==0) $aleaI[$id_etablissement]--;
                     //$prochainI=trouveProchain($aleaI[$id_etablissement],$aleaI);
                  }
                  for($i=$aleaI[$id_etablissement];$i<=$aleaI[$id_etablissement]+$nbImpairs;$i++)
                     $tInterdit[]=$i;
                  while(in_array($aleaP[$id_etablissement], $tInterdit) or in_array($aleaP[$id_etablissement]+$nbPairs, $tInterdit)) {
                     $aleaP[$id_etablissement]=rand(50000,99999);
                     //if($aleaP[$id_etablissement]%2==0) $aleaP[$id_etablissement]--;
                     //$prochainP=trouveProchain($aleaP[$id_etablissement],$aleaP);
                  }
                  for($i=$aleaP[$id_etablissement];$i<=$aleaP[$id_etablissement]+$nbPairs;$i++)
                     $tInterdit[]=$i;
                  $sql="SELECT id_saisie from odb_repartition where annee=$annee and id_etablissement=$id_etablissement and right(id_table,4)%2=1 order by id_table";
                  $result=odb_query($sql,__FILE__,__LINE__);
                  $cptAno=0;
                  while($row=mysql_fetch_array($result)) {
                     $id_saisie=$row['id_saisie'];
                     $id_anonyme=$cptAno+(int)$aleaI[$id_etablissement];
                     $cptAno++;
                     $sql="UPDATE odb_repartition SET id_anonyme=$id_anonyme WHERE id_saisie=$id_saisie and annee=$annee";
                     odb_query($sql,__FILE__,__LINE__);
                  }

                  $sql="SELECT id_saisie from odb_repartition where annee=$annee and id_etablissement=$id_etablissement and right(id_table,4)%2=0 order by id_table";
                  $result=odb_query($sql,__FILE__,__LINE__);
                  $cptAno=0;
                  while($row=mysql_fetch_array($result)) {
                     $id_saisie=$row['id_saisie'];
                     $id_anonyme=$cptAno+(int)$aleaP[$id_etablissement];
                     $cptAno++;
                     $sql="UPDATE odb_repartition SET id_anonyme=$id_anonyme WHERE id_saisie=$id_saisie and annee=$annee";
                     odb_query($sql,__FILE__,__LINE__);
                  }
/*
                  $sql="UPDATE odb_repartition SET id_anonyme=ENCODE(right(id_table,4)+".$aleaI[$id_etablissement].",'".$tParam['code']."') where id_etablissement=$id_etablissement and right(id_table,4)%2=1";
                  mysql_query($sql) or die(KO." - Erreur dans la requete $sql<br />".mysql_error());
                  $sql="UPDATE odb_repartition SET id_anonyme=ENCODE(right(id_table,4)+".$aleaP[$id_etablissement].",'".$tParam['code']."') where id_etablissement=$id_etablissement and right(id_table,4)%2=0";
                  mysql_query($sql) or die(KO." - Erreur dans la requete $sql<br />".mysql_error());
*/
               }
               ksort($tRepartition);
               foreach($tRepartition as $departement=>$t1) {
                  echo "<h1>$departement</h1>\n";
                  ksort($t1);
                  foreach($t1 as $etablissement=>$id_etablissement) {
                     $nbCan=$tEta[$id_etablissement];
                     $moitie=ceil($nbCan/2);
                     echo "<h2>$etablissement</h2><small>&Eacute;tablissement <b>#$id_etablissement : $nbCan</b> candidats</small><br/>\n";
                     echo "$moitie candidats impairs : <b>".$aleaI[$id_etablissement]."</b><small> - ".($aleaI[$id_etablissement]+$moitie)."</small><br/>\n";
                     echo "$moitie candidats pairs : <b>".$aleaP[$id_etablissement]."</b><small> - ".($aleaP[$id_etablissement]+$moitie)."</small><br/>\n";
                  }
               }
               $sql="UPDATE odb_repartition SET id_anonyme=ENCODE(id_anonyme,'".$tParam['code']."') where annee=$annee";
               //TODO pourquoi encode marche pas ? mauvaise solution de toutes facons...
               odb_query($sql,__FILE__,__LINE__);
               $msg=OK.' - Num&eacute;ros anonymes g&eacute;n&eacute;r&eacute;s et crypt&eacute;s';
               break;
            ///////////////////////////////////////////////////////////////////////
            case 'impression_anonymes':
               if($tab_auteur['login']!=$tParam['login_anonymes']) die(KO." - Vous n'&ecirc;tes pas autoris&eacute;(e) &agrave; ex&eacute;cuter cette action");
            	$msg='';
            	$_SESSION = array();
              	$sql="SELECT dep.departement, eta.etablissement centre, ser.serie, rep.id_table, decode(id_anonyme,'".$tParam['code']."') id_anonyme, jury "
            		. "from odb_repartition rep, odb_ref_etablissement eta, odb_ref_departement dep, odb_candidats can, odb_ref_serie ser "
            		. "where can.id_saisie=rep.id_saisie and can.annee=$annee and can.serie=ser.id and rep.id_etablissement=eta.id and eta.id_departement=dep.id and rep.annee=$annee "
            		. 'order by dep.departement, centre, id_table';
            	//echo ($sql);
            	$result=mysql_query($sql) or die(KO." - Erreur dans la requete $sql<br/>".mysql_error());
            	while($row=mysql_fetch_array($result)) {
            		foreach(array('departement','centre','serie','id_table','id_anonyme','jury') as $col) $$col=utf8_decode($row[$col]);
            		$id_table=getIdTableHumain($id_table);
            		$tPdf[$departement][$centre][$jury][$serie][$id_table]=$id_anonyme;
            		if((int)$id_anonyme<10000 || $id_anonyme>99999) // on vérifie que tous les numeros anonymes font bien 5 caracteres
            			die(KO." - Erreur sur le num&eacute;ro anonyme $id_anonyme ($id_table) dans le centre $centre ($departement)");
            	}
            	foreach($tPdf as $departement=>$t1) {
            		$msg.="<hr size='1'/>\n<A NAME='".getRewriteString($departement)."'></a>\n<h1>$departement</h1>\n";
            		$msg_pdf.="<tr class='tr_liste'><th><h2>$departement</h2></th><th><A HREF='#".getRewriteString($departement)."'>d&eacute;tail</a></th></tr>\n";
            		foreach($t1 as $centre=>$t2) {
            			$msg.="<h2>$centre</h2>\n";
            			$cpt=0;
            			$msg.="<table class='spip'>\n<tr><th>#</th><th>S&eacute;rie</th><th>Num&eacute;ro table</th><th>Num&eacute;ro anonyme</th><th>Jury</th></tr>";
            			foreach($t2 as $jury=>$t3) {
            				foreach($t3 as $serie=>$t4) {
	            				foreach($t4 as $id_table=>$id_anonyme) {
		            				//$cpt++;
		            				if($cpt++<6) $msg.="<tr class='tr_liste'><th>$cpt</th><td>$serie</td><td>$id_table</td><td>$id_anonyme</td><td>$jury</td></tr>\n";
		            				$pdf[$departement][$centre][]=array(
		            					//'departement'=>$departement,
		            					//'centre'=>$centre,
		            					'serie'=>$serie,		            					
		            					'id_table'=>$id_table,
		            					'id_anonyme'=>$id_anonyme,
		            					'jury'=>$jury
		               				);
		            			}
		            		}
							//YEDA 25 Mars 2008
							// Appel de pdf-inc-requete au lieu de pdf-inc-table
							//le parametre  $centre est envoyer aussi comme variable dans l'url, c'est par lui
							//que la ou les requetes à exécuter est reconnue.
							// Chaque requete est suivi d'un titre, d'un pied de page et des colonnes de résultats qui peuvent variées
							$requete="SELECT ser.serie, rep.id_table, decode(id_anonyme,'".$tParam['code']."') id_anonyme, rep.jury "
							. "from odb_repartition rep, odb_ref_etablissement eta, odb_ref_departement dep, odb_candidats can, odb_ref_serie ser "
							. "where can.id_saisie=rep.id_saisie and rep.jury=$jury and can.annee=$annee and rep.annee=$annee and can.serie=ser.id and rep.id_etablissement=eta.id and eta.etablissement='$centre' and eta.id_departement=dep.id "
							. "order by ser.serie";
						    $_SESSION['requete'][$centre][]=$requete;
						    $_SESSION['pied'][$centre][]=html_entity_decode("Num&eacute;ros anonymes $centre ($departement)");
						    $_SESSION['titre'][$centre][]=html_entity_decode("Num&eacute;ros anonymes &agrave; l'examen du Bac - Centre de composition <b>$centre</b> ($departement) JURY:$jury");
						    $_SESSION['cols'][$centre][]=array(
							'serie'=>'Serie',
							'id_table'=>'Num table',
							'id_anonyme'=>'Num anonyme',
							'jury'=>'Jury'
						  	);
            			////////////////////
	            		}
	            		/*
	            		echo "////////////////////////////////";
	            		print_r($_SESSION['requete'][$centre]);
	            		echo "////////////////////////////////";
            			*/
            			$msg.="<tr><th colspan=3>Il y a $cpt candidats dans ce centre</th></tr>\n";
            			$msg.="</table>\n";
		         		$nom_pdf=getRewriteString("$action|$departement|$centre");
		                $tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-requete.php?pdf=$nom_pdf&param=$centre'>";
		                $tmp2="<b>$centre</b> : exporter <b>$cpt</b> num&eacute;ros anonymes</A>";
		                $lien_pdf=$tmp1.vignette('pdf',"Exporter $cpt num&eacute;ros anonymes de $centre ($departement) en PDF").$tmp2;
		                $msg.=$lien_pdf;
		                $msg_pdf.="<tr><td colspan=2>".$tmp1.$tmp2."</td></tr>\n";
						$_SESSION['encryption'][$centre]=array('user'=>$tParam['code'],'owner'=>$tParam['code'],'action'=>array('print'));
						$_SESSION['format'][$centre]=array('taille'=>'A4', 'orientation'=>'portrait');
            		}
            	}
            	$msg.="<hr size=1/><A HREF='".generer_url_ecrire('odb_param')."&reset'>Finaliser cette action</a>\n";
            	$msg="<table class='spip'><tr><th><h1>"
            		. vignette('pdf','Documents PDF')
            		. " Num&eacute;ros anonymes</h1></th></tr>$msg_pdf</table>\n"
            		. $msg
            		;
            	break;
            	///////////////////////////////////////////////////////////////////////
            case 'configurer':
               if($tab_auteur['login']!=$tParam['login_anonymes']) die(KO." - Vous n'&ecirc;tes pas autoris&eacute;(e) &agrave; ex&eacute;cuter cette action");
               $msg= "<form name='form_param' action='".generer_url_ecrire('odb_param')."' METHOD='POST'>\n";
               $msg.= "<table class='spip'>\n";
               foreach($tParam as $param=>$valeur) {
                  if($param=='code') $password=true;else $password=false;
                  if($param[0]!='_')
                  	$msg.= formInputTextTR(str_replace('_',' ',ucfirst($param)),"set|$param",$valeur,'','',$password);
               }
               $msg.= "</table>\n";
               $msg.= "<input type='submit' class='forml' name='ok' value='Enregistrer ces param&egrave;tres'/>\n";
               $msg.= "</form>\n";
               break;
            ///////////////////////////////////////////////////////////////////////
	    case 'preparation_notes_anonymes':
	       $msg="<h1>Pr&eacute;paration des notes</h1><h2>Saisie anonyme des notes</h1>";
	       $sql="DELETE from odb_notes WHERE annee=$annee";
	       odb_query($sql,__FILE__,__LINE__);
	       $msg.=OK." - <b>".mysql_affected_rows()."</b> notes supprim&eacute;es<br/>";
	       $code=$tParam['code'];
	       $sql="insert into odb_notes (`id_table`,`id_anonyme`, `annee`, `id_serie`, `jury`, `id_matiere`, `type`, `coeff`)\n"
		  . "(SELECT DECODE(id_anonyme,'$code'), DECODE(id_anonyme,'$code'), 2008, serie, jury, id_matiere, type, coeff\n"
		  . " FROM odb_repartition rep, odb_ref_examen exa, odb_candidats can\n"
		  . " WHERE rep.annee=$annee and exa.annee=$annee and can.annee=$annee and (type='Ecrit' OR type='Pratique')"
		  . " and can.id_saisie=rep.id_saisie and can.serie=exa.id_serie and jury is not null)"
		  ;
	       odb_query($sql,__FILE__,__LINE__);
	       $msg.=OK." - <b>".mysql_affected_rows()."</b> notes cr&eacute;&eacute;es<br/>";
	       break;
	    ///////////////////////////////////////////////////////////////////////
            case 'deliberer':
            	$tDelib1=$tParam["_delib1_$annee"];
            	if($tab_auteur['login']!=$tParam['login_anonymes']) die(KO." - Vous n'&ecirc;tes pas autoris&eacute;(e) &agrave; ex&eacute;cuter cette action");
            	$sql="SELECT DISTINCT jury1, jury2, jury3, jury4, deliberation from odb_ref_deliberation delib, odb_ref_operateur ope\n".
            	"WHERE delib.id=ope.id_deliberation AND ope.annee=$annee ORDER BY id_deliberation, jury1";
            	$result=odb_query($sql,__FILE__,__LINE__);
            	//die("<pre>$sql</pre>");
            	while($row=mysql_fetch_array($result)) {
            		$deliberation=$row['deliberation'];
            		foreach(array('jury1','jury2','jury3','jury4') as $col)
            		if($row[$col]!=0) $tDelib[$deliberation][]=$row[$col];
            	}
            	$msg="<h1>Lev&eacute;e de l'anonymat</h1>\nCochez les jurys dont l'anonymat doit &ecirc;tre lev&eacute;.<br/>\nLes listes seront automatiquement disponibles dans l'espace de saisie des notes, accessible aux op&eacute;rateurs de saisie.";
            	if(is_array($tDelib)) {
						$msg.=odb_table_matieres(array_keys($tDelib));
						$msg.="<form name='form_delib' action='".generer_url_ecrire('odb_param')."' METHOD='POST' onSubmit=\"return confirm('ATTENTION !\\n- Avez-vous entre le BON MOT DE PASSE ?\\n\\nCliquez sur [Annuler] et mettez le bon mot de passe si tel n\'est pas le cas');\">\n";
						foreach($tDelib as $deliberation=>$tJurys) {
							asort($tJurys);
							$msg.="<A name='".getRewriteString($deliberation)."'></A>\n<h2>$deliberation</h2>\n".
							"<table class='spip'>\n<tr><th>Jury</th><th>1&deg;<br/>d&eacute;lib.</th></tr>\n";
							foreach($tJurys as $jury) {
								if(isset($tDelib1[$jury])) $selected='checked';
								else $selected='';
								$msg.="<tr><td><label for='delib1_$annee|$jury'>Jury $jury</label></td><td><INPUT type='checkbox' class='fondo' name='delib1_$annee|$jury' $selected/></td></tr>\n";
							}
							$msg.="<tr><td colspan=2><input type='submit' name='deliberer' class='forml' value='Enregistrer'/></td></tr>\n";
							$msg.="</table>\n";
						}
					} else $msg="Aucun jury d&eacute;fini pour les op&eacute;rateurs en $annee<br/>"
						   ."<small>Veuillez commencer par <A HREF='".generer_url_ecrire('odb_ref')."&step2=manuel&table=odb_ref_operateur&annee=$annee'>les cr&eacute;er</A> svp</small>"
						   ;
            	break;
            ///////////////////////////////////////////////////////////////////////
            case 'fix_numero_de_table':
               $lettres=range('A','E');
               for($i=0;$i<count($lettres);$i++) {
                  $lettre=$lettres[$i];
                  $sql = "SELECT left(id_table,instr(id_table,'-')-1) id_centre,max(right(id_table,4)) maximum from odb_candidats"
                       . " where right(id_table,9) like '$lettre%' and annee=$annee"
                       . " group by left(id_table,instr(id_table,'-')-1)"
                       . ' order by id_centre'
                       ;
                  $result=odb_query($sql,__FILE__,__LINE__);
                  echo "<hr size=1/><h1>$lettre</h1>$sql<br>";
                  while($row=mysql_fetch_array($result)) {
                     $idCentre=$row['id_centre'];
                     $max=(int)$row['maximum'];
                     if($i>0) {
                     	echo "Passage de $max ";
                     	$max+=$tMax[$lettres[$i-1]][$idCentre];
                     	echo " &agrave; $max<br/>\n";
                     }
                     $tMax[$lettre][$idCentre]=$max;
                     if($i>0) {
                        $sql = 'update odb_candidats '
                             . " set id_table = concat(convert(left(id_table,instr(id_table,'-')+5) using utf8), lpad(right(id_table,4)+".$tMax[$lettres[$i-1]][$idCentre].",4,'0')) "
                             . " where id_table like '$idCentre-$lettre%' and annee=$annee";
                       // mysql_query($sql) or die(KO." - Erreur dans la requete $sql<br />".mysql_error());
						echo $sql;
                        echo "<h2>Centre $idCentre : ".$tMax[$lettres[$i-1]][$idCentre]." en ".$lettres[$i-1].", $max en $lettre</h2>".OK." - $sql<br/>";
                     }
                  }
               }
               $sql="update odb_repartition rep, odb_candidats can set rep.id_table=can.id_table where rep.id_saisie = can.id_saisie and can.annee=$annee and rep.annee=$annee";
               odb_query($sql,__FILE__,__LINE__);
               break;
         }
      }
      echo $msg;
   fin_cadre_relief();
echo "<br/>\n<!-- ================== Formulaire param ================= -->\n";
debut_boite_info();
echo "<form name='form_param' method='POST' action='".generer_url_ecrire('odb_param')."' class='forml spip_xx-small'>\n";
echo "<table border=0 cellspacing=0 cellpadding=1 class='spip'>\n";
echo "<tr class='tr_odd'>\n\t<th>Action</th><th>Description</th>\n</tr>\n";
$title="Introspection du r&eacute;f&eacute;rentiel";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='introspection' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/loupe.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\"/></td>"
   . "<td>L'introspection consiste &agrave; v&eacute;rifier l'int&eacute;grit&eacute; du r&eacute;f&eacute;rentiel.<br/>Par exemple, les d&eacute;partements &agrave; 0 sont corrig&eacute;s (ou signal&eacute;s en cas de conflit entre ville et &eacute;tablissement), les champs invalides et les doublons sont signal&eacute;s.</td>\n"
   . "</tr>\n"
   ;
$title="R&eacute;initialiser les d&eacute;partements";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='reset' value='departement' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/effacer.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\" onclick=\"return confirm('Etes-vous certain(e) de vouloir reinitialiser les departements ?');\"/></td>"
   . "<td>Cette action remet les <b>d&eacute;partements des candidats</b> &agrave; 0.<br/>Si vous pensez que le d&eacute;partement est mal affect&eacute; pour certains candidats, r&eacute;initialisez les d&eacute;partements <b>puis lancez une introspection</b></td>\n"
   . "</tr>\n"
   ;
$title="R&eacute;initialiser les num&eacute;ros de table";
$inputSerie="<SELECT NAME='serie' class='fondo' onChange=\"if(this.value>0) laSerie=' en serie '+this.options[selectedIndex].text; else laSerie='';\" onLoad=\"laSerie='';\">".formOptionsRefInSelect('serie',0)."</SELECT>\n";
$inputCentre="<SELECT NAME='centre' class='fondo' onChange=\"if(this.value>0) leCentre='\\n qui composaient dans le centre '+this.options[selectedIndex].text; else leCentre='';\" onLoad=\"lCentre='';\">".formOptionsRefInSelect('centres',0)."</SELECT>\n";
$inputEtablissement="<SELECT NAME='etablissement' class='fondo' onChange=\"if(this.value>0) lEta='\\nqui proviennent de l\\'etablissement '+this.options[selectedIndex].text; else lEta='';\" onLoad=\"lEta='';\">".formOptionsRefInSelect('etablissements',0)."</SELECT>\n";
$inputSupprNumTable="<TEXTAREA class='fondo'  NAME='suppr_num_table' cols=16 rows=5></TEXTAREA>\n";
echo "<tr class=\"tr_liste\">\n\t<td align='center'><input type='image' name='vider' value='odb_repartition' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/effacer.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\" onclick=\"if(document.forms['form_param'].suppr_num_table.value=='') {if(document.forms['form_param'].serie.value==0) laSerie=' toutes series confondues';if(document.forms['form_param'].centre.value==0) leCentre='\\nquel que soit le centre de composition';if(document.forms['form_param'].etablissement.value==0) lEta='\\nquel que soit l\\'etablissement d\\'origine';return confirm('Etes-vous certain(e) de vouloir reinitialiser les numeros de table\\ndes candidats de $annee'+laSerie+leCentre+lEta+' ?');} else return confirm('ATTENTION ! Cette action est definitive\\n\\nSouhaitez vous vraiment supprimer les numeros de table pour ces candidats ?');\"/></td>"
   . "<td>Cette action <b>vide les num&eacute;ros de table</b> pour l'ann&eacute;e $annee.<br/>Vous pouvez sp&eacute;cifier une s&eacute;rie : $inputSerie<br/> et/ou un centre : $inputCentre<br/> et/ou un &eacute;tablissement : $inputEtablissement<br />et/ou un ou plusieurs num&eacute;ros de table &agrave; supprimer (1 par ligne) :<br />$inputSupprNumTable</td>\n"
   . "</tr>\n"
   ;
echo "<tr>\n\t<td align='center' colspan=2><hr size=1/>Gestion des notes<hr size=1/></td>\n</tr>\n";
$title="Suppression de notes";
$inputSupprNumTableNotes="<TEXTAREA class='forml' NAME='suppr_num_table_notes' cols=16 rows=5></TEXTAREA>\n";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='supprimer_notes' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/effacer.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\" onclick=\"if(document.forms['form_param'].suppr_num_table_notes.value=='') {alert('Veuillez entrer au moins un numero de table');return false;}\" /></td>"
   . "<td>Permet de <b>supprimer une ou plusieurs notes</b> d'un ou plusieurs candidats (un num&eacute;ro de table par ligne)<br/>$inputSupprNumTableNotes</td>\n"
   . "</tr>\n"
   ;
if($tab_auteur['login']==$tParam['login_anonymes']) {
   echo "<tr style='background-color:#fff;'>\n\t<td align='center' colspan=2><hr size=1/>Acc&egrave;s r&eacute;serv&eacute; : <b>".getNomComplet($tParam['login_anonymes'])."</b><hr size=1/></td>\n</tr>\n";
   $title="Configuration de SIOU";
   echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='configurer' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/configuration.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\"/></td>"
      . "<td>$title</td>\n"
      . "</tr>\n"
      ;
   $title="G&eacute;n&eacute;rer les num&eacute;ros anonymes";
   echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='anonymiser' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/anonymer.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\" onclick=\"return confirm('Ce processus va :\\n- generer de nouveaux numeros anonymes (remplace les anciens)\\n- crypter ces numeros avec le code (cf. `configuration de siou`)')\"/></td>"
      . "<td>$title <input type='image' name='action' align='absmiddle' value='impression_anonymes' src='".DIR_ODB_COMMUN."img_pack/vignettes/pdf.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[Imprimer les num&eacute;ros anonymes (pdf)]\"/>Imprimer n&deg; anonymes</td>\n"
      . "</tr>\n"
      ;
   $title="Pr&eacute;paration de la saisie des notes anonyme";
   echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='preparation_notes_anonymes' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/jury.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\" onclick=\"return confirm('Ce processus va regenerer la liste des numeros anonymes\\nIl permet de preparer la saisie des notes sous anonymat\\n\\nATTENTION !\\n- Veuillez ENTRER LE BON MOT DE PASSE')\"/></td>"
      . "<td>Initialisation notes | <input type='image' name='action' align='absmiddle' value='deliberer' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/jury.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[Gestion de la 1&egrave; d&eacute;lib&eacute;ration]\"/>Gestion 1<sup>&egrave;re</sup> d&eacute;lib&eacute;ration</td>\n"
      . "</tr>\n"
      ;
}
echo "</table>\n</form>\n";
fin_boite_info();
fin_cadre_relief();
fin_page();
exit;

}
?>


