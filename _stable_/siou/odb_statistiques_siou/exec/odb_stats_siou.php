<?php
if(isset($_GET['reset'])) session_destroy();
session_start();
//echo"<pre>";print_r($GLOBALS);exit;
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
define('DIR_ODB_CONTRIB',_DIR_PLUGINS.'odb/odb_contrib/');
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN.'inc-odb.php');

setlocale(LC_TIME, "fr_FR");

global $debug;
$debug=false;
define(OK,"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define(KO,"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

/**
 * Icone d'impression des convocations
 * Affiche une icone par groupe de 300 convocations pour un etablissement, peut etre limite a une serie donnee
 *
 * @param string $annee : annee
 * @param string $id_etablissement : identifiant de l'etablissement
 * @param string $nb : nombre de convocations au total (cette fonction va les decouper en groupes de 300 si $nb>300)
 * @param string $serie : serie des convocations
 * @return string
 */
function icones_impression_convocation($annee,$id_etablissement,$etablissement,$nb,$serie='',$id_departement='',$departement='') {
   $imgPrint="<IMG src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/imprimer.png' style='vertical-align:middle'>";
   if($serie=='') $serie_aff='';
   else $serie_aff="de s&eacute;rie $serie";
   if($departement!='') $serie_aff.="du d&eacute;partement $departement";
   $lien_convoc='';
   $limit=0;
   $nb_ico=ceil($nb/300);
   $cpt_ico=1;
   while($nb-$limit-300>0) {
      $lien_convoc.="<A href=\"../plugins/odb/odb_commun/inc-pdf-convocation.php?annee=$annee&serie=$serie&id_etablissement=$id_etablissement&etablissement=$etablissement&id_departement=$id_departement&departement=$departement&limit=$limit\" title=\"header=[$imgPrint Convocations] body=[Candidats &lt;".($limit+1)."-".($limit+300)."&gt; $serie_aff ($cpt_ico/$nb_ico)] fade=[on] fadespeed=[0.08]\">"
                  . "<IMG src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/imprimer.png' align='absmiddle' alt='Imprimer les convocations de $limit candidats $serie_aff'/>"
                  . "</A>"
                  ;
      $cpt_ico++;
      $limit+=300;
   }
   $lien_convoc.="<A href=\"../plugins/odb/odb_commun/inc-pdf-convocation.php?annee=$annee&serie=$serie&id_etablissement=$id_etablissement&etablissement=$etablissement&id_departement=$id_departement&departement=$departement&limit=$limit\" title=\"header=[$imgPrint Convocations] body=[Candidats &lt;".($limit+1)."-".$nb."&gt; $serie_aff ($cpt_ico/$nb_ico)] fade=[on] fadespeed=[0.08]\">"
               . "<IMG src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/imprimer.png' align='absmiddle' alt='Imprimer les convocations des $nb candidats $serie_aff'/>"
               . "</A>"
               ;
   return $lien_convoc;
}

/**
 * exécuté automatiquement par le plugin au chargement de la page ?exec=odb_stats_siou
 * 
 * @author Cedric PROTIERE
 */ 
function exec_odb_stats_siou() {
global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping;

include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");
echo "<SCRIPT SRC='".DIR_ODB_CONTRIB."boxover/boxover.js'></SCRIPT>\n";
$imgInfo="<img src='".DIR_ODB_CONTRIB."boxover/info.gif' style='vertical-align:middle'>";
$imgPrint="<IMG src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/imprimer.png' style='vertical-align:middle'>";
$annee=$_REQUEST['annee'];
if($annee=='') $annee=date('Y');
$array_ref=array('departement','etablissement','ef','lv','eps','prefixe','serie','sexe','ville','pays','matiere','centre');
foreach($array_ref as $ref)
   $tab_referentiel[$ref]=getReferentiel($ref,'tout');

$PDF_A3_PAYSAGE=array(
   //'shadecol' => array(0.1,0.8,0.5),
	'rowgap' => 125, //taille de l'espace entre le texte et les traits du tableau
	'width'  => 1150,
	'maxWidth' => 1150
);
$PDF_A4_PORTRAIT=array(
//'shadecol' => array(0.1,0.8,0.5),
'rowgap' => 125, //taille de l'espace entre le texte et les traits du tableau
'width'  => 575,
'maxWidth' => 575
);

debut_page(_T('Statistiques SIOU'), "", "");
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

debut_cadre_relief( "", false, "", $titre = _T('Statistiques SIOU'));
//debut_boite_info();
echo '<br>';

$REFERER=$_SERVER['HTTP_REFERER'];
$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];

echo "<IMG SRC='"._DIR_PLUGIN_ODB_STATS_SIOU."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";

//////////////////////////////////////////////// step 1 : formulaire de parametres
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
$msg="Choisissez une action ci-dessous";

foreach($_POST as $key=>$val)
if(substr_count($key,'stats_')>0) {
   $$key=$val;
}
$etablissement_txt=$_REQUEST['etablissement_txt'];
if($action!='') {
   $msg="";
   $tActionsAutoriseesEncadrants=array('etat_candidats_par_etablissement_avec_num_table','etat_candidats_par_etablissement_et_par_serie','liste_d_inscription_des_candidats','liste_d_inscription_des_candidats_affichage','impression_des_convocations');
   if(in_array($action,$tActionsAutoriseesEncadrants))
   	isAutorise(array('Admin','Encadrant'));
   else
   	isAutorise(array('Admin'));
   switch($action) {
      case 'etat_repartition' :
         $sql = 'SELECT * , nb_salles * capacite capacite_type , nb_salles * capacite - nb_repartis dispo '
           . ' FROM odb_ref_salle salle '
           . ' LEFT JOIN ( '
           . ' SELECT id_salle , count( * ) nb_repartis'
           . ' FROM odb_repartition '
           . " where annee=$annee"
           . ' GROUP BY id_salle '
           . ' ) rep ON salle . id = rep . id_salle '
           . ' ORDER BY id_etablissement, salle';
         $result=odb_query($sql,__FILE__,__LINE__);
         $nb_rows=mysql_num_rows($result);
         while($row=mysql_fetch_array($result)) {
            $id_salle = $row['id'];
            $id_etablissement=$row['id_etablissement'];
            if(!is_array($tab_eta_dept[$id_etablissement])) {
               foreach($tab_referentiel['etablissement'] as $dept => $tab)
                  if(is_numeric($dept))
                     if(isset($tab[$id_etablissement])) {
                        $tab_eta_dept[$id_etablissement]['departement']=$tab_referentiel['departement'][$dept];
                        $tab_eta_dept[$id_etablissement]['id_dept']=$dept;
                     }
            }
            $dispo=$row['dispo'];
            if($dispo=='')
               $row['dispo']="<font color='green'><b>".$row['capacite_type']."</b></font>";
            elseif($dispo==0)
               $row['dispo']="<font color='red'><b>$dispo</b></font>";
            else
               $row['dispo']="<font color='orange'><b>$dispo</b></font>";
            if($row['nb_repartis']=='')
               $row['nb_repartis']="<i>0</i>";
            $departement=$tab_eta_dept[$id_etablissement]['departement'];
            $id_dept=$tab_eta_dept[$id_etablissement]['id_dept'];
            $etablissement=$tab_referentiel['etablissement'][$id_dept][$id_etablissement];
            //echo "<hr>$id_salle - $id_etablissement - $departement $id_dept - $etablissement $id_etablissement";
            $champs=array('salle','nb_salles','capacite','nb_repartis','capacite_type','dispo');
            foreach($champs as $champ)
               $tab_repart[$departement][$etablissement][$id_salle][$champ]=$row[$champ];
         }
         if($stats_departement>0)
            $departements=array($tab_referentiel['departement'][$stats_departement]);
         else $departements=$tab_referentiel['departement'];
         foreach($departements as $dept) {
            if(!is_numeric($dept)) {
               ksort($tab_repart[$dept]);
               $msg.="<h1>$dept</h1>";
               $msg.="<table class='spip' width='100%' cellpadding=1 cellspacing=0>\n<tr>\n\t<th><small>&Eacute;tablissement</small></th>";
               foreach($champs as $champ)
                  $msg.="<th><small>".ucfirst(str_replace('_','<br/>',$champ))."</small></th>\n";
               $msg.="</tr>\n";
               foreach($tab_repart[$dept] as $etablissement => $tab_salles) {
                  foreach($tab_salles as $id_salle => $tab) {
                     $msg.="\t<tr class='tr_liste'><td><b>$etablissement</b></td>";
                     foreach($tab as $key=>$val) {
                        $msg.="<td>$val</td>";
                     }
                     $msg.="</tr>\n";
                  }
               }
               $msg.="</table>\n";
            }
         }
         break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_candidats_par_centre':
            $groupBy=' GROUP BY dep.departement , eta.etablissement, salle, num_salle, ser.serie';
            $lastCol='count( * ) nb_candidats';
            if($stats_departement>0) $and_departement=" AND eta.id_departement=$stats_departement";
            if($stats_centre>0) $and_centre=" AND rep.id_etablissement=$stats_centre";
            $sql = "SELECT dep.departement , eta.etablissement , concat(salle,num_salle) salle , num_salle, ser.serie, $lastCol"
                 . ' FROM odb_candidats can , odb_ref_serie ser , odb_ref_etablissement eta , odb_ref_departement dep,'
                 . ' odb_repartition rep, odb_ref_salle sal'
                 . " WHERE can.annee = $annee and rep.annee=$annee and sal.annee=$annee "
                 . $and_departement.$and_centre
                 . ' AND can.serie = ser.id AND can.id_saisie=rep.id_saisie'
                 . ' AND rep.id_etablissement = eta.id AND eta.id_departement = dep.id and sal.id=rep.id_salle'
                 . $groupBy
                 . ' ORDER BY dep.departement , eta.etablissement, num_salle, ser.serie, nom, prenoms'
                 ;
            //echo $sql;
            $result=odb_query($sql,__FILE__,__LINE__);
            $nb_rows=mysql_num_rows($result);
            $champs=array('salle','serie','nb_candidats');
            $msg="<table>";
            while($row=mysql_fetch_array($result)) {
               $departement=$row['departement'];
               $etablissement=$row['etablissement'];
               if($etablissement!=$etablissement_old) {
                  //on change de département
                  $msg.="</table>\n";
                  if($departement!=$departement_old) $msg.="<h1>$departement</h1>\n";
                  $msg.="<h2>$etablissement</h2>\n"
                     . "<table width='100%' border=0 cellpadding=1 cellspacing=0 style='border: 3px groove gray'>\n"
                     . "<tr>\n\t"
                     ;
                  foreach($champs as $champ)
                     $msg.="<th><small>".ucfirst(stripslashes(str_replace("_","<br/>",$champ)))."</small></th>\n";
                  $msg.="</tr>\n";
               }
               $msg.="<tr class='tr_liste'>";
               foreach($champs as $champ) {
                  $$champ=$row[$champ];
                  if($champ=='serie' && $serie!=$serie_old) $serie="<b>$serie</b>";
                  $msg.="<td><small>".$$champ."</small></td>";
               }
               $msg.="</tr>\n";
               $departement_old=$departement;
               $etablissement_old=$etablissement;
               $serie_old=$row['serie'];
            }
            $msg.="</table>\n";
         break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_candidats_par_etablissement_et_par_serie': // détail pour établissement
         
            $where="";
            if($stats_departement>0) $where="\n AND can.departement=$stats_departement ";
            if($stats_centre>0) $where.="\n AND rep.id_etablissement=$stats_centre ";
            if($stats_etablissement>0) $where.="\n AND can.etablissement=$stats_etablissement ";
/*            $sql="SELECT rep.id_table, rep.id_saisie, serie, prefixe, nom, prenoms, sexe, ne_le, ne_en, ne_vers, ldn, lv1, lv2, eps, ef1, ef2, can.departement, can.etablissement, rep.id_etablissement centre"
               . "\n FROM odb_repartition rep, odb_candidats can, odb_ref_departement dep, odb_ref_etablissement cen"
               . "\n WHERE can.annee=$annee AND rep.annee=$annee"
               . $where
               . "\n AND can.id_saisie=rep.id_saisie"
               . "\n AND cen.id=rep.id_etablissement AND dep.id=cen.id_departement"
               . "\n ORDER BY etablissement, serie, nom, prenoms"
               ;*/
				$sql = "SELECT can.id_table , can.id_saisie , serie , prefixe , nom , prenoms , sexe , ne_le , ne_en , ne_vers , ldn , lv1 , lv2 , eps , ef1 , ef2 , can.departement , can.etablissement , rep.id_etablissement centre "
					  . "\n FROM odb_candidats can "
					  . "\n LEFT JOIN odb_repartition rep on can.id_saisie = rep.id_saisie "
					  . "\n AND rep.annee = $annee "
					  . "\n LEFT JOIN odb_ref_etablissement cen on cen.id = rep.id_etablissement "
					  . "\n LEFT JOIN odb_ref_departement dep on dep.id = cen.id_departement "
					  . "\n WHERE can.annee = $annee "
					  . $where
					  . "\n ORDER BY etablissement , serie , nom , prenoms"
					  ;
            //echo "<pre>$sql</pre>";
            $annee_stats=$annee;
            $result=odb_query($sql,__FILE__,__LINE__);
            $nb_rows=mysql_num_rows($result);
            $pdf_cols=array('Candidat'=>'Candidat',
                            'Date_naissance'=>'Date et lieu de naissance',
                            'Serie'=>'Serie',
                            'LV1'=>'LV1',
                            'LV2'=>'LV2',
                            'EPS'=>'EPS',
                            'Ep_fac_1'=>'Ep. fac. 1',
                            'Ep_fac_2'=>'Ep. fac. 2',
                            'Centre'=>'Centre',
                           );
				$pdf_cols_id=array('Num_saisie'=>'Num saisie');
				$isNumTableOld=false;// si passe a vrai on affiche la colonne NumTableOld
            while($row=mysql_fetch_array($result)) {
               foreach(array('id_table','id_table_old','id_saisie','serie','prefixe','nom','prenoms','sexe','ne_le','ne_en','ne_vers','ldn','lv1','lv2','eps','ef1','ef2','departement','etablissement','centre') as $champ)
                  $$champ=stripslashes($row[$champ]);
               if($id_table!='0') {
               	$id_table=getIdTableHumain($id_table);
						$isNumTable=true;
						if($id_table_old!='') $isNumTableOld=true;
					}
               $id_departement=$departement;
               $departement=$tab_referentiel['departement'][$id_departement];
               $etablissement=$tab_referentiel['etablissement'][$id_departement][$etablissement];
               $etablissement=utf8_decode($etablissement);
               $prefixe=strtolower($tab_referentiel['prefixe'][$prefixe]);
               $serie=$tab_referentiel['serie'][$serie];
               $nom=strtoupper($nom);
               $prenoms=ucwords(strtolower($prenoms));
               $sexe=$tab_referentiel['sexe'][$sexe];
               $sexe=$sexe=='M'?'M.':'Mlle';
               $nom="$sexe $prefixe <b>$nom</b> $prenoms";
               if($ne_en>0) $ddn="En $ne_en";
               elseif($ne_vers>0) $ddn="Vers $ne_vers";
               else {
                  $tDate=explode('-',$ne_le);
                  $annee=$tDate[0];
                  $mois=$tDate[1];
                  $jour=$tDate[2];
                  $ddn="$jour/$mois/$annee";
               }
               $ddn.=" $ldn";
               $lv1=$tab_referentiel['lv'][$lv1];
               $lv2=$tab_referentiel['lv'][$lv2];
               $ef1=$tab_referentiel['ef'][$ef1];
               $ef2=$tab_referentiel['ef'][$ef2];
               $eps=$tab_referentiel['eps'][$eps];
               if(!is_array($tab_centres[$centre])) {
                  if(isset($tab_referentiel['etablissement'][$id_departement][$centre])) {
                     $tab_centres['centre'][$centre]=$tab_referentiel['etablissement'][$id_departement][$centre];
                     $tab_centres['departement'][$centre]=$departement;
                  }
                  else foreach($tab_referentiel['etablissement'] as $id_dept=>$tab)
                     if(isset($tab[$centre])) {
                        $tab_centres['centre'][$centre]=$tab[$centre];
                        $tab_centres['departement'][$centre]=$tab_referentiel['departement'][$id_dept];
                     }
               }
               $departement_centre=$tab_centres['departement'][$centre];
               $centre=$tab_centres['centre'][$centre];
               if($etablissement!=$etablissement_old) $cpt_pdf=0;
               else $cpt_pdf++;
               foreach(array('id_saisie','nom','ddn','lv1','lv2','eps','ef1','ef2','centre') as $var)
                  $tab_can[$departement][$etablissement][$serie][$id_saisie][$var]=stripslashes($$var);
               $pdf[$departement][$etablissement][$cpt_pdf]['Num_saisie']=$id_saisie;
               $pdf[$departement][$etablissement][$cpt_pdf]['Num_table']=$id_table;
               $pdf[$departement][$etablissement][$cpt_pdf]['Num_table_old']=$id_table_old;
               $pdf[$departement][$etablissement][$cpt_pdf]['Candidat']=utf8_decode($nom);
               $pdf[$departement][$etablissement][$cpt_pdf]['Date_naissance']=utf8_decode($ddn);
               $pdf[$departement][$etablissement][$cpt_pdf]['Serie']=$serie;
               $pdf[$departement][$etablissement][$cpt_pdf]['LV1']=utf8_decode($lv1);
               $pdf[$departement][$etablissement][$cpt_pdf]['LV2']=utf8_decode($lv2);
               $pdf[$departement][$etablissement][$cpt_pdf]['EPS']=utf8_decode($eps);
               $pdf[$departement][$etablissement][$cpt_pdf]['Ep_fac_1']=utf8_decode($ef1);
               $pdf[$departement][$etablissement][$cpt_pdf]['Ep_fac_2']=utf8_decode($ef2);
               $pdf[$departement][$etablissement][$cpt_pdf]['Centre']=utf8_decode($centre);
               $etablissement_old=$etablissement;
            }
				if($isNumTableOld) $pdf_cols_id=array_merge($pdf_cols,array('Num_table_old'=>'Ancien num table'));

				$pdf_cols=array_merge($pdf_cols_id,$pdf_cols);
            $msg="";
            if(is_array($tab_can)) ksort($tab_can);
            else die(KO." - Veuillez commencer par la r&eacute;partition des candidats $annee");

            foreach($tab_can as $departement=>$tab1) {
               //departement
               ksort($tab1);
               if($stats_centre>0)
                  if(isset($tab1[$etablissement_txt])) {
                     $tab_tmp[$etablissement_txt]=$tab1[$etablissement_txt];
                     unset($tab1);
                     $tab1=$tab_tmp;
                  }

               foreach($tab1 as $etablissement=>$tab2) {
                  //Etablissement
                  ksort($tab2);
                  foreach($tab2 as $serie=>$tab3) {
                     $srcFond=texte90(" $departement - $etablissement - $serie ",5,30,400,'src');
                     $nbCandidats=count($tab3);
                     if($nbCandidats>10) $imgFond=texte90(" $departement - $etablissement - $serie ",5,30,400);
                     else $imgFond='';
                     $msg.="<h1>$departement</h1>\n"
                         . "<h2>&Eacute;tablissement : $etablissement</h2>\n"
                         . "<h3>S&eacute;rie $serie - $nbCandidats candidat(e)s</h3>\n"
                         . "<table class='spip' width='100%' border=0 cellpadding=1 cellspacing=0 style='border: 1px solid gray;'>\n"
                         . "<tr><th background='$srcFond' rowspan=".($nbCandidats+1)." valign='top' width='30'>$imgFond</th>\n"
                         ;
                     foreach(array('N&deg; saisie','Candidat','Date et lieu de naissance','LV1','LV2','EPS','Epr. Fac. 1','Epr. Fac. 2','Centre<br/>de composition') as $col)
                        $msg.="\t<th><small>$col</small></th>\n";
                     $msg.="</tr>\n";
                     foreach($tab3 as $id_table=>$tab) {
                        $msg.="<tr class='tr_liste'>\n\t"
                            . "<td><small><b><a href='".generer_url_ecrire('odb_saisie')."&annee=$annee_stats&step2=odb_candidats&identifiant=id_saisie&id=".$tab['id_saisie']."'>$id_table</a></b></small></td>\n"
                            ;
                        foreach($tab as $key=>$col)
                           if($key!='id_saisie')
                              $msg.="\t<td><small>$col</small></td>\n";
                        $msg.="\t<td>&nbsp;</td>\n</tr>\n";
                     }
                     $msg.="</table>\n";
                     $nom_pdf=getRewriteString("$departement|$etablissement");
                     $_SESSION['data'][$nom_pdf]=$pdf[$departement][$etablissement];
                     $_SESSION['pied'][$nom_pdf]="Candidats $etablissement ($departement)";
                     $_SESSION['titre'][$nom_pdf]=html_entity_decode("Liste d'inscription des candidats &agrave; l'examen du Bac - Session unique de juin ".date("Y")." - Etablissement : $etablissement");
                     $_SESSION['cols'][$nom_pdf]=$pdf_cols;
                     $_SESSION['options'][$nom_pdf]=$PDF_A3_PAYSAGE;
                     $tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
                     $tmp2=" G&eacute;n&eacute;rer la liste de l'&eacute;tablissement <b>$etablissement</b> en PDF</A>";
                     $msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer la liste de $etablissement en PDF").$tmp2;
                     $lien_pdf[$nom_pdf]=$tmp1.$tmp2;
                  }
               }
            }
         break;
         
                 ///////////////////////////////////////////////////////////////////////////////////////////
         // VLAV et YEDA 10/03/2008 ajout du bouton pour la liste des candidats avec numero de table sans num de saisie
         case 'etat_candidats_par_etablissement_avec_num_table': // détail pour établissement     
            $where="";
            if($stats_departement>0) $where="\n AND can.departement=$stats_departement ";
            if($stats_centre>0) $where.="\n AND rep.id_etablissement=$stats_centre ";
            if($stats_etablissement>0) $where.="\n AND can.etablissement=$stats_etablissement ";
            
				$sql = "SELECT can.id_table , can.id_saisie , serie , prefixe , nom , prenoms , sexe , ne_le , ne_en , ne_vers , ldn , lv1 , lv2 , eps , ef1 , ef2 , can.departement , can.etablissement , rep.id_etablissement centre "
					  . "\n FROM odb_candidats can "
					  . "\n LEFT JOIN odb_repartition rep on can.id_saisie = rep.id_saisie "
					  . "\n AND rep.annee = $annee "
					  . "\n LEFT JOIN odb_ref_etablissement cen on cen.id = rep.id_etablissement "
					  . "\n LEFT JOIN odb_ref_departement dep on dep.id = cen.id_departement "
					  . "\n WHERE can.annee = $annee "
					  . $where
					  . "\n ORDER BY etablissement , serie , nom , prenoms"
					  ;
            //echo "<pre>$sql</pre>";
            $annee_stats=$annee;
            $result=odb_query($sql,__FILE__,__LINE__);
            $nb_rows=mysql_num_rows($result);
            $pdf_cols=array('id_table'=>'Num table',
            					 'Candidat'=>'Candidat',
                            'Date_naissance'=>'Date et lieu de naissance',
                            'Serie'=>'Serie',
                            'LV1'=>'LV1',
                            'LV2'=>'LV2',
                            'EPS'=>'EPS',
                            'Ep_fac_1'=>'Ep. fac. 1',
                            'Ep_fac_2'=>'Ep. fac. 2',
                            'Centre'=>'Centre',
                           );
				$pdf_cols_id=array('Num_saisie'=>'Num saisie');
				$isNumTableOld=false;// si passe a vrai on affiche la colonne NumTableOld
            while($row=mysql_fetch_array($result)) {
               foreach(array('id_table','id_table_old','id_saisie','serie','prefixe','nom','prenoms','sexe','ne_le','ne_en','ne_vers','ldn','lv1','lv2','eps','ef1','ef2','departement','etablissement','centre') as $champ)
                  $$champ=stripslashes($row[$champ]);
               if($id_table!='0') {
               	$id_table=getIdTableHumain($id_table);
						$isNumTable=true;
						if($id_table_old!='') $isNumTableOld=true;
					}
               $id_departement=$departement;
               $departement=$tab_referentiel['departement'][$id_departement];
               $etablissement=$tab_referentiel['etablissement'][$id_departement][$etablissement];
               $etablissement=utf8_decode($etablissement);
               $prefixe=strtolower($tab_referentiel['prefixe'][$prefixe]);
               $serie=$tab_referentiel['serie'][$serie];
               $nom=strtoupper($nom);
               $prenoms=ucwords(strtolower($prenoms));
               $sexe=$tab_referentiel['sexe'][$sexe];
               $sexe=$sexe=='M'?'M.':'Mlle';
               $nom="$sexe $prefixe <b>$nom</b> $prenoms";
               if($ne_en>0) $ddn="En $ne_en";
               elseif($ne_vers>0) $ddn="Vers $ne_vers";
               else {
                  $tDate=explode('-',$ne_le);
                  $annee=$tDate[0];
                  $mois=$tDate[1];
                  $jour=$tDate[2];
                  $ddn="$jour/$mois/$annee";
               }
               $ddn.=" $ldn";
               $lv1=$tab_referentiel['lv'][$lv1];
               $lv2=$tab_referentiel['lv'][$lv2];
               $ef1=$tab_referentiel['ef'][$ef1];
               $ef2=$tab_referentiel['ef'][$ef2];
               $eps=$tab_referentiel['eps'][$eps];
               if(!is_array($tab_centres[$centre])) {
                  if(isset($tab_referentiel['etablissement'][$id_departement][$centre])) {
                     $tab_centres['centre'][$centre]=$tab_referentiel['etablissement'][$id_departement][$centre];
                     $tab_centres['departement'][$centre]=$departement;
                  }
                  else foreach($tab_referentiel['etablissement'] as $id_dept=>$tab)
                     if(isset($tab[$centre])) {
                        $tab_centres['centre'][$centre]=$tab[$centre];
                        $tab_centres['departement'][$centre]=$tab_referentiel['departement'][$id_dept];
                     }
               }
               $departement_centre=$tab_centres['departement'][$centre];
               $centre=$tab_centres['centre'][$centre];
               if($etablissement!=$etablissement_old) $cpt_pdf=0;
               else $cpt_pdf++;

               foreach(array('id_table','nom','ddn','lv1','lv2','eps','ef1','ef2','centre') as $var)
                  $tab_can[$departement][$etablissement][$serie][$id_saisie][$var]=stripslashes($$var);
               $pdf[$departement][$etablissement][$cpt_pdf]['id_table']=$id_table;
               $pdf[$departement][$etablissement][$cpt_pdf]['Num_table_old']=$id_table_old;
               $pdf[$departement][$etablissement][$cpt_pdf]['Candidat']=utf8_decode($nom);
               $pdf[$departement][$etablissement][$cpt_pdf]['Date_naissance']=utf8_decode($ddn);
               $pdf[$departement][$etablissement][$cpt_pdf]['Serie']=$serie;
               $pdf[$departement][$etablissement][$cpt_pdf]['LV1']=utf8_decode($lv1);
               $pdf[$departement][$etablissement][$cpt_pdf]['LV2']=utf8_decode($lv2);
               $pdf[$departement][$etablissement][$cpt_pdf]['EPS']=utf8_decode($eps);
               $pdf[$departement][$etablissement][$cpt_pdf]['Ep_fac_1']=utf8_decode($ef1);
               $pdf[$departement][$etablissement][$cpt_pdf]['Ep_fac_2']=utf8_decode($ef2);
               $pdf[$departement][$etablissement][$cpt_pdf]['Centre']=utf8_decode($centre);
               $etablissement_old=$etablissement;
            }
				if($isNumTableOld) $pdf_cols_id=array_merge($pdf_cols,array('Num_table_old'=>'Ancien num table'));

            $msg="";
            if(is_array($tab_can)) ksort($tab_can);
            else die(KO." - Veuillez commencer par la r&eacute;partition des candidats $annee");

            foreach($tab_can as $departement=>$tab1) {
               //departement
               ksort($tab1);
               if($stats_centre>0)
                  if(isset($tab1[$etablissement_txt])) {
                     $tab_tmp[$etablissement_txt]=$tab1[$etablissement_txt];
                     unset($tab1);
                     $tab1=$tab_tmp;
                  }

               foreach($tab1 as $etablissement=>$tab2) {
                  //Etablissement
                  ksort($tab2);
                  foreach($tab2 as $serie=>$tab3) {
                     $srcFond=texte90(" $departement - $etablissement - $serie ",5,30,400,'src');
                     $nbCandidats=count($tab3);
                     if($nbCandidats>10) $imgFond=texte90(" $departement - $etablissement - $serie ",5,30,400);
                     else $imgFond='';
                     $msg.="<h1>$departement</h1>\n"
                         . "<h2>&Eacute;tablissement : $etablissement</h2>\n"
                         . "<h3>S&eacute;rie $serie - $nbCandidats candidat(e)s</h3>\n"
                         . "<table class='spip' width='100%' border=0 cellpadding=1 cellspacing=0 style='border: 1px solid gray;'>\n"
                         . "<tr><th background='$srcFond' rowspan=".($nbCandidats+1)." valign='top' width='30'>$imgFond</th>\n"
                         ;
                         //Num de saisie enleve
                     foreach(array('id Table','Candidat','Date et lieu de naissance','LV1','LV2','EPS','Epr. Fac. 1','Epr. Fac. 2','Centre<br/>de composition') as $col)
                        $msg.="\t<th><small>$col</small></th>\n";
                     $msg.="</tr>\n";
                     foreach($tab3 as $id_table=>$tab) {
                        $msg.="<tr class='tr_liste'>\n\t";
                        
                        foreach($tab as $key=>$col)
                           if($key!='id_saisie')
                              $msg.="\t<td><small>$col</small></td>\n";
                        $msg.="\t<td>&nbsp;</td>\n</tr>\n";
                     }
                     $msg.="</table>\n";
                     $nom_pdf=getRewriteString("$departement|$etablissement");
                     $_SESSION['data'][$nom_pdf]=$pdf[$departement][$etablissement];
                     $_SESSION['pied'][$nom_pdf]="Candidats $etablissement ($departement)";
                     $_SESSION['titre'][$nom_pdf]=html_entity_decode("Liste d'inscription des candidats &agrave; l'examen du Bac - Session unique de juin ".date("Y")." - Etablissement : $etablissement");
                     $_SESSION['cols'][$nom_pdf]=$pdf_cols;
                     $_SESSION['options'][$nom_pdf]=$PDF_A3_PAYSAGE;
                     $tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
                     $tmp2=" G&eacute;n&eacute;rer la liste de l'&eacute;tablissement <b>$etablissement</b> en PDF</A>";
                     $msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer la liste de $etablissement en PDF").$tmp2;
                     $lien_pdf[$nom_pdf]=$tmp1.$tmp2;
                  }
               }
            }
         break;
         
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'liste_d_inscription_des_candidats_affichage':
            $where="";
            if($stats_departement>0) $where=" AND cen.id_departement=$stats_departement ";
            if($stats_serie>0) $where.=" AND can.serie=$stats_serie ";
            if($stats_centre>0) $where.=" AND rep.id_etablissement=$stats_centre ";
            $sql="SELECT rep.id_table, rep.id_saisie, serie, prefixe, nom, prenoms, sexe, ne_le, ne_en, ne_vers, ldn, lv1, lv2, eps, ef1, ef2, cen.id_departement departement_centre, can.departement, can.etablissement, rep.id_etablissement centre"
               . " FROM odb_repartition rep, odb_candidats can, odb_ref_departement dep, odb_ref_etablissement cen"
               . " WHERE can.annee=$annee AND rep.annee=$annee"
               . $where
               . " AND can.id_saisie=rep.id_saisie"
               . " AND cen.id=rep.id_etablissement AND dep.id=cen.id_departement"
               . " ORDER BY centre, serie, nom, prenoms"
               ;
               $annee_stats=$annee;
            $result=odb_query($sql,__FILE__,__LINE__);
            $nb_rows=mysql_num_rows($result);
            $pdf_cols=array('Num_table'=>'Num table',
                                  'Candidat'=>'Candidat',
                                  'Date_naissance'=>'Date et lieu de naissance',
                                  'Serie'=>'Serie',
                                  'LV1'=>'LV1',
                                  'LV2'=>'LV2',
                                  'EPS'=>'EPS',
                                  'Ep_fac_1'=>'Ep. fac. 1',
                                  'Ep_fac_2'=>'Ep. fac. 2',
                                  'Etablissement'=>'Etablissement',
                                  'Emargement'=>'Emargement'
                                 );
            while($row=mysql_fetch_array($result)) {
               foreach(array('id_table','id_table_old','id_saisie','serie','prefixe','nom','prenoms','sexe','ne_le','ne_en','ne_vers','ldn','lv1','lv2','eps','ef1','ef2','departement','etablissement','centre') as $champ)
                  $$champ=stripslashes($row[$champ]);
               $id_table=getIdTableHumain($id_table);
               $id_departement=$departement;
               $departement=$tab_referentiel['departement'][$id_departement];
               $etablissement=$tab_referentiel['etablissement'][$id_departement][$etablissement];
               $prefixe=strtolower($tab_referentiel['prefixe'][$prefixe]);
               $serie=$tab_referentiel['serie'][$serie];
               $nom=strtoupper($nom);
               $prenoms=ucwords(strtolower($prenoms));
               $sexe=$tab_referentiel['sexe'][$sexe];
               $sexe=$sexe=='M'?'M.':'Mlle';
               $nom="$sexe $prefixe <b>$nom</b> $prenoms";
               if($ne_en>0) $ddn="En $ne_en";
               elseif($ne_vers>0) $ddn="Vers $ne_vers";
               else {
                  $tDate=explode('-',$ne_le);
                  $annee=$tDate[0];
                  $mois=$tDate[1];
                  $jour=$tDate[2];
                  $ddn="$jour/$mois/$annee";
               }
               $ddn.=" $ldn";
               $lv1=$tab_referentiel['lv'][$lv1];
               $lv2=$tab_referentiel['lv'][$lv2];
               $ef1=$tab_referentiel['ef'][$ef1];
               $ef2=$tab_referentiel['ef'][$ef2];
               $eps=$tab_referentiel['eps'][$eps];
               if(!is_array($tab_centres[$centre])) {
                  if(isset($tab_referentiel['etablissement'][$id_departement][$centre])) {
                     $tab_centres['centre'][$centre]=$tab_referentiel['etablissement'][$id_departement][$centre];
                     $tab_centres['departement'][$centre]=$departement;
                  }
                  else foreach($tab_referentiel['etablissement'] as $id_dept=>$tab)
                     if(isset($tab[$centre])) {
                        $tab_centres['centre'][$centre]=$tab[$centre];
                        $tab_centres['departement'][$centre]=$tab_referentiel['departement'][$id_dept];
                     }
               }
               $departement_centre=$tab_centres['departement'][$centre];
               $centre=$tab_centres['centre'][$centre];
               $centre=utf8_decode($centre);
               if($centre!=$centre_old) $cpt_pdf=0;
               else $cpt_pdf++;
               foreach(array('id_saisie','nom','ddn','lv1','lv2','eps','ef1','ef2','etablissement') as $var)
                  $tab_can[$departement_centre][$centre][$serie][$id_table][$var]=stripslashes($$var);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Num_table']=$id_table;
               $pdf[$departement_centre][$centre][$cpt_pdf]['Num_tableOld']=$id_table_old;
               $pdf[$departement_centre][$centre][$cpt_pdf]['Candidat']=utf8_decode($nom);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Date_naissance']=utf8_decode($ddn);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Serie']=$serie;
               $pdf[$departement_centre][$centre][$cpt_pdf]['LV1']=utf8_decode($lv1);
               $pdf[$departement_centre][$centre][$cpt_pdf]['LV2']=utf8_decode($lv2);
               $pdf[$departement_centre][$centre][$cpt_pdf]['EPS']=utf8_decode($eps);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Ep_fac_1']=utf8_decode($ef1);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Ep_fac_2']=utf8_decode($ef2);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Etablissement']=utf8_decode($etablissement);
               $centre_old=$centre;
               if($id_table_old!='')
                  $pdf_cols=array('Num_table'=>'Num table',
                                  'Num_tableOld'=>'Ancien num table',
                                  'Candidat'=>'Candidat',
                                  'Date_naissance'=>'Date et lieu de naissance',
                                  'Serie'=>'Serie',
                                  'LV1'=>'LV1',
                                  'LV2'=>'LV2',
                                  'EPS'=>'EPS',
                                  'Ep_fac_1'=>'Ep. fac. 1',
                                  'Ep_fac_2'=>'Ep. fac. 2',
                                  'Etablissement'=>'Etablissement',
                                  'Emargement'=>'Emargement'
                                 );
            }
            $msg="";
            ksort($tab_can);

            foreach($tab_can as $departement=>$tab1) {
               //departement
               ksort($tab1);
               if($stats_centre>0)
                  if(isset($tab1[$etablissement_txt])) {
                     $tab_tmp[$etablissement_txt]=$tab1[$etablissement_txt];
                     unset($tab1);
                     $tab1=$tab_tmp;
                  }

               foreach($tab1 as $centre=>$tab2) {
                  //Centre de composition
                  ksort($tab2);
                  foreach($tab2 as $serie=>$tab3) {
                     $srcFond=texte90(" $departement - $centre - $serie ",5,30,400,'src');
                     $nbCandidats=count($tab3);
                     if($nbCandidats>10) $imgFond=texte90(" $departement - $etablissement - $serie ",5,30,400);
                     else $imgFond='';
                     $msg.="<h1>$departement</h1>\n"
                         . "<h2>Centre de composition : $centre</h2>\n"
                         . "<h3>S&eacute;rie $serie - $nbCandidats candidat(e)s</h3>\n"
                         . "<table class='spip' width='100%' border=0 cellpadding=1 cellspacing=0 style='border: 1px solid gray;'>\n"
                         . "<tr><th background='$srcFond' rowspan=".($nbCandidats+1)." valign='top' width='30'>$imgFond</th>\n"
                         ;
                     foreach(array('N&deg; table','Candidat','Date et lieu de naissance','LV1','LV2','EPS','Epr. Fac. 1','Epr. Fac. 2','&Eacute;tablissement<br/>d\'origine') as $col)
                        $msg.="\t<th><small>$col</small></th>\n";
                     $msg.="</tr>\n";
                     foreach($tab3 as $id_table=>$tab) {
                        $msg.="<tr class='tr_liste'>\n\t"
                            . "<td><small><b><a href='".generer_url_ecrire('odb_saisie')."&annee=$annee_stats&step2=odb_candidats&identifiant=id_saisie&id=".$tab['id_saisie']."'>$id_table</a></b></small></td>\n"
                            ;
                        foreach($tab as $key=>$col)
                           if($key!='id_saisie')
                              $msg.="\t<td><small>$col</small></td>\n";
                        $msg.="\t<td>&nbsp;</td>\n</tr>\n";
                     }
                     $msg.="</table>\n";
                     $nom_pdf=getRewriteString("$departement|$centre");
                     $_SESSION['data'][$nom_pdf]=$pdf[$departement][$centre];
                     $_SESSION['titre'][$nom_pdf]=html_entity_decode("Liste d'inscription des candidats &agrave; l'examen du bac - Affichage - Session unique de juin ".date("Y")." - centre : $centre");
                     $_SESSION['cols'][$nom_pdf]=$pdf_cols;
                     $_SESSION['pied'][$nom_pdf]="Centre de composition $centre ($departement)";
                     $_SESSION['options'][$nom_pdf]=$PDF_A3_PAYSAGE;
                     $tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
                     $tmp2=" G&eacute;n&eacute;rer la liste d'affichage du centre <b>$centre</b> en PDF</A>";
                     $msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer la liste d'affichage de $centre en PDF").$tmp2;
                     $lien_pdf[$nom_pdf]=$tmp1.$tmp2;
                  }
               }
            }
         break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'liste_d_inscription_des_candidats':
            $where="";
            if($stats_departement>0) $where=" AND cen.id_departement=$stats_departement ";
            if($stats_centre>0) $where.=" AND rep.id_etablissement=$stats_centre ";
            if($stats_serie>0) $where.=" AND can.serie=$stats_serie ";
            $sql="SELECT rep.id_table, can.id_table_old, rep.id_saisie, serie, prefixe, nom, prenoms, sexe, ne_le, ne_en, ne_vers, ldn, lv1, lv2, eps, ef1, ef2, cen.id_departement departement_centre, can.departement, can.etablissement, rep.id_etablissement centre"
               . " FROM odb_repartition rep, odb_candidats can, odb_ref_departement dep, odb_ref_etablissement cen"
               . " WHERE can.annee=$annee AND rep.annee=$annee"
               . $where
               . " AND can.id_saisie=rep.id_saisie"
               . " AND cen.id=rep.id_etablissement AND dep.id=cen.id_departement"
               . " ORDER BY centre, id_table, nom, prenoms"
               ;
            //echo $sql;
            $annee_stats=$annee;
            $result=odb_query($sql,__FILE__,__LINE__);
            $nb_rows=mysql_num_rows($result);
            $pdf_cols=array('Num_table'=>'Num table',
                                  'Candidat'=>'Candidat',
                                  'Date_naissance'=>'Date et lieu de naissance',
                                  'Serie'=>'Serie',
                                  'LV1'=>'LV1',
                                  'LV2'=>'LV2',
                                  'EPS'=>'EPS',
                                  'Ep_fac_1'=>'Ep. fac. 1',
                                  'Ep_fac_2'=>'Ep. fac. 2',
                                  'Etablissement'=>'Etablissement',
                                  'Emargement'=>'Emargement'
                                 );
            while($row=mysql_fetch_array($result)) {
               foreach(array('id_table','id_table_old','id_saisie','serie','prefixe','nom','prenoms','sexe','ne_le','ne_en','ne_vers','ldn','lv1','lv2','eps','ef1','ef2','departement','etablissement','centre') as $champ)
                  $$champ=stripslashes($row[$champ]);
               $id_table=getIdTableHumain($id_table);
               $id_departement=$departement; 
               $departement=$tab_referentiel['departement'][$id_departement];
               $etablissement=$tab_referentiel['etablissement'][$id_departement][$etablissement];
               $prefixe=strtolower($tab_referentiel['prefixe'][$prefixe]);
               $serie=$tab_referentiel['serie'][$serie];
               $nom=strtoupper($nom);
               $prenoms=ucwords(strtolower($prenoms));
               $sexe=$tab_referentiel['sexe'][$sexe];
               $sexe=$sexe=='M'?'M.':'Mlle';
               $nom="$sexe $prefixe <b>$nom</b> $prenoms";
               if($ne_en>0) $ddn="En $ne_en";
               elseif($ne_vers>0) $ddn="Vers $ne_vers";
               else {
                  $tDate=explode('-',$ne_le);
                  $annee=$tDate[0];
                  $mois=$tDate[1];
                  $jour=$tDate[2];
                  $ddn="$jour/$mois/$annee";
               }
               $ddn.=" $ldn";
               $lv1=$tab_referentiel['lv'][$lv1];
               $lv2=$tab_referentiel['lv'][$lv2];
               $ef1=$tab_referentiel['ef'][$ef1];
               $ef2=$tab_referentiel['ef'][$ef2];
               $eps=$tab_referentiel['eps'][$eps];
               if(!is_array($tab_centres[$centre])) {
                  if(isset($tab_referentiel['etablissement'][$id_departement][$centre])) {
                     $tab_centres['centre'][$centre]=$tab_referentiel['etablissement'][$id_departement][$centre];
                     $tab_centres['departement'][$centre]=$departement;
                  }
                  else foreach($tab_referentiel['etablissement'] as $id_dept=>$tab)
                     if(isset($tab[$centre])) {
                        $tab_centres['centre'][$centre]=$tab[$centre];
                        $tab_centres['departement'][$centre]=$tab_referentiel['departement'][$id_dept];
                     }
               }
               $departement_centre=$tab_centres['departement'][$centre];
               $centre=$tab_centres['centre'][$centre];
               $centre=utf8_decode($centre);
               if($centre!=$centre_old) $cpt_pdf=0;
               else $cpt_pdf++;
               foreach(array('id_saisie','nom','ddn','lv1','lv2','eps','ef1','ef2','etablissement') as $var)
                  $tab_can[$departement_centre][$centre][$serie][$id_table][$var]=$$var;
               $pdf[$departement_centre][$centre][$cpt_pdf]['Num_table']=$id_table;
               $pdf[$departement_centre][$centre][$cpt_pdf]['Num_tableOld']=$id_table_old;
               $pdf[$departement_centre][$centre][$cpt_pdf]['Candidat']=utf8_decode($nom);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Date_naissance']=utf8_decode($ddn);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Serie']=$serie;
               $pdf[$departement_centre][$centre][$cpt_pdf]['LV1']=utf8_decode($lv1);
               $pdf[$departement_centre][$centre][$cpt_pdf]['LV2']=utf8_decode($lv2);
               $pdf[$departement_centre][$centre][$cpt_pdf]['EPS']=utf8_decode($eps);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Ep_fac_1']=utf8_decode($ef1);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Ep_fac_2']=utf8_decode($ef2);
               $pdf[$departement_centre][$centre][$cpt_pdf]['Etablissement']=utf8_decode($etablissement);
               $centre_old=$centre;
               if($id_table_old!='')
                  $pdf_cols=array('Num_table'=>'Num table',
                                  'Num_tableOld'=>'Ancien num table',
                                  'Candidat'=>'Candidat',
                                  'Date_naissance'=>'Date et lieu de naissance',
                                  'Serie'=>'Serie',
                                  'LV1'=>'LV1',
                                  'LV2'=>'LV2',
                                  'EPS'=>'EPS',
                                  'Ep_fac_1'=>'Ep. fac. 1',
                                  'Ep_fac_2'=>'Ep. fac. 2',
                                  'Etablissement'=>'Etablissement',
                                  'Emargement'=>'Emargement'
                                 );
            }
            $msg="";
            ksort($tab_can);

            foreach($tab_can as $departement=>$tab1) {
               //departement
               ksort($tab1);
               if($stats_centre>0)
                  if(isset($tab1[$etablissement_txt])) {
                     $tab_tmp[$etablissement_txt]=$tab1[$etablissement_txt];
                     unset($tab1);
                     $tab1=$tab_tmp;
                  }

               foreach($tab1 as $centre=>$tab2) {
                  //Centre de composition
                  ksort($tab2);
                  foreach($tab2 as $serie=>$tab3) {
                     $srcFond=texte90(" $departement - $centre - $serie ",5,30,400,'src');
                     $nbCandidats=count($tab3);
                     if($nbCandidats>10) $imgFond=texte90(" $departement - $etablissement - $serie ",5,30,400);
                     else $imgFond='';
                     $msg.="<h1>$departement</h1>\n"
                         . "<h2>Centre de composition : $centre</h2>\n"
                         . "<h3>S&eacute;rie $serie - $nbCandidats candidat(e)s</h3>\n"
                         . "<table class='spip' width='100%' border=0 cellpadding=1 cellspacing=0 style='border: 1px solid gray;'>\n"
                         . "<tr><th background='$srcFond' rowspan=".($nbCandidats+1)." valign='top' width='30'>$imgFond</th>\n"
                         ;
                     foreach(array('N&deg; table','Candidat','Date et lieu de naissance','LV1','LV2','EPS','Epr. Fac. 1','Epr. Fac. 2','&Eacute;tablissement<br/>d\'origine','&Eacute;margement') as $col)
                        $msg.="\t<th><small>$col</small></th>\n";
                     $msg.="</tr>\n";
                     foreach($tab3 as $id_table=>$tab) {
                        $msg.="<tr class='tr_liste'>\n\t"
                            . "<td><small><b><a href='".generer_url_ecrire('odb_saisie')."&annee=$annee_stats&step2=odb_candidats&identifiant=id_saisie&id=".$tab['id_saisie']."'>$id_table</a></b></small></td>\n"
                            ;
                        foreach($tab as $key=>$col)
                           if($key!='id_saisie')
                              $msg.="\t<td><small>$col</small></td>\n";
                        $msg.="\t<td>&nbsp;</td>\n</tr>\n";
                     }
                     $msg.="</table>\n";
                     $nom_pdf=getRewriteString("$departement|$centre");
                     $_SESSION['data'][$nom_pdf]=$pdf[$departement][$centre];
                     $_SESSION['titre'][$nom_pdf]=html_entity_decode("Liste d'inscription des candidats &agrave; l'examen du bac - &Eacute;margement - Session unique de juin ".date("Y")." - centre : $centre");
                     $_SESSION['cols'][$nom_pdf]=$pdf_cols;
                     $_SESSION['pied'][$nom_pdf]="Centre de composition $centre ($departement)";
                     $_SESSION['options'][$nom_pdf]=$PDF_A3_PAYSAGE;
                     $tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
                     $tmp2=" G&eacute;n&eacute;rer la liste d'&eacute;margement du centre <b>$centre</b> en PDF</A>";
                     $msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer la liste d'&eacute;margement de $centre en PDF").$tmp2;
                     $lien_pdf[$nom_pdf]=$tmp1.$tmp2;
                  }
               }
            }
         break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_repartition_des_candidats_par_departement_centre_serie':
            $where='';
            $tab_series=array();
            $sql="select distinct serie from odb_ref_serie order by serie";
            $result=odb_query($sql,__FILE__,__LINE__);
            while($row=mysql_fetch_array($result)) // affecte $tab_series en une ligne
               $tab_series[]=$row['serie'];
            
            if($stats_departement>0) $where=" AND cen.id_departement=$stats_departement ";
            if($stats_centre>0) $where.=" AND rep.id_etablissement=$stats_centre ";
            $sql = 'select dep.departement, cen.etablissement centre, ser.serie, count(*) nb_candidats'
                 . ' from odb_candidats can, odb_repartition rep, odb_ref_departement dep, odb_ref_etablissement cen, odb_ref_serie ser'
                 . ' where can.id_table = rep.id_table'
                 . " and can.annee=$annee and rep.annee=$annee"
                 . ' and cen.id_departement=dep.id'
                 . ' and cen.id=rep.id_etablissement'
                 . ' and ser.id=can.serie'
                 . $where
                 . ' group by dep.departement, centre, ser.serie'
                 . ' order by dep.departement, centre, ser.serie'
                 ;
            //echo $sql;
            $result=odb_query($sql,__FILE__,__LINE__);
            $nb_rows=mysql_num_rows($result);
            $colonnes=array('departement','centre','serie','nb_candidats');
            while($row=mysql_fetch_array($result)) {
               foreach($colonnes as $col)
                  $$col=$row[$col];
               $tab_repartition[$departement][$centre][$serie]=$nb_candidats;
            }
            $msg="<table width='100%'>\n";
            $msg.="<tr>\n\t<th><small>D&eacute;partement</small></th>\n\t<th><small>Centre</small></th>\n";
            foreach($tab_series as $serie)
               $msg.="\t<th><small>$serie</small></th>\n";
            $msg.="\t<th><small>Total</small></th>\n</tr>\n";
            if(is_array($tab_repartition)) {
               foreach($tab_repartition as $departement=>$tab1) {
                  foreach($tab1 as $centre=>$tab2) {
                     $msg.="<tr class='tr_liste'>\n\t<td><small>$departement</small></td>\n\t<td><small>$centre</small></td>\n";
                     foreach($tab_series as $serie) {
                        $msg.="\t<td><small>".$tab2[$serie]."</small></td>\n";
                        $total['centre'][$centre]+=$tab2[$serie];
                        $total['serie'][$serie]+=$tab2[$serie];
                     }
                     $msg.="<td><small>".$total['centre'][$centre]."</small></td></tr>\n";
                  }
               }
            }
            $msg.="<tr>\n<td colspan=2><small>Total s&eacute;rie</small></td>\n";
            foreach($tab_series as $serie) {
               $msg.="\t<td><small>".$total['serie'][$serie]."</small></td>\n";
               $total_tous+=$total['serie'][$serie];
            }
            $msg.="\t<td>$total_tous</td>\n</tr>\n";
            $msg.="</table>\n";
         break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'impression_des_convocations':
            $where='';
            if($stats_departement>0) $where=" AND eta.id_departement=$stats_departement ";
            if($stats_serie>0) $where=" AND can.serie=$stats_serie ";
            if($stats_etablissement>0) $where=" AND can.etablissement=$stats_etablissement ";
            $sql = 'select dep.departement, eta.etablissement, ser.serie, count(*) nb'
                 . ' from odb_ref_etablissement eta, odb_ref_departement dep, odb_candidats can, odb_ref_serie ser'
                 . " where eta.id_departement=dep.id and annee=$annee and can.etablissement=eta.id and ser.id=can.serie $where"
                 . ' group by dep.departement, eta.etablissement, serie'
                 . ' order by dep.departement, eta.etablissement, serie'
                 ;
            //echo $sql;
            $result=odb_query($sql,__FILE__,__LINE__);
            while($row=mysql_fetch_array($result)) {
               $departement=$row['departement'];
               $etablissement=$row['etablissement'];
               $serie=$row['serie'];
               $nb=$row['nb'];
               $tDepEta[$departement][$etablissement][$serie]=$nb;
            }
            foreach($tDepEta as $departement=>$t1) {
            	$nb=0;
            	foreach($t1 as $etablissement=>$t2) {
            		foreach($t2 as $cpt) $nb+=$cpt;
            	}
               $msg.="<h1>$departement ".icones_impression_convocation($annee,'','',$nb,'',$tab_referentiel['departement'][$departement],$departement)."</h1>\n";
               foreach($t1 as $etablissement=>$t2) {
                  $id_departement=$tab_referentiel['departement'][$departement];
                  $id_etablissement=$tab_referentiel['etablissement'][$id_departement][$etablissement];
                  $nb=0;
                  foreach($t2 as $cpt) $nb+=$cpt;
                  if($nb>1) $s='s';else $s='';
                  $msg.="<table width='100%' style='border-top: 1px solid #CCC;'><tr><td valign='top' width='60%'>";
                  $msg.="<h3>$etablissement</h3>\n<b>$nb candidat$s ".icones_impression_convocation($annee,$id_etablissement,$etablissement,$nb)."</b>\n"
                      . "</td><td>\n"
                      . "<table>\n<tr><th><small>S&eacute;rie</small></th><th><small>Nombre</small></th><th><small>Convocations</small></th></tr>\n";
                      ;
                  foreach($t2 as $serie=>$nb) {
                     //echo "$departement $id_departement - $etablissement $id_etablissement<br/>";
                     $lien_convoc=icones_impression_convocation($annee,$id_etablissement,$etablissement,$nb,$serie);
                     $msg.="<tr class='tr_liste'><td><small>$serie</small></td><td><small>$nb</small></td><td><small>$lien_convoc Imprimer</small></td></tr>\n";
                  }
                  $msg.="</table></td></tr></table>\n";
               }
            }
         break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_repartition_des_candidats_inscrits_par_serie_et_departement':
            $where='';
            $tab_series=array();
            $sql="select distinct serie from odb_ref_serie order by serie";
            $result=odb_query($sql,__FILE__,__LINE__);
            while($row=mysql_fetch_array($result)) // affecte $tab_series en une ligne
               $tab_series[]=$row['serie'];

            if($stats_departement>0) $where=" AND cen.id_departement=$stats_departement ";
            if($stats_centre>0) $where.=" AND rep.id_etablissement=$stats_centre ";
            $sql = 'select dep.departement, ser.serie, sex.sexe, count(*) nb_candidats'
                 . ' from odb_candidats can, odb_repartition rep, odb_ref_departement dep, odb_ref_etablissement cen, odb_ref_serie ser,'
                 . ' odb_ref_sexe sex'
                 . " where can.id_table = rep.id_table and can.annee=$annee and rep.annee=$annee and cen.id_departement=dep.id"
                 . ' and cen.id=rep.id_etablissement and ser.id=can.serie and sex.id=can.sexe'
                 . $where
                 . ' group by dep.departement, ser.serie, sex.sexe'
                 . ' order by dep.departement, ser.serie, sex.sexe'
                 ;
            //echo $sql;
            $result=odb_query($sql,__FILE__,__LINE__);
            $nb_rows=mysql_num_rows($result);
            $colonnes=array('departement','sexe','serie','nb_candidats');
            while($row=mysql_fetch_array($result)) {
               foreach($colonnes as $col)
                  $$col=$row[$col];
               $tab_repartition[$departement][$serie][$sexe]=$nb_candidats;
            }
            //print_r($tab_repartition);
            $msg="<table width='100%'>\n";
            $msg.="<tr>\n\t<th><small>S&eacute;rie</small></th>\n";
            foreach($tab_series as $serie)
               $msg.="\t<th colspan=3><small>$serie</small></th>\n";
            $msg.="\t<th colspan='3'><small>Total</small></th>\n</tr>\n";
            $msg.="<tr style='font-size:10px;'>\n\t<th><small>D&eacute;partement</small></th>\n";
            foreach($tab_series as $serie)
               $msg.="\t<th><small>M</small></th>\n\t<th><small>F</small></th>\n\t<th><small>T</small></th>\n";
            $msg.="\t<th><small>M</small></th>\n\t<th><small>F</small></th>\n\t<th><small>T</small></th>\n</tr>\n";

            if(is_array($tab_repartition)) {
               foreach($tab_repartition as $departement=>$tab1) {
                  $msg.="<tr class='tr_liste' style='font-size:10px;'>\n\t<td><small>$departement</small></td>\n\t";
                  foreach($tab_series as $serie) {
                     $msg.="\t<td><small>".$tab1[$serie]['M']."</small></td>\n";
                     $msg.="\t<td><small>".$tab1[$serie]['F']."</small></td>\n";
                     $msg.="\t<td><small>".($tab1[$serie]['M']+$tab1[$serie]['F'])."</small></td>\n";
                     $total['departement'][$departement]['M']+=$tab1[$serie]['M'];
                     $total['departement'][$departement]['F']+=$tab1[$serie]['F'];
                     $total['serie'][$serie]['M']+=$tab1[$serie]['M'];
                     $total['serie'][$serie]['F']+=$tab1[$serie]['F'];
                  }
                  $msg.="<td><small>".$total['departement'][$departement]['M']."</small></td><td><small>".$total['departement'][$departement]['F']."</small></td><td><small>".($total['departement'][$departement]['M']+$total['departement'][$departement]['F'])."</small></td></tr>\n";
               }
            }
            $msg.="<tr style='font-size:10px;'>\n<td><small>Total s&eacute;rie</small></td>\n";
            foreach($tab_series as $serie) {
               $msg.="\t<td><small>".$total['serie'][$serie]['M']."</small></td>\n";
               $msg.="\t<td><small>".$total['serie'][$serie]['F']."</small></td>\n";
               $msg.="\t<td><small>".($total['serie'][$serie]['M']+$total['serie'][$serie]['F'])."</small></td>\n";
               $total_tous_m+=$total['serie'][$serie]['M'];
               $total_tous_f+=$total['serie'][$serie]['F'];
            }
            $msg.="\t<td>$total_tous_m</td>\n";
            $msg.="\t<td>$total_tous_f</td>\n";
            $msg.="\t<td>".($total_tous_m+$total_tous_f)."</td>\n</tr>\n";
            $msg.="</table>\n";
         break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_preparation_des_jurys':
         	$msg="";$cpt_repartis=0;
            $numeroJury=$_REQUEST['numeroJury'];
				if($numeroJury!='') {      
					if($_REQUEST['viderJury']) {
						$sql="UPDATE odb_repartition SET jury=NULL where annee=$annee and jury=$numeroJury";
						odb_query($sql,__FILE__,__LINE__);
						$msg=OK." - Jury $numeroJury r&eacute;initialis&eacute; (contenait ".mysql_affected_rows()." candidats)";
					}   	
		      	//echo "$numero<br/>";
		      	else foreach($_REQUEST as $k=>$v) {
		      		if(substr_count($k,'jury|')>0 && $v>0) {
		      			$tmp=explode('|',$k);
		      			$centre=substr($tmp[1],strlen('centre'));
		      			$serie=substr($tmp[2],strlen('serie'));
		      			$sql="UPDATE odb_repartition SET jury=$numeroJury where annee=$annee and id_etablissement=$centre and jury is null and id_saisie in (select id_saisie from odb_candidats where annee=$annee and serie=$serie) limit $v";
		      			//echo "$centre $serie $v <pre>$sql</pre><br/>";
		      			odb_query($sql,__FILE__,__LINE__);
		      			$cpt_repartis+=$v;
		      		}
		      	}
		      	if($cpt_repartis>0) $msg=OK." - $cpt_repartis candidats attribu&eacute;s au jury $numeroJury";
         	}
         	$sql = 'SELECT dep.departement, eta . etablissement centre , ser . serie , jury , count( * ) nbCan '
			        . ' FROM odb_candidats can , odb_repartition rep , odb_ref_serie ser , odb_ref_etablissement eta, odb_ref_departement dep '
			        . ' WHERE can . id_saisie = rep . id_saisie '
			        . " AND can . annee = $annee "
			        . " AND rep . annee = $annee "
			        . ' AND can . serie = ser . id '
			        . ' AND rep . id_etablissement = eta . id '
			        . ' AND eta.id_departement = dep.id '
			        . ' AND jury is not null '
			        . ' GROUP BY centre , ser . serie , jury '
			        . ' ORDER BY centre , ser . serie , jury '
			        ;
			//echo $sql;
            $result=odb_query($sql,__FILE__,__LINE__);
            $nb_rows=mysql_num_rows($result);
            $colonnes=array('departement','centre','serie','jury','nbCan');
            while($row=mysql_fetch_array($result)) {
               foreach($colonnes as $col)
                  $$col=$row[$col];
               $tJury[$departement][$centre][$serie][$jury]=$nbCan;
               $tJury[$departement][$centre][$serie]['total']+=$nbCan;
               $tRepJury[$jury]['departement'][$departement]+=$nbCan;
               $tRepJury[$jury]['serie'][$serie]+=$nbCan;
            }
            if(is_array($tRepJury)) {
            	$nbCanTotal=0;
            	ksort($tRepJury);
            	//echo"tRepJury<pre>";print_r($tRepJury);echo "</pre>\n";
	            $msgJuryRep="<table class='spip'>\n<tr>\n";
	            foreach(array('Jury','D&eacute;partement','S&eacute;ries','Total') as $col) 
	            	$msgJuryRep.="\t<th>$col</th>\n";
	            $msgJuryRep.="</tr>\n";
	            foreach($tRepJury as $jury=>$t1) {
            		ksort($tRepJury[$jury]['departement']);
            		ksort($tRepJury[$jury]['serie']);
            		$tDepartements=array();
	            	$nbCanTotalJury=0;
	            	foreach($t1['departement'] as $departement=>$nbCan) {
	            		if($departement==$tab_referentiel['departement'][$stats_departement])
	            			$tDepartements[]="<b>$departement</b> <small>($nbCan)</small>";
	            		else 
	            			$tDepartements[]="<b>".$departement[0]."</b>".substr($departement,1)." <small>($nbCan)</small>";
	            		$tSeries=array();
	            	}
            		foreach($t1['serie'] as $serie=>$nbCan) {
            			$tSeries[]="<b>$serie</b> <small>($nbCan)</small>";
            			$nbCanTotalJury+=$nbCan;
         			}
	            	asort($tDepartements);
	            	asort($tSeries);

         			if($nbCanTotalJury<200 || $nbCanTotalJury>350) $nbCanAff="<div style='color:#F00;font-weight:bold;'>$nbCanTotalJury</div>";
         			elseif($nbCanTotalJury<250 || $nbCanTotalJury>300) $nbCanAff="<div style='color:#F90;font-weight:bold;'>$nbCanTotalJury</div>";
         			else $nbCanAff=$nbCanTotalJury;
           			$msgJuryRep.="<tr class='tr_liste'>\n"
						. "\t<td>Jury <b>$jury</b></td>\n"
						. "\t<td>".implode(', ',$tDepartements)."</td>\n"
           				. "\t<td>".implode('<br/>',$tSeries)."</td>\n"
           				. "\t<td>$nbCanAff</td>\n"
						. "</tr>\n"
						;
	      			$nbCanTotal+=$nbCanTotalJury;

	            }
	            $msgJuryRep.="<tr><th colspan=4>$nbCanTotal candidats affect&eacute;s &agrave; un jury</th></tr>\n";
	            $msgJuryRep.="</table>\n";
            } else $msgJuryRep='Aucun candidat n\'a &eacute;t&eacute; r&eacute;parti';

            $where='';
            if($stats_departement>0) $where=" AND eta.id_departement=$stats_departement ";
            if($stats_centre>0) $where.=" AND rep.id_etablissement=$stats_centre ";
            $sql = 'SELECT dep . departement , eta . etablissement centre, rep.id_etablissement id_centre, can.serie id_serie, ser . serie , rep.id_table, right( rep . id_table , 4 ) numCan '
                 . ' from odb_candidats can , odb_ref_serie ser , odb_repartition rep , odb_ref_etablissement eta , odb_ref_departement dep '
                 . ' where rep . id_etablissement = eta . id and can . id_table = rep . id_table and can . serie = ser . id and eta . id_departement = dep . id '
                 . " AND can.annee=$annee AND rep.annee=$annee $where "
                 . ' order by dep . departement , eta . etablissement , ser . serie, numCan '
                 ;
            //echo $sql;
            $result=odb_query($sql,__FILE__,__LINE__);
            $nb_rows=mysql_num_rows($result);
            $colonnes=array('departement','centre','serie','id_table','numCan','id_centre','id_serie');
            while($row=mysql_fetch_array($result)) {
               foreach($colonnes as $col)
                  $$col=$row[$col];
               $tab_repartition[$departement][$centre][$serie][]=$numCan;
               $tRef[$centre]=$id_centre;
               $tRef[$serie]=$id_serie;
            }

            //$tCanSansJury=array();
            foreach($tab_repartition as $departement=>$t1) 
               foreach($t1 as $centre=>$t2) {
                  foreach($t2 as $serie=>$t3) {
                  	 $nbLignes[$departement]++;
                  	 $nbCanRestant=count($t3)-$tJury[$departement][$centre][$serie]['total'];
                  	 if($nbCanRestant>0) 
                  	 	$tCanSansJury[$departement][$serie][$centre]=$nbCanRestant;
                     $oldCan=0;
                     $cpt=0;
                     $cptFourchette=0;
                     foreach($t3 as $numCan) {
                        if($oldCan==0 || $numCan-$oldCan==1) {
                           //les numéros se suivent
                           if($oldCan==0) {
                              $canFirst[$cptFourchette]=$numCan;
                           }
                           $canLast[$cptFourchette]=$numCan;
                        } else {
                           $cptFourchette++;
                           $canFirst[$cptFourchette]=$numCan;
                           $canLast[$cptFourchette]=$numCan;
                        }
                        $oldCan=(int)$numCan;
                        $cpt++;
                     }
                     for($i=0;$i<=$cptFourchette;$i++) {
                        //echo "Serie $serie : Fourchette $i : ".$canFirst[$i]."-".$canLast[$i]."<br />";
                        $tabFourchette[$departement][$centre][$serie][$i]=$canFirst[$i];
                        if($canLast[$cptFourchette]!=$canFirst[$i]) $tabFourchette[$departement][$centre][$serie][$i].="&rArr;".$canLast[$i];
                        $cptCan[$departement][$centre][$serie]=$cpt;
                     }
                  }
               }
            $nbCanTotal=0;
            if(is_array($tCanSansJury)>0) { 
	            foreach($tCanSansJury as $departement=>$t1) {
	            	$old_departement="";
	            	ksort($t1);
	            	foreach($t1 as $serie=>$t2) {
	            		$nbCanSerie=0;
	            		$tCentres=array();
	            		foreach($t2 as $centre=>$nbCan) {
	            			$tCentres[]="$centre <small>($nbCan)</small>";
	            			$nbCanSerie+=$nbCan;
	            		}
	            		//if($departement!=$old_departement) $departement="<b>$departement</b>";
	           			$msgCanSansJury.="<tr class='tr_liste'>\n"
										. "\t<td>$departement</td>\n"
										. "\t<td>$serie</td>\n"
	           						. "\t<td>".implode("<br/>",$tCentres)."</td>\n"
										. "\t<td>$nbCanSerie</td>\n"
										. "</tr>\n"
										;
						$nbCanTotal+=$nbCanSerie;
						$old_departement=$departement;
	            	}
	            }
	            $msgCanSansJury="<table class='spip'>\n<tr><th>D&eacute;partement</th><th>S&eacute;rie</th><th>Centres</th><th>Total</th></tr>\n"
								. $msgCanSansJury
	            				. "<tr><th colspan=4>$nbCanTotal candidats sans jury</th></tr>\n"
								. "</table>\n"
								;
            } else {
            	$msgCanSansJury="Tous les candidats sont affect&eacute;s &agrave; un jury";
            	if($stats_departement>0) $msgCanSansJury.="<br/>dans le d&eacute;partement <b>".$tab_referentiel['departement'][$stats_departement]."</b>";
            	else $msgCanSansJury.='<br/>au B&eacute;nin';
            }
			$msg.="\n<!-- tableaux de bord -->\n";
            $msg.="<table class='spip'>\n<tr>\n";
            $msg.="\t<th>Candidats affect&eacute;s &agrave; un jury</th>\n";
            $msg.="\t<th>Candidats sans jury</th>\n";
            $msg.="</tr>\n<tr valign='top'>\n";
            $msg.="<td style='vertical-align:top;'>\n<!-- Candidats affectes a un jury -->\n$msgJuryRep\n</td>\n";
            $msg.="<td style='vertical-align:top;'>\n<!-- Candidats sans jury -->\n$msgCanSansJury\n</td>\n";
            $msg.="</tr>\n</table>\n";
			$msg.="\n<!-- fin tableaux de bord -->\n";
            //echo"tabFourchettes<pre>";print_r($tabFourchette);echo"</pre>\n";
            $msg.="<table width='66%' class='spip'>\n<form name='form_jury' action='".generer_url_ecrire('odb_stats_siou')."' method='post'>";
            $msg.="<tr>\n\t<th><small>D&eacute;partement</small></th>\n\t<th><small>Centre</small></th>\n\t<th><small>S&eacute;rie</small></th>\n\t<th><small>Fourchettes</small></th>\n\t<th><small>Jurys</small></th></tr>\n";
			
            if(count($tabFourchette)>1)
            	$tLiensDepartements=array_keys($tabFourchette);
            $msgLiensDepartements="D&eacute;partements";
            if(is_array($tLiensDepartements))
            	foreach($tLiensDepartements as $dept)
            		$msgLiensDepartements.=" | <A HREF='#".getRewriteString($dept)."'>$dept</A>\n";
            $msgLiensDepartements.=" | <A HREF='#form_jury'><b>&darr;</b></A>\n"; 
            foreach($tabFourchette as $departement=>$t1) {
               foreach($t1 as $centre=>$t2) {
               	  ksort($t2);
               	  //echo "<hr>$centre<pre>";print_r($t2);echo"</pre>\n";
                  foreach($t2 as $serie=>$fourchettes) {
                  	 //echo "<br/>$serie : ".count($fourchettes). "fourchette(s)";
                     $msg.="<tr class='tr_liste' valign='top'>\n";
                     if($departement!=$departement_old) {
                     	$msg.="\t<td background='".texte90(" $departement ",5,40,200,$type='src')."' style='vertical-align:top;font-weight:bold;font-size:24px;background-repeat:repeat-y;' rowspan='".$nbLignes[$departement]."'><A NAME='".getRewriteString($departement)."'></a>$departement</td>\n";
                     	$index[]=$departement;
                     }
                     if($centre!=$centre_old) {
                     	$jurys="<table align='left'>\n<tr class='tr_liste'><th><small>S&eacute;rie</small></th><th><small>Jury</small></th><th><small>Nombre</small></th></tr>\n";
                     	if(is_array($tJury[$departement][$centre])) {
		                  	foreach($tJury[$departement][$centre] as $serie2=>$tj1)
		                  		foreach($tj1 as $jury=>$nbCan)
		                  			if($jury!='total') $jurys.="<tr class='tr_liste'><td><small>$serie2</small></td><td><small>$jury</small></td><td><small>$nbCan</small></td></tr>\n";
		                  	$jurys.="</table>\n";
		                  } else $jurys='<small>Aucun jury d&eacute;fini dans ce centre</small>';
                     	$msg.="\t<td style='vertical-align:top;font-weight:bold;font-size:18px;' rowspan='".count($t2)."'>$centre<p/>$jurys</td>\n";
                     }
                     $nbCan=$cptCan[$departement][$centre][$serie];
                     $nbCanRestant=$nbCan-$tJury[$departement][$centre][$serie]['total'];
                     if($nbCanRestant==0) $serie_aff="<div style='color:#0a0;font-weight:bold;'>$serie</div>";
                     elseif($nbCanRestant==$nbCan) $serie_aff="<div style='color:#f00;font-weight:bold;'>$serie</div>";
                     else $serie_aff="<div style='color:#f90;font-weight:bold;'>$serie</div>";
                     $msg.="\t<td>$serie_aff</td>\n";
                     $departement_old=$departement;
                     $centre_old=$centre;
                     //fourchettes
                     if($nbCanRestant>1) $s='s'; else $s='';
                     if($nbCan>$nbCanRestant) $nbCanAff="<b>$nbCanRestant</b>/$nbCan";
                     else $nbCanAff="<b>$nbCan</b>";
                     $msg.="\t<td><small>".implode("<br />+ ",$fourchettes)."<hr size=1 />= $nbCanAff candidat$s</small></td>\n";
                     $inputJury="<input name='jury|centre".$tRef[$centre]."|serie".$tRef[$serie]."' value='' size=3 maxlength=3 class='fondo' "
						. "onClick=\"if(this.value=='') {this.value=Math.min($nbCanRestant,300);this.select();}\" onKeyUp=\"if(this.value>$nbCan) this.value='$nbCan';\" onBlur=\"if(this.value==parseInt(this.value)) document.forms['form_jury'].nb.value=parseInt(document.forms['form_jury'].nb.value)+parseInt(this.value);document.forms['form_jury'].nb.style.backgroundColor=bgOld;\" onFocus=\"if(this.value==parseInt(this.value)) document.forms['form_jury'].nb.value=parseInt(document.forms['form_jury'].nb.value)-parseInt(this.value);bgOld=document.forms['form_jury'].nb.style.backgroundColor;document.forms['form_jury'].nb.style.backgroundColor='#f00';\""
						//. "title=\"header=[$imgInfo Pr&eacute;paration des jurys] body=[document.forms['form_jury'].nb.value] fade=[on] fadespeed=[0.08]\""
						."/>"; 
                     $msg.="\t<td>$inputJury</td>\n";
                     $msg.="</tr>\n";
                  }
               }
               $msg.="<tr><td colspan=4><center>$msgLiensDepartements</center></td></tr>\n";
            }
            $inputNbCanJury="<input name='nb' value='0' size=3 class='fondo'/>";
            $inputSelectJury="<select name='numeroJury' class='fondo'>";
            $sql="SELECT jury, count(*) nb from odb_repartition where annee=$annee group by jury order by jury";
            $result=odb_query($sql,__FILE__,__LINE__);
            while($row=mysql_fetch_array($result)) {
            	$jury=$row['jury'];
            	$nb=$row['nb'];
            	if($jury!='') $tJury[$jury]=$nb;
            }
            for($i=1;$i<=$jury;$i++) {
            	$nb=$tJury[$i];
            	if($nb>1) $s='s';else $s='';
            	if($nb=='') {
            		$nb_aff="aucun";
            		$nb=0;
            	}
            	else $nb_aff=$nb;
            	$inputSelectJury.="<option style='background-color:rgb(0,".min(round(128*$nb/300,0),255).",0);' value='$i'>Jury $i ($nb_aff candidat$s)</option>\n";
            }
            $jury++;
            $inputSelectJury.="<option selected style='background-color:#f00;' value='$jury'>Jury $jury (aucun candidat)</option>\n";
            $inputSelectJury.="</select>\n";
            $msg.="<tr><td colspan=2><A NAME='form_jury'></A>\n"
            	 . "Disposer $inputNbCanJury candidats dans le jury $inputSelectJury "
                . "<input type='submit' name='ajouterJury' value='OK' class='fondo' style='background-color:#4b4;' "
                . "onClick=\"nbCan=document.forms['form_jury'].nb.value;if(nbCan>300) return(confirm('Le nombre maximum recommande par jury est de 300 candidats\\n\\nVous souhaitez disposer '+nbCan+' candidats dans le jury '+document.forms['form_jury'].numeroJury.value+'.\\nEtes-vous sur(e) ?'));\"/>"
                . "<input type='submit' name='viderJury' value='Vider ce jury' class='fondo' style='background-color:#b44;' "
                . "onClick=\"return(confirm('Etes-vous sur(e) de vouloir vider le jury '+document.forms['form_jury'].numeroJury.value+' ?'));\"/>"
                . "</td></tr>\n"
                ;
				$msg.="<input type='hidden' name='action' value='etat_preparation_des_jurys'/>\n";
				$msg.="<input type='hidden' name='stats_departement' value='$stats_departement'/>\n";
				$msg.="<input type='hidden' name='stats_centre' value='$stats_centre'/>\n";
            $msg.="</form></table>\n";
            $msg=odb_table_matieres($index).$msg;
         break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_candidats_admissibles_par_serie':
         	odb_maj_decisions($annee,0,3,1);
         	odb_maj_decisions($annee,0,3,2);
         	odb_maj_decisions($annee,0,3,3);
         	$sql="SELECT jury1, jury2, jury3, jury4, deliberation FROM odb_ref_deliberation delib, odb_ref_operateur ope\n".
         	"WHERE delib.id=ope.id_deliberation ORDER BY id_deliberation, jury1";
         	$result=odb_query($sql,__FILE__,__LINE__);
         	while($row=mysql_fetch_array($result)) {
         		$deliberation=$row['deliberation'];
         		foreach(array('jury1','jury2','jury3','jury4') as $col)
         			if($row[$col]!=0) $tDelib[$deliberation][]=$row[$col];
         	}
         	foreach($tDelib as $deliberation=>$tJurys) {
         		$sql="SELECT ser.serie, delib1 decision, delib2, delib3, avg(moyenne) moyenne, count( * ) nb\n".
         		"FROM `odb_decisions` decis, odb_candidats can, odb_repartition rep, odb_ref_serie ser\n".
         		"WHERE decis.id_table = rep.id_table\n".
         		"AND can.annee=$annee and rep.annee=$annee and decis.annee=$annee ".
         		"AND can.id_table = rep.id_table AND ser.id = can.serie ".
         		"AND jury in (".implode(',',$tJurys).")\n".
         		"GROUP BY serie, delib1, delib2, delib3 with rollup";
         		//echo"<hr><b>$deliberation</b><pre>";print_r($tJurys);echo("<br/>$sql</pre>");
         		$result=odb_query($sql,__FILE__,__LINE__);
         		while($row=mysql_fetch_array($result)) {
         			foreach(array('serie','decision','delib2','delib3','moyenne','nb') as $col)
         				$$col=$row[$col];
	         		if($serie!=null && $delib2==null && $delib3==null) {
	         			if($decision!=null) {
	         				$tDecision[$deliberation][$serie][$decision]=$nb;
	         				//$tMoyenne[$deliberation][$serie][$decision]=round($moyenne,2);
	         			}
	         			else {
	         				$tDecision[$deliberation][$serie]['Total']=$nb;
	         				//if($decision!='Absent') $tMoyenne[$deliberation][$serie]['Present']+=round($moyenne/3,2);
	         			}
	         		}
	         		if($serie!=null && $decision=='Admissible' && $delib3==null) {
	         			if($delib2!=null) {
	         				$tDelib2[$deliberation][$serie][$delib2]=$nb;
	         				if($delib2!='Oral') $tDelib2[$deliberation][$serie]['Admis']+=$nb;
	         				//echo "$deliberation $serie $decision $delib2 $delib3 : Admis + $nb<br/>";
	         			}
	         			else {
	         				//echo "$deliberation $serie $nb<br/>";
	         				$tDelib2[$deliberation][$serie]['Total']=$nb;
	         			}
	         		}
         		}
         	}
         	/*echo"<hr>tDecision<pre>";print_r($tDecision);echo"</pre>\n";
         	echo"<hr>tDelib2<pre>";print_r($tDelib2);echo"</pre>\n";
         	echo"<hr>tMoyenne<pre>";print_r($tMoyenne);echo"</pre>\n";*/
         	$msg=odb_table_matieres(array_merge(array('!National'),array_keys($tDecision)));
         	foreach($tDecision as $deliberation=>$t1) {
         		$tTotal=array();
	         	$msg.="<A name='".getRewriteString($deliberation)."'></A><h2>$deliberation</h2>\n<TABLE class='spip'>\n<tr><th>S&eacute;rie</th><th>Pr&eacute;sents</th><th>Admissibles</th><th>Taux</th><th>Admis 1<sup>er</sup> groupe</th></tr>\n";
         		foreach($t1 as $serie=>$tDelib) {
         			$presents=(int)$tDelib['Total']-$tDelib['Absent'];
         			$admissibles=(int)$tDelib['Admissible'];
         			//$moyennePresents=$tMoyenne[$deliberation][$serie]['Present'];
         			//$moyenneAdmissibles=$tMoyenne[$deliberation][$serie]['Admissible'];
         			$taux=round($admissibles/$presents,4);
         			if(is_array($tDelib2[$deliberation][$serie])) {
         				$admis="<TABLE class='spip' width='90%'>\n";
         				foreach($tDelib2[$deliberation][$serie] as $delib2=>$nb2) {
         					if(!in_array($delib2,array('Total','Admis','Oral'))) {
         						$admis.="<tr><th>$delib2</th><td>$nb2</td></tr>\n";
         						$tTotal['admis']+=$nb2;
         					}
         				}
         				$taux2=round($tDelib2[$deliberation][$serie]['Admis']/$tDelib2[$deliberation][$serie]['Total']*100,2);
         				//$taux2=afficheTaux($taux2).(100*$taux2).'%';
         				$admis.="<tr><th>Total</th><td><b>".$tDelib2[$deliberation][$serie]['Admis']."</b>/".$tDelib2[$deliberation][$serie]['Total']." ($taux2%)</td></tr>\n</table>\n";
         			} else $admis='';
         			$msg.="<tr><th>$serie</th><td>$presents</td><td>$admissibles</td><td>".afficheTaux($taux).' '.($taux*100)."%</td><td>$admis</td></tr>\n";
         			$tNational[$serie]['presents']+=$presents;
         			$tNational[$serie]['admissibles']+=$admissibles;
         			$tNational[$serie]['admis']+=$tDelib2[$deliberation][$serie]['Admis'];
         			//$tNational[$serie]['moyennePresents']+=$moyennePresents;
         			//$tNational[$serie]['moyenneAdmissibles']+=$moyenneAdmissibles;
         			$tNational[$serie]['nbCentres']+=1;
         			$tTotal['presents']+=$presents;
         			$tTotal['admissibles']+=$admissibles;
	         	}
	         	$presents=$tTotal['presents'];
	         	$admissibles=$tTotal['admissibles'];
	         	$taux=round($admissibles/$presents,4);
	         	$msg.="<tr><th>Total</th><th>$presents</th><th>$admissibles</th><td>".afficheTaux($taux).' '.($taux*100)."%</td><td>Admis : <b>".$tTotal['admis']."</b>/$admissibles = ".round($tTotal['admis']/$admissibles*100,2)."%</td></tr>\n\n";
	         	$msg.="</TABLE>\n";
	         	//echo "$deliberation<pre>";print_r($t1);die('</pre>stop');
         	}
         	$msg.="<A name='national'></A><h2>National</h2>\n<TABLE class='spip'>\n<tr><th>S&eacute;rie</th><th>Pr&eacute;sents</th><th>Admissibles</th><th>Taux</th><th>Admis 1<sup>er</sup> groupe</th></tr>\n";
         	$tTotal=array();
         	foreach($tNational as $serie=>$tDelib) {
         		$presents=(int)$tDelib['presents'];
         		$admissibles=(int)$tDelib['admissibles'];
         		$admis=(int)$tDelib['admis'];
         		$taux=round($admissibles/$presents,4);
	         	//$moyennePresents=round($tDelib['moyennePresents']/$tDelib['nbCentres'],2);
	         	//$moyenneAdmissibles=round($tDelib['moyenneAdmissibles']/$tDelib['nbCentres'],2);
	         	$msg.="<tr><td>$serie</td><td>$presents</td><td>$admissibles</small></td><td>".afficheTaux($taux).' '.($taux*100)."%</td><td><b>$admis</b> (".round(100*$admis/$admissibles,2)."%)</td></tr>\n";
        			$tTotal['presents']+=$presents;
        			$tTotal['admissibles']+=$admissibles;
        			$tTotal['admis']+=$admis;
        		}
        		$presents=$tTotal['presents'];
         	$admissibles=$tTotal['admissibles'];
         	$admis=$tTotal['admis'];
         	$taux=round($admissibles/$presents,4);
         	$msg.="<tr><th>Total</th><th>$presents</th><th>$admissibles</th><td>".afficheTaux($taux).' '.($taux*100)."%</td><td><b>$admis</b> (".round(100*$admis/$admissibles,2)."%)</td></tr>\n\n";
       		$msg.="</TABLE>\n";      		
        	break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_classement_etablissements_par_serie':
         	$sql = "select dep . departement , vil.ville, eta . etablissement , ser.serie, delib1 decision , count( * ) nb \n"
		        . " from odb_ref_serie ser, odb_ref_departement dep , odb_ref_ville vil, odb_ref_etablissement eta , odb_candidats can , odb_decisions decis \n"
		        . " where can . id_table = decis . id_table and can . annee = $annee and decis . annee = $annee \n"
		        . " and can . etablissement = eta . id and can . departement = dep . id and can.serie=ser.id and eta.id_ville=vil.id \n"
		        . " group by departement , etablissement , serie, delib1 order by departement , etablissement , serie , delib1 ";
		      $result=odb_query($sql,__FILE__,__LINE__);
		      $cpt=0;
		      while($row=mysql_fetch_array($result)) {
		      	$cpt++;
		      	foreach(array('departement','ville','etablissement','serie','decision','nb') as $col) $$col=odb_propre($row[$col]);
		      	if(in_array($serie,array('A1','A2','B'))) $groupe='A1 - A2 - B';
		      	//elseif(in_array($serie,array('C','D'))) $groupe='C - D';
		      	elseif(in_array($serie,array('G2','G3'))) $groupe='G2 - G3';
		      	elseif(in_array($serie,array('E','F2','F3'))) $groupe='E - F2 - F3';
		      	else $groupe=$serie;
		      	$tClassement[$departement][$etablissement][$ville][$groupe][$decision]=$nb;
		      }
		      
		      foreach($tClassement as $departement=>$t1) {
		      	foreach($t1 as $etablissement=>$t2) {
		      		foreach($t2 as $ville=>$t3) {
			      		foreach($t3 as $groupe=>$t4) {
			      			$presents=0;
			      			$inscrits=0;
				      		foreach($t4 as $decision=>$nb) {
				      			if($decision!='Absent') $presents+=$nb;
				      			$inscrits+=$nb;
				      		}
				      		$admissibles=$t4['Admissible'];
				      		if(substr_count($etablissement,'CL/')==0) {
				      			$taux=$presents>0?round(100*$admissibles/$presents,2):-1;
				      			$cle=str_pad($taux*100,5,'0',STR_PAD_LEFT).'-'.str_pad($presents,3,'0',STR_PAD_LEFT)."-$etablissement";
				      			$tClassementNat[$groupe][$cle]['Departement']=$departement;
				      			$tClassementNat[$groupe][$cle]['Ville']=$ville;
				      			$tClassementNat[$groupe][$cle]['Etablissement']=$etablissement;
				      			$tClassementNat[$groupe][$cle]['Serie']=$groupe;
				      			$tClassementNat[$groupe][$cle]['Presents']+=$presents;
				      			$tClassementNat[$groupe][$cle]['Admissibles']+=(int)$admissibles;
				      			$tClassementNat[$groupe][$cle]['Inscrits']+=$inscrits;
				      			$tClassementNat[$groupe][$cle]['Taux']=$taux;
				      		}
			      		}
		      		}
		      	}
		      }
		      ksort($tClassementNat);
		      $colonnes=array('Departement','Ville','Etablissement','Inscrits','Presents','Admissibles','Taux');
		      $msg="<h1>Classement $annee des &eacute;tablissements<sup>*</sup></h1>\n";
		      $msg.="<sup>*</sup> &Eacute;tablissements ayant pr&eacute;sent&eacute; au moins 10 candidats par groupe de s&eacute;rie";
		      foreach($tClassementNat as $groupe=>$tClassement) {
			      $cpt=0;
					krsort($tClassement);
			      $msg.="<hr size=1/><h2>Classement $annee - s&eacute;rie $groupe</h2>\n<table class='spip'>\n<tr>\n\t<th>#</th>\n";
			      foreach($colonnes as $col) $msg.="\t<th>$col</th>\n";
			      $msg.="</tr>\n";
			      $pdf=array();
			      foreach($tClassement as $cle=>$tEta) {
			      	if ((in_array($groupe,array('E - F2 - F3','F1','F4')) || $tEta['Inscrits']>=10)) {
				      	$cpt++;
				      	$taux=$tEta['Taux'];
				      	if($taux_old!=$taux) $cpt_aff=$cpt;
				      	$pdf[$cle]['cpt']=$cpt_aff;
				      	$tEta['Taux']=$taux==-1?"<i>N/A</i>":"$taux%";
				      	$msg.="<tr>\n\t<th>$cpt_aff</th>\n";
				      	foreach($colonnes as $col) {
				      		$$col=$tEta[$col];
				      		$msg.="\t<td>".$$col."</td>\n";
				      		$pdf[$cle][$col]=html_entity_decode(utf8_decode($$col));
				      	}
				      	$taux_old=$taux;
				      	$msg.="</tr>\n";
			      	}
			      }
			      $msg.="</table>\n";
		         $nom_pdf=getRewriteString("Classement etablissements $annee - $groupe");
	         	$_SESSION['data'][$nom_pdf]=$pdf;
	         	//echo"<pre>";print_r($pdf['National']);die('</pre>');
	         	$_SESSION['pied'][$nom_pdf]=html_entity_decode("Classement limit&eacute; aux &eacute;tablissements ayant pr&eacute;sent&eacute; au moins 10 candidats en s&eacute;rie $groupe au bac $annee");
	         	$_SESSION['titre'][$nom_pdf]=html_entity_decode("Classement des &eacute;tablissements $annee - s&eacute;rie $groupe");
	         	$_SESSION['cols'][$nom_pdf]['cpt']=html_entity_decode('#');
	         	foreach($colonnes as $col) $_SESSION['cols'][$nom_pdf][$col]=ucfirst($col);
	         	//$_SESSION['cols'][$nom_pdf]['id_table']=html_entity_decode('Num&eacute;ro de table');
	         	$_SESSION['format'][$nom_pdf]=array('taille'=>'A4', 'orientation'=>'portrait');
	         	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
	         	$tmp2=" G&eacute;n&eacute;rer le classement des <b>&eacute;tablissements $annee - $groupe</b> en PDF</A>";
	         	$msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer le classement des &eacute;tablissements $annee - $groupe en PDF").$tmp2;
	         	$lien_pdf[$nom_pdf]=$tmp1.$tmp2;        	
			     }
         	break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_classement_candidats_f':
         	$andSexe="and sex.sexe='F'";
         	$titre2=' (filles)';
         case 'etat_classement_candidats_h':
         	if($andSexe=='') {
         		$andSexe="and sex.sexe='M'";
         		$titre2=' (gar&ccedil;ons)';
         	}
         case 'etat_classement_candidats':
         	if($titre2=='') $titre2='(tous sexes)';
         	$msg="<h1>Candidats $annee admis au 1er groupe $titre2</h1>\n";
         	odb_maj_decisions($annee,0,3,1);
         	odb_maj_decisions($annee,0,3,2);
         	odb_maj_decisions($annee,0,3,3);
         	$sql =" SELECT dep . departement , decis.id_table, concat_ws(' ', if(sex . sexe='M','M.','Mlle') , pre . prefixe , concat('<b>',nom,'</b>') , prenoms) candidat , ser . serie , eta . etablissement , moyenne , delib2 mention \n"
		        . " from odb_decisions decis , odb_ref_departement dep , odb_ref_sexe sex , odb_ref_etablissement eta , odb_ref_serie ser , odb_candidats can \n"
		        . " left join odb_ref_prefixe pre on can . prefixe = pre . id \n"
		        . " where can . id_table = decis . id_table and decis.delib1='Admissible' and decis.delib2!='Oral' and decis.delib2!='Reserve'\n"
		        . " and can . sexe = sex . id and can . etablissement = eta . id and can . serie = ser . id and can . departement = dep . id \n"
		        . " and can . annee = $annee and decis . annee = $annee $andSexe \n"
		        . " order by moyenne desc";
		        //echo "<pre>".htmlspecialchars($sql)."</pre>\n";
         	$result=odb_query($sql,__FILE__,__LINE__);
         	$colonnes=array('departement','id_table','candidat','serie','etablissement','moyenne','mention');
         	$cpt=0;
         	while($row=mysql_fetch_array($result)) {
         		$moyenne=$row['moyenne'];
         		$departement=$row['departement'];
         		$cpt++;
         		$cptD[$departement]++;
         		$row['id_table']=getIdTableHumain($row['id_table']);
         		foreach($colonnes as $col) {
         			$$col=$row[$col];
         			$tClassement[$cpt][$col]=$$col;
         			if($cpt<=2000) {
         				$pdf['National'][$cpt][$col]=html_entity_decode(utf8_decode($$col));
         				$pdf['National'][$cpt]['cpt']=$cpt;
         			}
         			$tClassementDep[$departement][$cpt][$col]=$$col;
         			$pdf[$departement][$cpt][$col]=html_entity_decode(utf8_decode($$col));
         			$pdf[$departement][$cpt]['cpt']=$cptD[$departement];
         		}
         	}
         	$msg.=odb_table_matieres(array_merge(array('-National'),array_keys($tClassementDep)));
         	$msg.="<A name='national'></a><h2>National</h2><hr size=1/><table class='spip'>\n<tr>\n\t<th>#</th>\n";
         	$cpt=1;
         	foreach($colonnes as $col) $msg.="\t<th>".ucfirst($col)."</th>\n";
         	$msg.="</tr>\n";
         	foreach($tClassement as $tNational) {
         		if($cpt<=100) {
         			$msg.="<tr class='tr_liste'>\n\t<th>".($cpt++)."</th>\n";
	         		foreach($tNational as $cle=>$valeur) {
	         			$msg.="\t<td>".stripslashes($valeur)."</td>\n";
	         		}
	         		$msg.="</tr>\n";
         		}
         	}
         	$msg.="</table>\n";
	         $nom_pdf=getRewriteString("Classement national $annee $titre2");
         	$_SESSION['data'][$nom_pdf]=$pdf['National'];
         	//echo"<pre>";print_r($pdf['National']);die('</pre>');
         	//$_SESSION['pied'][$nom_pdf]="Classement $departement $annee $titre2";
         	$_SESSION['titre'][$nom_pdf]=html_entity_decode("Classement national $annee $titre2");
         	$_SESSION['cols'][$nom_pdf]['cpt']=html_entity_decode('#');
         	foreach($colonnes as $col) $_SESSION['cols'][$nom_pdf][$col]=ucfirst($col);
         	$_SESSION['cols'][$nom_pdf]['id_table']=html_entity_decode('Num&eacute;ro de table');
	         //$_SESSION['options'][$nom_pdf]=$PDF_A4_PORTRAIT;
	         //$_SESSION['format'][$nom_pdf]=array('taille'=>'A4', 'orientation'=>'portrait');
         	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
         	$tmp2=" G&eacute;n&eacute;rer le classement national $annee $titre2 en PDF</A>";
         	$msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer le <b>classement national $annee $titre2</b> en PDF").$tmp2;
         	$lien_pdf[$nom_pdf]=$tmp1.$tmp2;        	
         	
         	ksort($tClassementDep);
         	foreach($tClassementDep as $departement=>$tCD) {
         		$cpt=1;
         		$msg.="<hr size=1/><A name='".getRewriteString($departement)."'></a><h2>$departement</h2><hr size=1/><table class='spip'>\n<tr>\n\t<th>#</th>\n";
	         	foreach($colonnes as $col) $msg.="\t<th>".ucfirst($col)."</th>\n";
	         	$msg.="</tr>\n";
	         	foreach($tCD as $k=>$tDep) {
	         		if($cpt<=100) {
		         		$msg.="<tr class='tr_liste'>\n\t<th>".($cpt++)."</th>\n";
		         		foreach($tDep as $cle=>$valeur) {
		         			$msg.="\t<td>".stripslashes($valeur)."</td>\n";
		         		}
		         		$msg.="</tr>\n";
	         		}
	         	}
	         	//$msg.="<tr><th colspan="
	         	$msg.="</table>\n";
	         	$nom_pdf=getRewriteString("Classement $departement $annee $titre2");
	         	$_SESSION['data'][$nom_pdf]=$pdf[$departement];
	         	//$_SESSION['pied'][$nom_pdf]="Classement $departement $annee $titre2";
	         	$_SESSION['titre'][$nom_pdf]=html_entity_decode("Classement $departement $annee $titre2");
	         	$_SESSION['cols'][$nom_pdf]['cpt']=html_entity_decode('#');
	         	foreach($colonnes as $col) $_SESSION['cols'][$nom_pdf][$col]=ucfirst($col);
	         	$_SESSION['cols'][$nom_pdf]['id_table']=html_entity_decode('Num&eacute;ro de table');
	         	//$_SESSION['options'][$nom_pdf]=$PDF_A4_PORTRAIT;
	         	//$_SESSION['format'][$nom_pdf]=array('taille'=>'A4', 'orientation'=>'portrait');
	         	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
	         	$tmp2=" G&eacute;n&eacute;rer le classement $departement $annee $titre2 en PDF</A>";
	         	$msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer le classement $departement $annee $titre2 en PDF").$tmp2;
	         	$lien_pdf[$nom_pdf]=$tmp1.$tmp2;
         	}
        	break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_classement_grandes_ecoles':
         	$ecole=$_REQUEST['ecole'];
         	odb_maj_decisions($annee,0,3,1);
         	odb_maj_decisions($annee,0,3,2);
         	odb_maj_decisions($annee,0,3,3);
         	$sql="SELECT commentaire, id_matiere1, id_matiere2, id_matiere3, id_matiere4, serie, coeff1, coeff2, coeff3, coeff4, coeff_bac from odb_ref_ecole ecole, odb_ref_serie ser where ecole='$ecole' and ser.id=ecole.id_serie ORDER BY serie";
         	$result=odb_query($sql,__FILE__,__LINE__);
	         $colonnes=array('commentaire','id_matiere1','id_matiere2','id_matiere3','id_matiere4',"serie","coeff1",'coeff2','coeff3','coeff4','coeff_bac');
				while($row=mysql_fetch_array($result)) {
					foreach($colonnes as $col) $$col=$row[$col];
					//CPRO : le bac aussi a son coeff
					$tMatieres[$serie][0]['matiere']="Moyenne du bac";
					$tMatieres[$serie][0]['coeff']=$coeff_bac;
					for($i=1;$i<=4;$i++) {
						if($commentaire!='') $sCommentaire=$commentaire;
						$id_matiere=(int)${"id_matiere$i"};
						if($id_matiere!=0) {
							$tMatieres[$serie][$i]['matiere']=$tab_referentiel['matiere'][$id_matiere];
							$tMatieres[$serie][$i]['coeff']=${"coeff$i"};
						}
					}
				}
         	$msg="<h1>Classement $ecole $annee</h1>\n";
				for($i=1;$i<=4;$i++) {
         		$sql ="select can.id_table, sex.sexe, pre.prefixe, nom, prenoms, note*ecole.coeff$i moyenne$i, ecole.coeff$i coeff$i, coeff_bac, ser.serie, decis.moyenne moyenne_bac, decis.delib2 mention\n".
	         		"from odb_ref_serie ser, odb_ref_ecole ecole, odb_ref_sexe sex, odb_notes notes, odb_decisions decis, odb_candidats can\n".
	         		"left join odb_ref_prefixe pre on can.prefixe=pre.id\n".
	         		"where notes.id_table=can.id_table and notes.id_table=decis.id_table and notes.annee=$annee and can.annee=$annee and decis.annee=$annee\n".
	         		"and ser.id=ecole.id_serie and can.serie=ser.id and notes.id_matiere=ecole.id_matiere$i and notes.type='Ecrit' \n".
	         		"and ecole='$ecole' and decis.delib1='Admissible' and decis.delib2!='Oral' and decis.delib2!='Reserve'\n".
	         		"and sex.id=can.sexe\n".
	         		"order by id_table";
			      //   echo "<pre>".htmlspecialchars($sql)."</pre>\n";
	         	$result=odb_query($sql,__FILE__,__LINE__);
	         	$colonnes=array('id_table','sexe','prefixe','nom','prenoms',"moyenne$i","coeff$i",'serie','moyenne_bac','mention');
					while($row=mysql_fetch_array($result)) {
						foreach($colonnes as $col) $$col=$row[$col];
						foreach($colonnes as $col) $tCan[$id_table][$col]=$row[$col];
						if($sexe=='F') $civilite='Mlle';else $civilite='M.';
						$tCan[$id_table]['candidat']="$civilite $prefixe <b>$nom</b> $prenoms";
						$tCan[$id_table]['total']+=$row["moyenne$i"];
						$tCan[$id_table]['coeff']+=$row["coeff$i"];
						$tCan[$id_table]['moyenne_bac']=$row["moyenne_bac"];
						$tCan[$id_table]['coeff_bac']=$row["coeff_bac"];
					}
         	}
         	foreach($tCan as $id_table=>$t1) {
         		$t1['moyenne']=round($t1['total']/$t1['coeff'],2);
         		$t1['moyenne']=round((($t1['moyenne']*(1-$t1['coeff_bac']))+($t1['moyenne_bac']*$t1['coeff_bac'])),2);
         		$cle=str_pad(100*$t1['moyenne'],4,'0',STR_PAD_LEFT)."_$id_table";
         		if($t1['moyenne']>=8) $tClassement[$cle]=$t1;
         	}
         	krsort($tClassement);
         	$msg.=$sCommentaire;
         	$sPost="<u>Modalit&eacute;s de calcul des notes pour $ecole :</u>\n\n$sCommentaire\n";
         	foreach($tMatieres as $serie=>$t1) {
         		$msg.="<h2>S&eacute;rie $serie</h2>\n<table class='spip'>\n<tr><th>Mati&egrave;re</th><th>Coeff</th></tr>\n";
         		$sPost.="\n<b>S&eacute;rie $serie</b>\n";
         		foreach($t1 as $t2) {
         			$msg.="<tr><td>".$t2['matiere']."</td><td>".$t2['coeff']."</td></tr>\n";
         			$sPost.="- ".$t2['matiere']." coeff ".$t2['coeff']."\n";
         		}
         		$msg.="</table>\n";
         	}
         	//echo"<pre>";print_r($tClassement);echo"</pre>";
         	$colonnes2=array('id_table','candidat','moyenne','serie','moyenne_bac','mention');
         	$msg.="<table class='spip'>\n<tr>\n\t<th>#</th>\n";
         	foreach($colonnes2 as $col) $msg.="\t<th>".str_replace('_',' ',ucfirst($col))."</th>\n";
         	$msg.="</tr>\n";
         	$cpt=0;
         	foreach($tClassement as $cle=>$t1) {
         		$cpt++;
         		$moyenne=$t1['moyenne'];
         		if($moyenne!=$moyenne_old) $cpt_aff=$cpt;
         		$pdf[$cle]['cpt']=$cpt_aff;
         		$msg.="<tr>\n\t<th>$cpt_aff</th>\n";
         		foreach($colonnes2 as $col) {
         			$msg.="\t<td>".stripslashes($t1[$col])."</td>\n";
         			$pdf[$cle][$col]=html_entity_decode(utf8_decode(stripcslashes($t1[$col])));
         		}
         		$msg.="</tr>\n";
         		$moyenne_old=$moyenne;
         	}
         	$msg.="</table>\n";
	       $nom_pdf=getRewriteString("Classement $ecole $annee");
         	$_SESSION['data'][$nom_pdf]=$pdf;
         	//echo"<pre>";print_r($pdf['National']);die('</pre>');
         	//$_SESSION['pied'][$nom_pdf]="Classement $departement $annee $titre2";
         	$_SESSION['titre'][$nom_pdf]=html_entity_decode("Classement $ecole $annee");
         	$_SESSION['cols'][$nom_pdf]['cpt']='#';
         	foreach($colonnes2 as $col) $_SESSION['cols'][$nom_pdf][$col]=str_replace('_',' ',ucfirst($col));
         	$_SESSION['options'][$nom_pdf]=$PDF_A4_PORTRAIT;
         	$_SESSION['post'][$nom_pdf]=html_entity_decode(utf8_decode($sPost));
         	$_SESSION['format'][$nom_pdf]=array('taille'=>'A4', 'orientation'=>'portrait');
         	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
         	$tmp2=" G&eacute;n&eacute;rer le <b>classement $ecole $annee</b> en PDF</A>";
         	$msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer le classement $ecole $annee en PDF").$tmp2;
         	$lien_pdf[$nom_pdf]=$tmp1.$tmp2;        	
         	break;
         ///////////////////////////////////////////////////////////////////////////////////////////
         ///////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_impression_attestations':
         	/*
         	odb_maj_decisions($annee,0,3,1);
         	odb_maj_decisions($annee,0,3,2);
         	odb_maj_decisions($annee,0,3,3);
         	*/
         	$sql="SELECT count(*) nb from odb_decisions where delib2!='Oral' and delib2!='Reserve' and delib1='Admissible' and annee=$annee";
         	$result=odb_query($sql,__FILE__,__LINE__);
         	$row=mysql_fetch_array($result);
         	$nbGroupe1=$row['nb'];
         	$sql="SELECT count(*) nb from odb_decisions where delib3='Passable' and delib1='Admissible' and annee=$annee";
         	//echo $sql;
         	$result=odb_query($sql,__FILE__,__LINE__);
         	$row=mysql_fetch_array($result);
         	$nbGroupe2=$row['nb'];
         	$msg="<h1>Impression des attestations $annee</h1>\n".
         	"<table class='spip'>";
         	$msg.="<script type='text/javascript'>function bon_format_date(chaine) {\n".
         	'var exp=new RegExp("^[0-9]{1,2}\/[01]?[0-9]\/[0-9]{4}$","g");return exp.test(chaine);}</script>';
         	$msg.="<h2>Groupe 1 : $nbGroupe1 attestations</h2>\n";
         	"<table class='spip'>";
         	$aujourdhui=date('xx/m/Y');
         	$verif="onSubmit=\"if(!bon_format_date(document.forms['form_groupe1'].date_delib.value))\n".
         	" {alert('Veuillez saisir une date de deliberation correcte');return false;}\n".
         	"if(!bon_format_date(document.forms['form_groupe1'].date_attestation.value))\n".
         	" {alert('Veuillez saisir une date d\'attestation correcte');return false;}\n".
         	"return true;\"";
         	$msg.="<tr><td>".vignette('pdf',"Non admissibles jury $jury")."</td>\n".
         	"<form name='form_groupe1' action='../plugins/odb/odb_commun/inc-pdf-attestations.php' $verif method='POST'>\n".
         	"<input type='hidden' name='jury' value='$jury'/>\n".
         	"<input type='hidden' name='groupe' value='1'/>\n".
         	"<input type='hidden' name='limit' value='0'/>\n".
         	"<input type='hidden' name='annee' value='$annee'/>\n".
         	"<input type='hidden' name='exec' value='odb_stats_siou'/>\n".
         	"<td><small><label for='date_delib'>Date de la d&eacute;lib&eacute;ration</label></small><br/><input name='date_delib' class='fondo' size=10 value='$aujourdhui'\"/><br/>\n".
         	"<small><label for='date_attestation'>Date de l'attestation</label></small><br/><input name='date_attestation' class='fondo' size=10 value='$aujourdhui'\"/></td>\n</tr>\n<tr>\n\t<td colspan=2>";
         	$cpt=0;
         	$cptMax=floor($nbGroupe1/2000)+1;
         	for($i=0;$i<=$nbGroupe1;$i+=2000) {
         		$cpt++;
         		$msg.="<input type='submit' onClick=\"document.forms['form_groupe1'].limit.value='$i';\" value='Attestations\n1er groupe\n$cpt/$cptMax' class='fondo' />\n";
         	}
         	$msg.="</td>\n</tr>\n";
         	$msg.="</form></tr>\n";
				$msg.="</table>\n";         	
         	$msg.="<h2>Groupe 2 : $nbGroupe2 attestations</h2>\n".
         	"<table class='spip'>";
         	$verif="onSubmit=\"if(!bon_format_date(document.forms['form_groupe2'].date_delib.value))\n".
         	" {alert('Veuillez saisir une date de deliberation correcte');return false;}\n".
         	"if(!bon_format_date(document.forms['form_groupe2'].date_attestation.value))\n".
         	" {alert('Veuillez saisir une date d\'attestation correcte');return false;}\n".
         	"return true;\"";
         	$msg.="<tr><td>".vignette('pdf',"Non admissibles jury $jury")."</td>\n".
         	"<form name='form_groupe2' action='../plugins/odb/odb_commun/inc-pdf-attestations.php' $verif method='POST'>\n".
         	"<input type='hidden' name='limit' value='0'/>\n".
         	"<input type='hidden' name='jury' value='$jury'/>\n".
         	"<input type='hidden' name='groupe' value='2'/>\n".
         	"<input type='hidden' name='annee' value='$annee'/>\n".
         	"<input type='hidden' name='exec' value='odb_stats_siou'/>\n".
         	"<td><small><label for='date_delib'>Date de la d&eacute;lib&eacute;ration</label></small><br/><input name='date_delib' class='fondo' size=10 value='$aujourdhui'\"/><br/>\n".
         	"<small><label for='date_attestation'>Date de l'attestation</label></small><br/><input name='date_attestation' class='fondo' size=10 value='$aujourdhui'\"/></td>\n</tr>\n<tr>\n\t<td colspan=2>";
         	$cpt=0;
         	$cptMax=floor($nbGroupe2/2000)+1;
         	for($i=0;$i<=$nbGroupe2;$i+=2000) {
         		$cpt++;
         		$msg.="<input type='submit' onClick=\"document.forms['form_groupe2'].limit.value='$i';\" value='Attestations\n2e groupe\n$cpt/$cptMax' class='fondo' />\n";
         	}
         	$msg.="</table>\n";
         	break;
         	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
         case 'etat_statistiques_genre':
         	$msg="<h1>Statistiques sur le genre - bac $annee</h1>\n";
         	$sql =" SELECT dep.departement, if( delib1 != 'Admissible', delib1, if( delib2 != 'Oral' AND delib2 != 'Reserve', delib2, delib3 ) ) resultat, sex.sexe, count(*) nombre \n".
         	" FROM odb_ref_departement dep, odb_ref_sexe sex, odb_candidats can, odb_decisions decis\n".
         	" WHERE can.id_table=decis.id_table and can.annee=$annee and decis.annee=$annee and can.sexe=sex.id and can.departement=dep.id\n".
         	" GROUP BY dep.departement, resultat, sexe\n".
         	" ORDER BY dep.departement, resultat, sexe"
         	;
		      //echo "<pre>".htmlspecialchars($sql)."</pre>\n";
         	$result=odb_query($sql,__FILE__,__LINE__);
         	$colonnes=array('departement','resultat','sexe','nombre');
         	$colonnes2=array('resultat','sexe','nombre','total','taux');
         	$cpt=0;
         	while($row=mysql_fetch_array($result)) {
         		$sexe=$row['sexe'];
         		$departement=$row['departement'];
         		$cpt++;
         		$cptD[$departement]++;
         		foreach($colonnes as $col) {
         			$$col=$row[$col];
         		}
         		$tStats[$departement][$resultat][$sexe]=$nombre;
         	}
         	ksort($tStats);
         	//echo"tStats<pre>";print_r($tStats);echo"</pre><hr>";
         	foreach($tStats as $departement=>$t1) {
         		$cptD[$departement]=0;
         		$tNb=array();
         		$tNbSexe=array();
         		$tNbPrecis=array();
         		$tNbSexePrecis=array();
         		foreach($t1 as $resultat=>$t2) {
         			foreach($t2 as $sexe=>$nb) {
         				$tNbSexe['inscrit'][$sexe]+=$nb;
         				$tNb['inscrit']+=$nb;
         				switch($resultat) {
         					case 'Absent':
         						$tNbSexePrecis['absent'][$sexe]+=$nb;
         						$tNbPrecis['absent']+=$nb;
         						break;
         					case 'Ajourne':
         					case 'Refuse':
         						$tNbSexe['echec'][$sexe]+=$nb;
         						$tNb['echec']+=$nb;
         						break;
         					case 'Reserve':
         						$tNbSexe['reserve'][$sexe]+=$nb;
         						$tNb['reserve']+=$nb;
         						break;
         					default:
         						$tNbSexe['admis'][$sexe]+=$nb;
         						$tNb['admis']+=$nb;
         						break;
         				}
         				$tNbSexe['present'][$sexe]=$tNbSexe['inscrit'][$sexe]-$tNbSexePrecis['absent'][$sexe];
         				$tNb['present']=$tNb['inscrit']-$tNbPrecis['absent'];
         				$tNbSexePrecis[$resultat][$sexe]+=$nb;
         				$tNbPrecis[$resultat]+=$nb;
         			}
         		}
         		
         		$tTitres=array('inscrit','present','echec','admis','reserve');
         		$tTous=array('inscrit','present','absent','echec','Ajourne','Refuse','admis','Passable','Abien','Bien','TBien','reserve');
         		foreach($tTous as $resultat) {
         			if(in_array($resultat,$tTitres)) $isTitre=true;else $isTitre=false;
	         		foreach(array('F','M') as $sexe) {
	         			$cptD[$departement]++;
	         			if($isTitre) {
	         				if($tNb[$resultat]>0) $tHtml[$departement][$cptD[$departement]]=array(
			         			//'Departement'=>$departement,
			         			'Resultat'=>"<b>".ucfirst($resultat)."</b>",
			         			'Sexe'=>$sexe,
			         			'Nombre'=>(int)$tNbSexe[$resultat][$sexe],
			         			'Total'=>$tNb[$resultat],
			         			'Taux'=>round(100*$tNbSexe[$resultat][$sexe]/$tNb[$resultat],2).'%',
			         		);
	         			} else {
	         				if($tNbPrecis[$resultat]>0) $tHtml[$departement][$cptD[$departement]]=array(
			         			//'Departement'=>$departement,
			         			'Resultat'=>ucfirst($resultat),
			         			'Sexe'=>$sexe,
			         			'Nombre'=>(int)$tNbSexePrecis[$resultat][$sexe],
			         			'Total'=>$tNbPrecis[$resultat],
			         			'Taux'=>round(100*$tNbSexePrecis[$resultat][$sexe]/$tNbPrecis[$resultat],2).'%',
			         		);
	         				
	         			}
	         		}
         		}
         		foreach($tHtml[$departement] as $cpt=>$t) {
         			foreach($t as $k=>$v) {
         				$tPdf[$departement][$cpt][$k]=html_entity_decode(utf8_decode($v));
         				if($k=='Nombre' || $k=='Total') $tHtmlNat[$cpt][$k]+=$v;
         				elseif($k=='Departement') $tHtmlNat[$cpt][$k]='National';
         				else $tHtmlNat[$cpt][$k]=$v;
         			}
         			$tHtmlNat[$cpt]['Taux']=round(100*$tHtmlNat[$cpt]['Nombre']/$tHtmlNat[$cpt]['Total'],2).'%';
         		}         		
         	}
         	foreach($tHtmlNat as $cpt=>$t) {
         		foreach($t as $k=>$v) {
         			$tPdfNat[$cpt][$k]=html_entity_decode(utf8_decode($v));
         		}
         	}
         	//echo "tHtml<pre>";print_r($tHtml);echo"</pre><hr>tHtmlNat<pre>";print_r($tHtmlNat);echo"</pre>";
				//echo "tPdf<pre>";print_r($tPdfNat);echo"</pre>";
         	$msg.=odb_table_matieres(array_merge(array('-National'),array_keys($tStats)));
         	$msg.="<A name='national'></a><h2>National</h2><hr size=1/><table class='spip'>\n<tr>\n";
         	$cpt=1;
         	foreach($colonnes2 as $col) $msg.="\t<th>".ucfirst($col)."</th>\n";
         	$msg.="</tr>\n";
         	foreach($tHtmlNat as $tNational) {
         		if($tNational['Sexe']=='F') $couleur='#EE3399';
         		else $couleur='#3366CC';
         		foreach($tNational as $cle=>$valeur) {
         			$msg.="\t<td style='color:$couleur;'>".stripslashes($valeur)."</td>\n";
         		}
         		$msg.="</tr>\n";
        		}
        	
         	$msg.="</table>\n";
	         $nom_pdf=getRewriteString("Stats genre national $annee");
         	$_SESSION['data'][$nom_pdf]=$tPdfNat;
         	//echo"<pre>";print_r($pdf['National']);die('</pre>');
         	//$_SESSION['pied'][$nom_pdf]="Classement $departement $annee $titre2";
         	$_SESSION['titre'][$nom_pdf]=html_entity_decode("Statistiques genre national $annee");
         	foreach($colonnes2 as $col) $_SESSION['cols'][$nom_pdf][ucfirst($col)]=ucfirst($col);
         	$_SESSION['options'][$nom_pdf]=$PDF_A4_PORTRAIT;
         	$_SESSION['format'][$nom_pdf]=array('taille'=>'A4', 'orientation'=>'portrait');
         	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
         	$tmp2=" G&eacute;n&eacute;rer les <b>statistiques genre national $annee</b> en PDF</A>";
         	$msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer les statistiques genre national $annee en PDF").$tmp2;
         	$lien_pdf[$nom_pdf]=$tmp1.$tmp2;        	
         	
         	foreach($tHtml as $departement=>$t1) {
         		$msg.="<A name='".getRewriteString($departement)."'></a><h2>$departement</h2><hr size=1/><table class='spip'>\n<tr>\n";
	         	$cpt=1;
	         	foreach($colonnes2 as $col) $msg.="\t<th>".ucfirst($col)."</th>\n";
	         	$msg.="</tr>\n";
	         	foreach($t1 as $tDep) {
	         		if($tDep['Sexe']=='F') $couleur='#EE3399';
	         		else $couleur='#3366CC';
	         		foreach($tDep as $cle=>$valeur) {
	         			$msg.="\t<td style='color:$couleur;'>".stripslashes($valeur)."</td>\n";
	         		}
	         		$msg.="</tr>\n";
	        		}
	        	
	         	$msg.="</table>\n";
		         $nom_pdf=getRewriteString("Stats genre $departement $annee");
	         	$_SESSION['data'][$nom_pdf]=$tPdf[$departement];
	         	//echo"<pre>";print_r($pdf['National']);die('</pre>');
	         	//$_SESSION['pied'][$nom_pdf]="Classement $departement $annee $titre2";
	         	$_SESSION['titre'][$nom_pdf]=html_entity_decode("Statistiques genre $departement $annee");
	         	foreach($colonnes2 as $col) $_SESSION['cols'][$nom_pdf][ucfirst($col)]=ucfirst($col);
	         	$_SESSION['options'][$nom_pdf]=$PDF_A4_PORTRAIT;
	         	$_SESSION['format'][$nom_pdf]=array('taille'=>'A4', 'orientation'=>'portrait');
	         	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
	         	$tmp2=" G&eacute;n&eacute;rer les <b>statistiques genre $departement $annee</b> en PDF</A>";
	         	$msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer les statistiques genre $departement $annee en PDF").$tmp2;
	         	$lien_pdf[$nom_pdf]=$tmp1.$tmp2;
         	}
         	break;
         	/////////////////////////////////////////////////////////////////////////
         	case 'etat_suivi_attestations':
         		$msg="<h1>Suivi des attestations $annee</h1>\n";
         		$sql="SELECT rep.id_table, sex.sexe, pre.prefixe, nom, prenoms, delib1, delib2, delib3, id_retrait, jury, ser.serie\n".
         		" from odb_repartition rep, odb_ref_sexe sex, odb_decisions decis, odb_ref_serie ser, odb_candidats can\n".
         		" left join odb_ref_prefixe pre on pre.id=can.prefixe\n".
         		" where can.annee=$annee and decis.annee=$annee and rep.annee=$annee\n".
         		" and can.id_table=rep.id_table and can.id_table=decis.id_table and ser.id=can.serie and sex.id=can.sexe\n".
         		" order by jury, serie, nom, prenoms";
         		//echo "<pre>$sql</pre>";
         		$result=odb_query($sql,__FILE__,__LINE__);
         		$cpt=0;
         		$colonnes=array('jury','serie','id_table', 'sexe', 'prefixe', 'nom', 'prenoms', 'delib1', 'delib2', 'delib3', 'id_retrait');
         		$colonnes2=array('jury','serie','id_table', 'candidat', 'delib', 'id_retrait','id_piece','emargement');
         		$colonnes_aff=array('jury','serie','id_table', 'candidat', 'delib', 'id_retrait');
         		$pdf_titres=array('jury'=>'Jury','serie'=>'S&eacute;rie','id_table'=>'N&deg; table','candidat'=>'Candidat','delib'=>'Mention','id_retrait'=>'N&deg; retrait','id_piece'=>'N&deg; pi&egrave;ce','emargement'=>'&Eacute;margement');
         		foreach($pdf_titres as $k=>$v) $pdf_titres_utf[$k]=html_entity_decode(utf8_decode($v));
         		while($row=mysql_fetch_array($result)) {
         			foreach($colonnes as $col) $$col=$row[$col];
         			$id_table=getIdTableHumain($id_table);
         			if($sexe=='M') $candidat="M. ";
         			else $candidat="Mlle ";
         			$candidat.=stripslashes("$prefixe <b>$nom</b> $prenoms");
         			if($delib1=='Admissible') {
	         			if($delib2!='Oral') $delib=$delib2;
	         			else $delib="$delib3 2&egrave; groupe";
         			} else $delib='';
         			if($delib!='') {
         				$cpt++;
	         			if($id_retrait==0) {
	         				$sql="UPDATE odb_repartition set id_retrait=$cpt where id_table='$id_table' and annee=$annee";
	         				odb_query($sql,__FILE__,__LINE__);
	         				$id_retrait=$cpt;
	         			}
	         			foreach($colonnes2 as $col) {
	         				$tAtt[$jury][$cpt][$col]=$$col;
	         				$tPdf[$jury][$cpt][$col]=html_entity_decode(utf8_decode($$col));
	         			}
         			}
         		}
         		//$cpt_jury=0;
         		foreach($tAtt as $jury=>$t1) {
         			//$cpt_jury++;
	         		//if($cpt_jury<=50) {
	         			$msg.="<h2>Jury $jury</h2>\n";
	         			$cpt=0;
		         		$msg.="<table class='spip'>\n<tr>\n";
		         		foreach($pdf_titres as $k=>$col) if(in_array($k,$colonnes_aff)) $msg.="\t<th>$col</th>\n";
		         		$msg.="</tr>\n";
		         		foreach($t1 as $t2) if($cpt<20) {
		         			$cpt++;
		         			$msg.="<tr>\n";
		         			foreach($t2 as $k=>$v) if(in_array($k,$colonnes_aff)) {
		         				$msg.="\t<td>$v</td>\n";
		         			}
		         			$msg.="</tr>\n";
		         		}
		         		$msg.="</table>\n";
				         $nom_pdf=getRewriteString("Suivi attestations jury $jury $annee");
			         	$_SESSION['data'][$nom_pdf]=$tPdf[$jury];
			         	//echo"<pre>";print_r($pdf['National']);die('</pre>');
			         	//$_SESSION['pied'][$nom_pdf]="Classement $departement $annee $titre2";
			         	$_SESSION['titre'][$nom_pdf]=html_entity_decode("Suivi attestations $annee - jury $jury ");
			         	$_SESSION['cols'][$nom_pdf]=$pdf_titres_utf;
			         	$_SESSION['options'][$nom_pdf]=$PDF_A3_PAYSAGE;
			         	//$_SESSION['format'][$nom_pdf]=array('taille'=>'A4', 'orientation'=>'portrait');
			         	$tmp1="<A HREF='../plugins/odb/odb_commun/inc-pdf-table.php?pdf=$nom_pdf'>";
			         	$tmp2=" G&eacute;n&eacute;rer le <b>suivi attestations jury $jury $annee</b> en PDF</A>";
			         	$msg.=$tmp1.vignette('pdf',"G&eacute;n&eacute;rer le suivi attestations jury $jury $annee en PDF").$tmp2;
			         	$lien_pdf[$nom_pdf]=$tmp1.$tmp2;
	         		//} else $msg.="<br>jury $jury : la suite &agrave; venir";
         		}
         	break;
         	default:
         		//global $msg;
         		include("$action.php");
   }
   $titre_action=ucfirst(str_replace(' d '," d'",str_replace("_",' ',$action)));
   $gauche="<hr size=1/>".$titre_action;
}

$nb_rows=mysql_affected_rows();
if($nb_rows>0) $gauche.="<hr size=1/><b>$nb_rows</b> enregistrements sont concern&eacute;s";
if(!in_array($action,array(
	'etat_candidats_par_etablissement_avec_num_table',
	'liste_d_inscription_des_candidats',
	'liste_d_inscription_des_candidats_affichage',
	'etat_repartition_des_candidats_par_departement_centre_serie',
	'etat_candidats_par_etablissement_et_par_serie',
	'etat_repartition_des_candidats_inscrits_par_serie_et_departement',
	'etat_preparation_des_jurys',
	'etat_classement_candidats','etat_classement_candidats_f','etat_classement_candidats_h',
	'etat_classement_etablissements_par_serie',
	'etat_classement_grandes_ecoles'
))) {
   debut_gauche();
      debut_boite_info();
         echo "<b>Statistiques SIOU</b>$gauche";
      fin_boite_info();
      odb_raccourcis('');
   creer_colonne_droite();
}
debut_droite();
      $nb_pdf=count($lien_pdf)+1;
      if($nb_pdf>1) {
         // il y a des documents pdf à générer
         echo "<table class='spip'>\n";
         echo "<tr valign='top'><td rowspan='$nb_pdf'>".vignette('pdf',"Documents PDF")."</td><th><small>Documents &agrave; g&eacute;n&eacute;rer</small></th></tr>\n";
         foreach($lien_pdf as $nom=>$lien)
            echo "<tr class='tr_liste'><td><small>$lien</small></td></tr>\n";
         echo "<tr><td colspan=2 align='center'><small>Cliquez sur un des liens ci-dessus pour g&eacute;n&eacute;rer la liste au format pdf</small></td></tr>\n";
         echo "</table>\n";
      }
   debut_cadre_relief("", false, "", $titre = _T("$titre_action"));
      echo "<!-- debut msg $action -->\n$msg\n<!-- debut msg $action -->\n";
   fin_cadre_relief();
echo "<br/>\n<!-- ================== Formulaire param ================= -->\n";
debut_boite_info();
//FIXME ne devrait jamais arriver puisque annee vaut normalement toujours qqch
if($annee=='') $annee=date("Y");
$inputAnnee="<SELECT name='annee' class='fondo'>\n"
				. formSelectAnnee($annee)
				. "</SELECT>\n"
				;

$inputDepartement="<SELECT NAME='stats_departement' class='fondo'>".formOptionsRefInSelect('departement',$stats_departement)."</SELECT>\n";
$inputCentre="<SELECT NAME='stats_centre' class='fondo' onChange=\"document.forms['form_stats_siou'].stats_etablissement.value=0;document.forms['form_stats_siou'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$stats_centre)."</SELECT>\n";
$inputResultatsCentre="<SELECT NAME='stats_resultats_centre' class='fondo' onChange=\"document.forms['form_stats_siou'].stats_etablissement.value=0;document.forms['form_stats_siou'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$stats_resultats_centre)."</SELECT>\n";
$inputEtablissement="<SELECT NAME='stats_etablissement' class='fondo' onChange=\"document.forms['form_stats_siou'].stats_centre.value=0;document.forms['form_stats_siou'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('etablissements',$stats_etablissement)."</SELECT>\n";
$inputSerie="<SELECT NAME='stats_serie' class='fondo' >".formOptionsRefInSelect('serie',$stats_serie)."</SELECT>\n";
//$inputComplet="<label for='stats_complet'>Complet</label><input type='checkbox' name='stats_complet' value='1'/>";
echo "<form name='form_stats_siou' method='POST' action='".generer_url_ecrire('odb_stats_siou')."' class='forml spip_xx-small' onSubmit=\"//return confirm('Avez-vous suffisamment filtre l\'affichage de votre choix ?');\">\n";
echo "<input type='hidden' name='etablissement_txt' value='$etablissement_txt'/>\n";
echo "<table border=0 cellspacing=0 cellpadding=1 width='100%' class='spip'>\n";
echo "<tr>\n\t<th>Action</th><td>$inputAnnee $inputDepartement $inputCentre $inputEtablissement $inputSerie</td>\n</tr>\n";
$title="&Eacute;tat d&eacute;tail des candidats par &eacute;tablissement et par s&eacute;rie";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_candidats_par_etablissement_avec_num_table' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/liste_etablissement.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
   . "<td>Listes des candidats de chaque &eacute;tablissement <b>pour communiquer les num&eacute;ros de table</b></td>\n"
   . "</tr>\n"
   ;
$title="&Eacute;tat d&eacute;tail des candidats par &eacute;tablissement et par s&eacute;rie";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_candidats_par_etablissement_et_par_serie' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/liste_etablissement.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
   . "<td>Listes des candidats de chaque &eacute;tablissement pour le <b>collationnement</b></td>\n"
   . "</tr>\n"
   ;
$title="Liste d'inscription des candidats (affichage)";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='liste_d_inscription_des_candidats_affichage' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/liste_affichage.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
	. "<td>Listes &agrave; envoyer dans les centres de composition <b>pour affichage</b></td>\n"
	. "</tr>\n"
	;
$title="Liste d'inscription des candidats (&eacute;margement)";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='liste_d_inscription_des_candidats' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/signature.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
	. "<td>Listes &agrave; envoyer dans les centres de composition <b>pour &eacute;margement</b></td>\n"
	. "</tr>\n"
	;
$title="Impression des convocations";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='impression_des_convocations' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/convocation.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
	. "<td>G&eacute;n&eacute;ration des convocations au format <b>pdf</b> par &eacute;tablissement et par s&eacute;rie</td>\n"
	. "</tr>\n"
	;
if(isAdmin()) {
	$title="&Eacute;tat r&eacute;partition des candidats";
	echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_repartition' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/dispo.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
		. "<td>Places disponibles et nombre de candidats r&eacute;partis, centre par centre</td>\n"
		. "</tr>\n"
		;
	$title="&Eacute;tat nombre de candidats par centre, par salle et par s&eacute;rie";
	echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_candidats_par_centre' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/enveloppe.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
		. "<td>Nombre de candidats par salle et par s&eacute;rie <b>pour la pr&eacute;paration des enveloppes</b></td>\n"
		. "</tr>\n"
		;
	$title="&Eacute;tat r&eacute;partition des candidats inscrits par d&eacute;partement, centre, s&eacute;rie";
	echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_repartition_des_candidats_par_departement_centre_serie' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/repartir_logo.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
		. "<td>Tableau synth&eacute;tique de la r&eacute;partition | <input type='image' align='absmiddle' name='action' value='etat_repartition_des_candidats_inscrits_par_serie_et_departement' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/repartir_logo.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/>par genre</td>\n"
		. "</tr>\n"
		;
	$title="Pr&eacute;paration des jurys";
	echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_preparation_des_jurys' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
		. "<td>$title</td>\n"
		. "</tr>\n"
		;
	echo "<tr><th colspan=2 style='text-align:center;'><hr size=1/>Statistiques concernant les notes<hr size=1/></th></tr>\n";
	$title="Taux de r&eacute;ussite";
	echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_candidats_admissibles_par_serie' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
		. "<td>$title | \n".
		  "<input align='absmiddle' type='image' name='action' value='etat_classement_etablissements_par_serie' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title &eacute;tablissements\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title &eacute;tablissements] fade=[on] fadespeed=[0.08]\"/> &Eacute;tablissements".
		  "</td>\n"
		. "</tr>\n"
		;   
	$title="Classement des candidats";
	echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_classement_candidats' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
		. "<td>$title | \n".
		  "<input align='absmiddle' type='image' name='action' value='etat_classement_candidats_f' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title (filles)\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title (filles)] fade=[on] fadespeed=[0.08]\"/> Filles | \n".
		  "<input align='absmiddle' type='image' name='action' value='etat_classement_candidats_h' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title (gar&ccedil;ons)\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title (gar&ccedil;ons)] fade=[on] fadespeed=[0.08]\"/> Gar&ccedil;ons\n".
		  "</td>\n</tr>\n"
		;
	$title="Statistiques sur le genre";
	echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_statistiques_genre' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
		. "<td>$title</td>\n"
		. "</tr>\n"
		;
	$title="Classement aux grandes &eacute;coles";
	echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_classement_grandes_ecoles' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\" onclick=\"if(document.forms['form_stats_siou'].ecole.value=='') {alert('Veuillez commencer par choisir une ecole');return false;}\"/></td>"
		. "<td>$title ".formSelectQuery("&Eacute;cole",'ecole',"SELECT distinct ecole from odb_ref_ecole order by ecole",'ecole','','class="fondo"')."</td>\n"
		. "</tr>\n"
		;
}   
echo "<tr><th colspan=2 style='text-align:center;'><hr size=1/>Impressions pour les candidats<hr size=1/></th></tr>\n";
$title='Attestations';
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='etat_impression_attestations' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
   . "<td>$title\n".
     //"<input align='absmiddle' type='image' name='action' value='etat_impression_diplomes' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title : Dipl&ocirc;mes\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title : Dipl&ocirc;mes] fade=[on] fadespeed=[0.08]\"/> Dipl&ocirc;mes \n".
     "<input align='absmiddle' type='image' name='action' value='etat_suivi_attestations' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title : Suivi\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title : Suivi] fade=[on] fadespeed=[0.08]\"/>$title : Suivi \n".
     "</td>\n</tr>\n"
   ;     
$title='R&eacute;sultats par centre';
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='resultats_par_centre' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title] fade=[on] fadespeed=[0.08]\"/></td>"
   . "<td>$inputResultatsCentre<br/>$title\n".
     //"<input align='absmiddle' type='image' name='action' value='etat_impression_diplomes' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title : Dipl&ocirc;mes\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title : Dipl&ocirc;mes] fade=[on] fadespeed=[0.08]\"/> Dipl&ocirc;mes \n".
     "<input align='absmiddle' type='image' name='action' value='resultats_refuses_par_centre' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title : refus&eacute;s\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title : refus&eacute;s] fade=[on] fadespeed=[0.08]\"/>Refus&eacute;s\n".
     "<input align='absmiddle' type='image' name='action' value='resultats_ajournes_par_centre' src='"._DIR_PLUGIN_ODB_STATS_SIOU."img_pack/jury.png' alt=\"$title : refus&eacute;s\" title=\"header=[$imgInfo Statistiques SIOU] body=[$title : ajourn&eacute;s] fade=[on] fadespeed=[0.08]\"/>Ajourn&eacute;s\n".
     "</td>\n</tr>\n"
   ;     
          
echo "</table>\n</form>\n";
fin_boite_info();
fin_cadre_relief();

fin_page();
exit;
}
?>
