<?php
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
define('DIR_ODB_CONTRIB',_DIR_PLUGINS.'odb/odb_contrib/');
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
   global $tab_referentiel,$imgInfo;
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
        . " $whereDept AND salle.annee=$annee "
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
      $idCentre=$row['id_etablissement'];
      $id_ville=$row['id_ville'];
      $ville=$tab_referentiel['ville'][$id_departement][$id_ville];
      $salle=$row['salle'];
      if($row['dispo']=='') $row['dispo']=$row['capacite_type'];
      $tSeriesSalle=getSeriesDansSalle($annee,$idCentre,$salle);
      $repartitionSalle='&nbsp;';
      if(is_array($tSeriesSalle)) {
			if(count($tSeriesSalle)>1) $style="style='color:#f00;'";
			else $style='';
      	$repartitionSalle='';
      	foreach($tSeriesSalle as $serie=>$nbCan) 
      		$repartitionSalle.="<b $style>$nbCan</b> ($serie)<br/>\n";
		}
		$tSeriesCentre=getSeriesDansCentre($annee,$idCentre,$salle);
		$repartition='Aucun candidat';
		if(is_array($tSeriesCentre)) {
			$repartition="<table class='spip'>\n<tr><th>S&eacute;rie</th><th>Candidats</th></tr>\n";
			foreach($tSeriesCentre as $serie=>$nbCan) {
				$repartition.="<tr><td>$serie</td><td>$nbCan</td></tr>\n";
			}
			$repartition.="</table>\n"
				. "<center><small>Derni&egrave;re salle : $repartitionSalle</small></center>"
				;
		}
      $row['repartition']=$repartition;
      $row['repartitionSalle']=$repartitionSalle;
      //echo"<br/>$etablissement $salle : ";print_r($tSeriesSalle);print_r($tSeriesCentre);
      $champs=array('salle','nb_salles','capacite','nb_repartis','capacite_type','dispo','id_etablissement','repartitionSalle','repartition');
      foreach($champs as $champ)
         $tab_repart[$departement][$ville][$etablissement][$salle][$champ]=$row[$champ];
   }


   $thead="<th><small>".$cpt_bidon++."</small></th><th><small>Dept</small></th><th><small>Ville</small></th><th><small>Centre</small></th><th><small>Salle</small></th><th><small>Capacit&eacute;</small></th><th><small>Dispo</small></th><th title=\"header=[$imgInfo Aide] body=[&Agrave; r&eacute;partir dans la salle en cours] fade=[on] fadespeed=[0.08]\"><small>R&eacute;partir<br/>$imgInfo</small></th><th title=\"header=[$imgInfo Aide] body=[R&eacute;partition des s&eacute;ries<ul><li>dans derni&egrave;re salle</li><li>dans le centre</li></ul> ] fade=[on] fadespeed=[0.08]\"><small>R&eacute;partition<br/>$imgInfo</small></th>";
   ksort($tab_repart);
   foreach($tab_repart as $dept=>$tab) {
      foreach($tab as $ville=>$tab1)
         foreach($tab1 as $centre=>$tab2) {
            foreach($tab2 as $salle=>$tab3) {
            	foreach(array('dispo','capacite','nb_salles','capacite_type','repartitionSalle','repartition','id_etablissement') as $col)
            		$$col=$tab3[$col];
               if($capacite_type!=$dispo) $dispo_aff="<span style='color:rgb(".round((1-$dispo/$capacite_type)*255,0).",0,0);'><b>$dispo</b></span>";
               else $dispo_aff=$dispo;
               if($dept!=$old_dept) $departement="<b>$dept</b>";
               else $departement=$dept;
               if($ville!=$old_ville) $ville_aff="<div id='".getRewriteString($ville)."'><b>$ville</b></div>";
               else $ville_aff=$ville;
               $salleEnCours=$capacite-(($capacite_type-$dispo)%$capacite);
               $tbody[]= "<tr class='tr_liste'>"
               	. "<td><small>".$cpt_bidon++."</small></td>"
               	. "<td><small>$departement</small></td><td><small>$ville_aff</small></td>"
               	. "<td title='Centre #$id_etablissement'><small><div id='".getRewriteString($centre)."'>$centre</div></small></td>"
               	. "<td title=\"header=[$imgInfo $centre <small>($salle)</small>] body=[<b>$nb_salles</b> salles <span style='border:1px solid #369;padding:2px;color:#000;font-weight:bold;'>$salle</span> de <b>$capacite</b> places</span><br/><br/><small>Cliquez ici pour r&eacute;partir * candidats</small>] fade=[on] fadespeed=[0.08]\"><small><A href='javascript:;' onClick=\"".htmlRepartitionInEventJS($id_etablissement,$salle)."\" style='color:#555;'>$salle </small>$imgInfo</A></td>"
                  . "<td><small>$capacite_type</small></td>"
                  . "<td><small>$dispo_aff</small></td><td><small><A title='Cliquez ici pour r&eacute;partir $salleEnCours candidats' href='javascript:;' onClick=\"".htmlRepartitionInEventJS($id_etablissement,$salle,$salleEnCours)."\" style='color:#555;'><b>$salleEnCours</b></A><small style='color:#666;'>/$capacite</small></small></td>"
                  . "<td title=\"header=[$imgInfo $centre <small>($salle)</small>] body=[$repartition] fade=[on] fadespeed=[0.08]\"><small><p style='border:1px solid #aaa;padding:2px;'>$imgInfo $repartitionSalle</p></small></td>"
                  . "</tr>\n"
                  ;
               $old_dept=$dept;
               $old_ville=$ville;
            }
         }
   }
   return odb_html_table("Capacit&eacute; des centres",$tbody,$thead);
}

/** Ligne "Total ville" du formulaire de repartition
 * @param int $idVille : id_ville d'origine
 * @param string $ville : ville d'origine
 * @param int $idCentre : Centre de composition de destination
 * @param int $nbRepartir : nombre de candidats a repartir dans ce centre
 * @param int $nbCandidats : nombre de candidats total dans le contexte (departement et/ou serie)
 * @return string : ligne HTML a mettre dans un odb_html_table()
 */
function htmlLigneRepartisseurTotalVille($idVille,$ville,$idCentre,$nbRepartir,$nbCandidats) {
	if((int)$nbRepartir>1) $s='s';else $s='';
	$str= "<TH colspan=2 title='Ville $idVille'><small><div>Total $ville</div></small></TH>\n\t"
			."<TH colspan=2><small>"
			."<A HREF='#capaciteCentres' onClick=\"element=document.getElementById('".getRewriteString($ville)."');"
			. "if(element==null) {element=document.getElementById('boiteInfoCapacite');element.innerHTML='<b>".addslashes($ville)."</b>'}"
			. "document.forms['form_repartition'].nombre.value=$nbRepartir;"
			. "document.forms['form_repartition'].ok_particulier.disabled=false;"
			. "document.forms['form_repartition'].ok_generique.disabled=true;"
			. "document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces $nbRepartir num&eacute;ros de table';"
			. "document.forms['form_repartition'].ok_particulier.focus();"
			. "document.forms['form_repartition'].envoi_centre.value=$idCentre;"
			. "document.forms['form_repartition'].repart_ville.value=$idVille;"
			. "document.forms['form_repartition'].repart_etablissement.value=0;"
			. "document.forms['form_repartition'].nombre.select();"
			. "element.style.border='1px dashed #f33;';"
			. "element.style.padding='2px';"
			. "element.style.backgroundColor='#fb6';"
			. "element.innerHTML+='<br/>$nbRepartir candidat$s &agrave; r&eacute;partir';"
			. "\""
			." TITLE='Cliquez pour acc&eacute;der &agrave; leur r&eacute;partition'>"
			." $nbRepartir candidat$s</A></small>"
			."</TH>\n\t"
			."<TH>".afficheTaux($nbRepartir/$nbCandidats,1)."</TH>\n"
			;
	return $str;
}

/** Ligne du formulaire de repartition
 * @param string $ville : ville d'origine
 * @param int $idEtablissement : etablissement d'origine
 * @param string $etablissement : etablissement d'origine
 * @param int $idCentre : Centre de composition de destination
 * @param string $centre : Centre de composition de destination
 * @param int $nbRepartir : nombre de candidats a repartir dans ce centre
 * @param int $nbCandidats : nombre de candidats total dans le contexte (departement et/ou serie)
 * @return string : ligne HTML a mettre dans un odb_html_table()
 */
function htmlLigneRepartisseur($ville,$idCentre,$centre,$idEtablissement,$etablissement,$nbRepartir,$nbCandidats) {
	if((int)$nbRepartir>1) $s='s';else $s='';
	$idVilleCentre=getIdVilleEtablissement($idCentre);
	$villeCentre=odb_propre(getLibelleVille($idVilleCentre));
	if(substr_count($centre,$villeCentre)>0)
		$centre_aff=str_replace($villeCentre,"<b>$villeCentre</b>",$centre);
	else $centre_aff="$centre<br/><i>$villeCentre</i>";
	$str="<TD><small>$ville</small></TD>\n\t"
		."<TD><small><div title='&Eacute;ablissement $idEtablissement'>$etablissement</div></small></TD>\n\t"
		."<TD><small><div title='Centre $idCentre - $villeCentre'>$centre_aff</div></small></TD>\n\t"
		."<TD><small>"
		."<A href='#capaciteCentres' onClick=\"element=document.getElementById('".getRewriteString($centre)."');\n"
		. "if(element==null) {element=document.getElementById('boiteInfoCapacite');element.innerHTML='<b>".addslashes($centre)."</b>'}\n"
		. "document.forms['form_repartition'].nombre.value=$nbRepartir;\n"
		. "document.forms['form_repartition'].ok_particulier.disabled=false;\n"
		. "document.forms['form_repartition'].ok_generique.disabled=true;\n"
		. "document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces $nbRepartir num&eacute;ros de table';\n"
		. "document.forms['form_repartition'].ok_particulier.focus();\n"
		. "document.forms['form_repartition'].envoi_centre.value=$idCentre;\n"
		. "document.forms['form_repartition'].repart_etablissement.value=$idEtablissement;\n"
		. "document.forms['form_repartition'].repart_ville.value=0;\n"
		. "document.forms['form_repartition'].nombre.select();\n"
		. "element.style.border='1px dashed #f33;';\n"
		. "element.style.padding='2px';\n"
		. "element.style.backgroundColor='#fb6';\n"
		. "element.innerHTML+='<br/>$nbRepartir candidat$s &agrave; r&eacute;partir';\n"
		//. "$eltCentre.title='Vous devez r&eacute;partir $nbRepartir candidats dans ce centre'"
		. "\""
		." TITLE='Cliquez pour acc&eacute;der &agrave; leur r&eacute;partition'>"
		. "<b>$nbRepartir</b></A></small></TD>\n\t"
		."<TD>".afficheTaux($nbRepartir/$nbCandidats,1)."</TD>\n"
		;
	return $str;
}

/** Repartition javascript dans un evenement onClick...
 * @param int $idCentre : centre de composition dans lequel repartir
 * @param string $salle : type de salle du centre dans lequel repartir
 * @param string $nbCan : nb de candidats a repartir
 * @return string : code JS de l'evenement
 */
function htmlRepartitionInEventJS($idCentre,$salle,$nbCan='*') {
	$str="document.forms['form_repartition'].nombre.value='$nbCan';\n"
		. "document.forms['form_repartition'].ok_particulier.disabled=false;\n"
		. "document.forms['form_repartition'].ok_generique.disabled=true;\n"
		. "document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces $nbCan num&eacute;ros de table';\n"
		. "document.forms['form_repartition'].ok_particulier.focus();\n"
		. "document.forms['form_repartition'].envoi_centre.value=$idCentre;\n"
		. "document.forms['form_repartition'].envoi_salle.value='$salle';\n"
		//. "document.forms['form_repartition'].repart_etablissement.value=0;"
		//. "document.forms['form_repartition'].repart_ville.value=0;"
		. "document.forms['form_repartition'].nombre.select();\""
		;
	return $str;
}

/** Repartition des candidats ODB
 * 
 * @author <a href='mailto:cedric [at] protiere [dot] com'>Cedric PROTIERE</a>
 * @version 1.1
 */
function exec_odb_repartition() {
global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping,$imgInfo;

include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");
echo "<SCRIPT SRC='".DIR_ODB_CONTRIB."boxover/boxover.js'></SCRIPT>\n";
$imgInfo="<img src='".DIR_ODB_CONTRIB."boxover/info.gif' style='vertical-align:middle'>";

$annee=date("Y");

$array_ref=array('departement','etablissement','ef','lv','eps','prefixe','serie','sexe','ville','pays');
foreach($array_ref as $ref)
   $tab_referentiel[$ref]=getReferentiel($ref,'tout');

debut_page(_T('R&eacute;partition des candidats'), "", "");
//echo "<br />";
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
//echo '<br/>';

$REFERER=$_SERVER['HTTP_REFERER'];
$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];

isAutorise(array('Admin'));

$etablissement_txt=$_REQUEST['etablissement_txt'];
if(strlen($_POST['ok_particulier'])>0) {
//////////////////////////////////////////////// validation du formulaire : cas particulier
$annee=$_REQUEST['annee'];
$nombre=$_REQUEST['nombre'];
$envoi_centre=$_REQUEST['envoi_centre'];
$envoi_salle=$_REQUEST['envoi_salle'];
//if($etablissement_txt=='') 
$etablissement_txt=getLibelleEtablissement($envoi_centre);

if($nombre=='*') $nombre='0';

foreach($_POST as $key=>$val)
if(substr_count($key,'repart_')>0) {
   $$key=$val;
   $colonne=substr($key,strlen('repart_'));
   //echo "$key=$val ($colonne)<br/>\n";
   if($val>0) $par[$colonne]=$val;
}

if((int)$envoi_centre>0 && $envoi_salle!='') {
	$repartition=repartirCandidats($par,'',$envoi_centre,$envoi_salle,$etablissement_txt,$annee,$nombre);
} else 
	die(KO." - Erreur de r&eacute;partition - [$nombre] candidats [$annee] =&gt; Centre [$etablissement_txt] ([$envoi_centre]), salle [$envoi_salle]");
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
   debut_boite_info();
      echo $repartition['msg_info'];
      echo "<br/>\n<small>";
      echo $repartition['msg_repartition'].'</small>';
   fin_boite_info();
/*
debut_boite_info();
   echo capaciteCentres($annee, $repart_departement);
fin_boite_info();
*/
$inputSerie="<SELECT NAME='repart_serie' class='forml'>".formOptionsRefInSelect('serie',$repart_serie)."</SELECT>\n";
$inputDepartement="<SELECT NAME='repart_departement' class='forml'>".formOptionsRefInSelect('departement',$repart_departement)."</SELECT>\n";
$inputCentre="<SELECT NAME='envoi_centre' style='background-color:#f66;' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$envoi_centre,'',$idDept)."</SELECT>\n";
}
if(strlen($_REQUEST["step3"])>0) {
//////////////////////////////////////////////// step 3 : affichage répartition dépt/série par établissement
$annee=$_REQUEST['annee'];
$idDept=$_REQUEST['idDept'];
//$nbCandidats=$_REQUEST['nbCandidats'];
$step3=$_REQUEST['step3'];
$inputHidden="<INPUT type='hidden' name='step3' value='$step3'/>";
$idSerie=$_REQUEST['idSerie'];
$nbCandidats=getNbCandidats($annee,$idDept,$idSerie);
$nbCandidatsARepartir=getNbCandidatsARepartir($annee,$idDept,$idSerie);
$departement=$tab_referentiel['departement'][$idDept];
$serie=$tab_referentiel['serie'][$idSerie];
$inputSerie="<SELECT NAME='repart_serie' class='forml'>".formOptionsRefInSelect('serie',$idSerie,'S&eacute;rie')."</SELECT>\n";
$inputDepartement="<SELECT NAME='repart_departement' class='forml'>".formOptionsRefInSelect('departement',$idDept)."</SELECT>\n";
$inputCentre="<SELECT NAME='envoi_centre' style='background-color:#f66;' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centre',$envoi_centre,'',$idDept)."</SELECT>\n";
$inputEtablissement="<SELECT NAME='repart_etablissement' class='forml'>".formOptionsRefInSelect('etablissement',$repart_etablissement,'&Eacute;tablissement',$idDept)."</SELECT>\n";
$inputVille="<SELECT NAME='repart_ville' class='forml'>".formOptionsRefInSelect('ville',0,'Ville',$idDept)."</SELECT>\n";
   debut_gauche();
debut_cadre_relief("", false, "", $titre = _T("Module r&eacute;partition"));
		echo "<IMG SRC='"._DIR_PLUGIN_ODB_REPARTITION."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";
      //debut_boite_info();
         echo "<hr size=0/><center style='font-weight:bold;'>"
            . "<A style='font-size:100px;color:#222;' title='R&eacute;partition des $serie' HREF='".generer_url_ecrire('odb_repartition')."&step2=isSerie&idSerie=$idSerie&nbCandidats=&annee=$annee'>"
            . "$serie</A><br/><A style='font-size:30px;color:#555;' title='R&eacute;partition dans $departement' HREF='".generer_url_ecrire('odb_repartition')."&step2=isDept&idDept=$idDept&nbCandidats=&annee=$annee'>"
            . "$departement</A></center>\n"
            ;
      //fin_boite_info();
fin_cadre_relief();
		debut_raccourcis();
			icone_horizontale (_L("R&eacute;f&eacute;rentiel &eacute;tablissements $departement"), generer_url_ecrire('odb_ref')."&step2=manuel&table=ETA|$idDept|$departement|odb_ref_etablissement", "../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png");
			icone_horizontale (_L('Consulter / saisir ces candidats'), generer_url_ecrire('odb_saisie')."&filtreSerie=$idSerie&filtreDepartement=$idDept#acces_clic", "../"._DIR_PLUGIN_ODB_REF."/img_pack/siou_carre.png");
		creer_colonne_droite();
   debut_droite();
      debut_cadre_relief("", false, "", $titre = _T("R&eacute;partition des candidats $annee"));
         $titre="R&eacute;partition ".$tab_referentiel['departement'][$idDept].", s&eacute;rie ".$tab_referentiel['serie'][$idSerie]." ($nbCandidatsARepartir/$nbCandidats candidats)";
         $thead="<TH>Ville</TH><TH>&Eacute;tablissement</TH><TH><small>Centre<br/>+ proche</small></TH><TH><small>Candidats<br/>&agrave; r&eacute;partir</small></TH><TH>%</TH>";
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
					$tab[]=htmlLigneRepartisseurTotalVille($old_idVille,$old_ville,$old_idCentre,$cptVille,$nbCandidats);
					$tab[]="<td colspan=5><hr size=0/></td>";
            	$cptVille=$nb_rows;
				} else {
					$cptVille+=$nb_rows;
				}
            foreach(array('etablissement','ville','centre') as $lieu)
               if(trim($$lieu)=="" )
                  $$lieu=ucfirst($lieu).' <small>['.${"id".ucfirst($lieu)}.']</small>';
            $tab[]=htmlLigneRepartisseur($ville,$idCentre,$centre,$idEtablissement,$etablissement,$nb_rows,$nbCandidats);
            $old_ville=$ville;
            $old_idCentre=$idCentre;
         }
			// derniere ville de la liste
			$tab[]=htmlLigneRepartisseurTotalVille($idVille,$ville,$old_idCentre,$cptVille,$nbCandidats);
			$tab[]="<td colspan=5><hr size=0/></td>";
         echo odb_html_table($titre,$tab,$thead);
      fin_cadre_relief();
   debut_boite_info();
   	echo "<A NAME='capaciteCentres'></A><small><div align='right'>$nbCandidatsARepartir/$nbCandidats candidats en $serie ($departement)</div><div align='right' id='boiteInfoCapacite' onClick=\"this.innerHTML='';this.style.backgroundColor='#fff;';this.style.borderStyle='none;';\"></div></small>";
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
		echo "<IMG SRC='"._DIR_PLUGIN_ODB_REPARTITION."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";
      debut_boite_info();
         echo "<p>R&eacute;partition des candidats <b>".$tab_referentiel['departement'][$idDept]." ".$tab_referentiel['serie'][$idSerie]."</b>"
            . "<hr size=1><b>Cliquez</b> sur le nombre de candidats pour <b>pr&eacute;remplir le formulaire</b> des cas particulier de r&eacute;partition</p>\n"
            ;
      fin_boite_info();
      odb_raccourcis('');
   creer_colonne_droite();
   debut_droite();
      debut_cadre_relief("", false, "", $titre = _T("R&eacute;partition des candidats $annee"));
         if($step2=='isDept') {
            echo "\n<!-- tri par departement -->\n";
            $inputSerie="<SELECT NAME='repart_serie' class='forml'>".formOptionsRefInSelect('serie',$repart_serie)."</SELECT>\n";
            $inputDepartement="<SELECT NAME='repart_departement' class='forml'>".formOptionsRefInSelect('departement',$idDept)."</SELECT>\n";
            $inputVille="<SELECT NAME='repart_ville' class='forml'>".formOptionsRefInSelect('ville',$repart_ville,'Ville',$idDept)."</SELECT>\n";
            $inputCentre="<SELECT NAME='envoi_centre' style='background-color:#f66;' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centre',$envoi_centre,'',$idDept)."</SELECT>\n";
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
            $inputCentre="<SELECT NAME='envoi_centre' style='background-color:#f66;' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$envoi_centre)."</SELECT>\n";
            $inputEtablissement="<SELECT NAME='repart_etablissement' class='forml'>".formOptionsRefInSelect('etablissements',$repart_etablissement,'&Eacute;tablissement')."</SELECT>\n";
            $serie=$tab_referentiel['serie'][$idSerie];
            $titre="R&eacute;partition s&eacute;rie $serie par d&eacute;partement ($nbCandidats candidats)";
            $triPar="D&eacute;partement";
            $ref=$tab_referentiel['departement'];
            $isDept=false;
         } else die(KO." - Impossible de d&eacute;terminer le tri");

         $thead="<TH>$triPar</TH><TH><small>Nombre de candidats<br>&agrave; r&eacute;partir</small></TH><TH>%</TH>";
         foreach($ref as $id => $valeur) {
            if(is_numeric($id)) {
               if($isDept)
                  $idSerie=$id;
               else
                  $idDept=$id;
               $nb_rows=(int)getNbCandidatsARepartir($annee,$idDept,$idSerie);

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
         echo odb_html_table($titre,$tab,$thead);
      fin_cadre_relief();
} 
if(!((strlen($_POST['ok_generique'])>0 || strlen($_REQUEST['from_ville']>0) || strlen($_REQUEST['from_etablissement']>0))) && $step2=='' && $step3=="") {
//////////////////////////////////////////////// step 1 : affichage répartition géo / département
$annee=isset($_REQUEST['annee'])?$_REQUEST['annee']:date("Y");
if(!isset($_REQUEST['ok_particulier'])) {
   debut_gauche();
		echo "<IMG SRC='"._DIR_PLUGIN_ODB_REPARTITION."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br/>\n";
		debut_boite_info();
			echo "R&eacute;partition $annee";
		fin_boite_info();
      odb_raccourcis('odb_repartition');
   creer_colonne_droite();
   debut_droite();
}
      $inputSerie="<SELECT NAME='repart_serie' class='forml'>".formOptionsRefInSelect('serie',$repart_serie)."</SELECT>\n";
      $inputDepartement="<SELECT NAME='repart_departement' class='forml'>".formOptionsRefInSelect('departement',$repart_departement)."</SELECT>\n";
      $inputVille="<SELECT NAME='repart_ville' class='forml'>".formOptionsRefInSelect('villes',$repart_ville)."</SELECT>\n";
      $inputCentre="<SELECT NAME='envoi_centre' style='background-color:#f66;' class='forml' onChange=\"document.forms['form_repartition'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$envoi_centre)."</SELECT>\n";

		debut_boite_info();
      	$nbErreurs=odb_introspection($annee);
      	$s=$nbErreurs>1?'s':'';
      	if($nbErreurs==0) {
      		$nbErreurs='Aucune';
      		$append=" - vous pouvez proc&eacute;der &agrave; la r&eacute;partition $annee";
			}
      	else $append="<br/><b>Veuillez corriger chaque erreur</b> avant de commencer la r&eacute;partition";
      	echo "<b>$nbErreurs</b> erreur$s d&eacute;tect&eacute;e$s $append";
      fin_boite_info();
      echo "<br/>";

      debut_cadre_relief("", false, "", $titre = _T("R&eacute;partition des candidats $annee"));
         $nbCandidats=(int)getNbCandidatsARepartir($annee);
         $titre="R&eacute;partition g&eacute;ographique B&eacute;nin ($nbCandidats candidats)";
         $tab[]="<TH>D&eacute;partement</TH><TH><small>Nombre de candidats<br/>&agrave; r&eacute;partir</small></TH><TH>%</TH>";
         foreach($tab_referentiel['departement'] as $idDept => $dept) {
            if(is_numeric($idDept)) {
               $nb_rows=(int)getNbCandidatsARepartir($annee,$idDept,0);

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
         echo odb_html_table($titre,$tab);
         echo "<hr size=1>\n";
         unset($tab);
         $titre="R&eacute;partition par s&eacute;rie B&eacute;nin ($nbCandidats candidats)";
         $tab[]="<TH>S&eacute;rie</TH><TH><small>Nombre de candidats<br/>&agrave; r&eacute;partir</small></TH><TH>%</TH>";
         foreach($tab_referentiel['serie'] as $idSerie => $serie) {
            if(is_numeric($idSerie)) {
               $nb_rows=(int)getNbCandidatsARepartir($annee,0,$idSerie);

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
         echo odb_html_table($titre,$tab);

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

$inputSalle="<input name='envoi_salle' value='' size=3 class='fondo'/>";
echo "<form name='form_repartition' method='POST'"
	. " action='".generer_url_ecrire('odb_repartition')."' class='forml spip_xx-small' "
	. "onSubmit=\"if(this.envoi_salle.value=='') {"
	. "  if(confirm('Vous n\'avez pas choisi de type de salle\\nSouhaitez-vous choisir le type [A] ?')) {"
	. "    this.envoi_salle.value='A';return true;"
	. "  } else {this.envoi_salle.focus();return false;}"
	. "}\">\n";
echo "<FIELDSET style='background-color:#eee;'>\n";
echo "<LEGEND>Cas particuliers</LEGEND>\n";
echo "Disposer...\n"
   . "<ul>\n<li><INPUT name='nombre' class='fondo' size=6 maxlength=6 value='0' onKeyUp=\"if(this.value>0 || this.value=='*') {document.forms['form_repartition'].ok_generique.disabled=true;document.forms['form_repartition'].ok_particulier.disabled=false;document.forms['form_repartition'].ok_particulier.value='G&eacute;n&eacute;rer ces '+this.value+' num&eacute;ros de table';} else {document.forms['form_repartition'].ok_generique.disabled=false;document.forms['form_repartition'].ok_particulier.disabled=true;document.forms['form_repartition'].ok_particulier.value='Choisissez le nombre de candidats &agrave; r&eacute;partir';}\"> candidats (tapez <b>*</b> pour tous ceux qui correspondent aux crit&egrave;res ci-dessous)</li>\n</ul>\n"
   . "<FIELDSET style='background-color:#ccc;'>\n<LEGEND>Choix recommand&eacute;s</LEGEND>\n"
   . "<table border=0 class='spip' style='border-style:none;' width='100%'>\n"
   . "<tr><td><label for='repart_serie'>issus de série</label></td><th>$inputSerie</th></tr>\n"
   . "<tr><td><label for='repart_dept'>et du d&eacute;partement</label></td><th>$inputDepartement</th></tr>\n"
   . "<tr><td><label for='repart_ville'>et de la ville</label></td><th>$inputVille</th></tr>\n"
   . "<tr><td><label for='repart_etablissement'>et de l'&eacute;tablissement</label></td><th>$inputEtablissement</th></tr>\n"
   . "</table>\n</FIELDSET>\n"
   . "<FIELDSET style='background-color:#ddd;'>\n<LEGEND>Autres choix</LEGEND>\n"
   . "<table border=0 width='100%' class='spip' style='border-style:none;'>\n"
   . "<tr><td>Langues</td><td>$inputLv1 $inputLv2</td></tr>\n"
   . "<tr><td>&Eacute;preuves facultatives</td><td>$inputEf1 $inputEf2</td></tr>\n"
   . "<tr><td>Sport</td><td>$inputEps</td></tr></table>\n"
   . "</FIELDSET>\n"
   . "<table border=0 class='spip' width='100%' style='border-style:none;'>"
   . "<tr><th>...dans le centre</th><th>$inputCentre</th><th><label for='envoi_salle'>Type de salle</label></th><td>$inputSalle</td></tr>"
   . "</table>\n"
   ;
echo "<INPUT TYPE=SUBMIT name='ok_particulier' CLASS='fondo' VALUE='Veuillez choisir un nombre de candidats &agrave; r&eacute;partir' disabled"
   . " onclick=\"if(document.forms['form_repartition'].envoi_centre.value=='0') {alert('Veuillez choisir le centre dans lequel vous souhaitez\\ndisposer ces candidats');document.forms['form_repartition'].envoi_centre.focus();return false;}\"/>\n";
echo "</FIELDSET></FIELDSET><br/>\n";
echo "<FIELDSET>\n";
echo "<LEGEND>Cas g&eacute;n&eacute;riques</LEGEND>\n";
echo "Si vous avez trait&eacute; tous les cas particuliers, vous pouvez :<br/>\n";
$nb_restant=getNbCandidatsARepartir($annee);
echo "<INPUT TYPE=SUBMIT name='ok_generique' CLASS='fondo' VALUE='G&eacute;n&eacute;rer les $nb_restant num&eacute;ros de table restant' onclick=\"return confirm('Vous souhaitez terminer le processus.\\nEtes-vous bien certain d\'avoir fini la disposition de tous les cas particuliers ?\\n - Series E, F1-F4\\n - Langues mortes\\n - etc')\">\n";
echo "</FIELDSET>\n";
foreach(array('step2','step3','idDept','idSerie','idEtablissement','nbCandidats','annee','from_ville','from_etablissement','envoi_nombre','etablissement_txt') as $hidden)
	echo "<INPUT TYPE='hidden' NAME='$hidden' VALUE='".$$hidden."'/>\n";
echo "</form>\n";
fin_boite_info();
fin_cadre_relief();
fin_page();
exit;
}
?>
