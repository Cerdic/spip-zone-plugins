<?php
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN.'inc-odb.php');
include_once(DIR_ODB_COMMUN."inc-referentiel.php");

setlocale(LC_TIME, "fr_FR");

global $debug, $txt_gauche, $txt_debug;
$debug=false;

$txt_gauche="";// texte boite de gauche
$txt_="";// texte boite de droite
$txt_debug=""; // texte debug
define(OK,"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define(KO,"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

// exécuté automatiquement par le plugin au chargement de la page ?exec=odb_repartition
function exec_odb_statistiques() {
global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping;

include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");
$annee=date("Y");

$array_ref=array('departement','etablissement','ef','lv','eps','prefixe','serie','sexe','ville','pays','prefixe');
foreach($array_ref as $ref)
   $tab_referentiel[$ref]=getReferentiel($ref,'tout');

debut_page(_T('Statistiques candidats'), "", "");
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
   echo "<hr/>Auteur<pre style='text-align:left;'>";
   print_r($tab_auteur);
   echo "</pre><hr/>";
   echo "_POST<pre style='text-align:left;'>";
   print_r($_POST);
   echo "</pre><hr/>";
   echo "tab_referentiel<pre style='text-align:left;'>";
   print_r($tab_referentiel);
   echo "</pre><hr/>";
   echo "<A NAME='fin_debug'></A>\n";
}

debut_cadre_relief( "", false, "", $titre = _T('R&eacute;partition des candidats'));
//debut_boite_info();
echo '<br>';

$REFERER=$_SERVER['HTTP_REFERER'];
$REMOTE_ADDR=$_SERVER['REMOTE_ADDR'];

echo "<IMG SRC='"._DIR_PLUGIN_ODB_REPARTITION."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";

//////////////////////////////////////////////// step 1 : formulaire de stats
$annee=isset($_REQUEST['annee'])?$_REQUEST['annee']:date("Y");
$etablissement_txt=isset($_REQUEST['etablissement_txt'])?$_REQUEST['etablissement_txt']:'';
$centre_txt=isset($_REQUEST['centre_txt'])?$_REQUEST['centre_txt']:'';
debut_gauche();
   debut_boite_info();
      echo "<b>Statistiques sur les candidats</b><br/>Choisissez vos crit&eacute;res et comptez les candidats !";
   fin_boite_info();
   odb_raccourcis('');
creer_colonne_droite();
debut_droite();
   debut_cadre_relief("", false, "", $titre = _T("Statistiques candidats $annee"));

   $colonnes=array('nom','serie','departement','etablissement','centre','lv1','lv2','ef1','ef2','eps');
   if(isset($_REQUEST['step2'])) {
      $criteres="";
      $sql="SELECT can.id_saisie, can.id_table, prefixe, nom, prenoms, jury from odb_candidats can left join odb_repartition rep on can.id_saisie=rep.id_saisie where rep.annee=$annee and can.annee=$annee ";
      foreach($colonnes as $col) {
         ${"stats_$col"}=$_REQUEST["stats_$col"];
         $tmp1='';$tmp2='';
         $ref=$col;
         switch($col) {
            case 'serie':
               $tmp1='en s&eacute;rie';
               break;
            case 'departement':
               $tmp1='dans le d&eacute;partement';
               break;
            case 'etablissement':
               $tmp1='dans l\'&eacute;tablissement';
               $tab_referentiel[$ref][${"stats_$col"}]=$etablissement_txt;
               break;
            case 'centre':
               $tmp1='dans le centre';
               $tab_referentiel[$ref][${"stats_$col"}]=$centre_txt;
               break;
            case 'lv1':
               $tmp1='en LV1';
               $ref='lv';
               break;
            case 'lv2':
               $tmp1='en LV2';
               $ref='lv';
               break;
            case 'ef1':
               $tmp1='ayant l\'&eacute;preuve facultative 1';
               $ref='ef';
               break;
            case 'ef2':
               $tmp1='ayant l\'&eacute;preuve facultative 2';
               $ref='ef';
               break;
            case 'eps':
               $tmp2='en sport';
               break;
            case 'nom':
               $tmp1='qui se nomment';
               break;
         }
         if(${"stats_$col"} !=0) {
            $criteres .= ", $tmp1 <b>".$tab_referentiel[$ref][${"stats_$col"}]."</b> $tmp2";
            if($col!='centre') $sql.=" AND can.$col='".${"stats_$col"}."'";
            else $sql .= " AND rep.id_etablissement=".${"stats_$col"}."";
         }
         if($col=='nom' && trim($stats_nom)!='') {
            $criteres .= ", $tmp1 <b>".ucfirst(strtolower(${"stats_$col"}))."</b> $tmp2";
            $sql.=" AND $col='".${"stats_$col"}."'";
         }
      }
      $sql.=" order by nom, prenoms";
      //echo "$sql<hr/>\n";
      $result=odb_query($sql,__FILE__,__LINE__);
      $nbCandidats=mysql_num_rows($result);
      $rep="Il y a <b>$nbCandidats</b> candidat(e)(s) en <b>$annee</b>$criteres";
      echo $rep;
      if($nbCandidats<=50) {
         echo " :<br/>\n";
         debut_boite_info();
         echo "<table width='100%' border=0 cellpadding=1 cellspacing=0>\n<tr>\n";
         $champs=array('id_saisie','id_table','prefixe','nom','prenoms','jury');
         foreach($champs as $champ)
            if(!in_array($champ, array('prefixe','prenoms')))
               echo "\t<th><small>".ucfirst(str_replace('id_','Num&eacute;ro ',$champ))."</small></th>";
         echo "</tr>\n";
         while($row=mysql_fetch_array($result)) {
            echo "<tr class='tr_liste'>\n\t";
            foreach($champs as $champ)
               $$champ=$row[$champ];
            echo "<td><small><A HREF='".generer_url_ecrire('odb_saisie')."&annee=$annee&step2=odb_candidats&identifiant=id_saisie&id=$id_saisie'>$id_saisie</A></small></td><td><small>$id_table</small></td><td><small>".$tab_referentiel['prefixe'][$prefixe]." <b>$nom</b> $prenoms</small></td><td>$jury</td></tr>\n";
         }
         echo "</table>\n";
         fin_boite_info();
      } else echo ".<br/><small>Il y a plus de 50 r&eacute;ponses, veuillez mieux filtrer pour afficher les r&eacute;sultats.</small>";
   } else echo "Veuillez choisir les crit&egrave;res permettant de compter les candidats ci-dessous\n";

   fin_cadre_relief();
echo "<br/>\n<!-- ================== Formulaire repartition ================= -->\n";
debut_boite_info();

$inputAnnee="<SELECT NAME='annee' class='forml'>".formSelectAnnee($annee)."</SELECT>\n";
$inputNom="<INPUT NAME='stats_nom' value='$stats_nom' class='forml'>\n";
$inputSerie="<SELECT NAME='stats_serie' class='forml'>".formOptionsRefInSelect('serie',$stats_serie)."</SELECT>\n";
$inputDepartement="<SELECT NAME='stats_departement' class='forml'>".formOptionsRefInSelect('departement',$stats_departement)."</SELECT>\n";
$inputEtablissement="<SELECT NAME='stats_etablissement' class='forml' onChange=\"document.forms['form_stats'].etablissement_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('etablissements',$stats_etablissement)."</SELECT>\n";
$inputCentre="<SELECT NAME='stats_centre' class='forml' onChange=\"document.forms['form_stats'].centre_txt.value=this.options[selectedIndex].text;\">".formOptionsRefInSelect('centres',$stats_centre)."</SELECT>\n";
$inputLv1="<SELECT NAME='stats_lv1' class='fondo'>".formOptionsRefInSelect('lv',$stats_lv1,'LV1')."</SELECT>\n";
$inputLv2="<SELECT NAME='stats_lv2' class='fondo'>".formOptionsRefInSelect('lv',$stats_lv2,'LV2')."</SELECT>\n";
$inputEf1="<SELECT NAME='stats_ef1' class='fondo'>".formOptionsRefInSelect('ef',$stats_ef1,'EF1')."</SELECT>\n";
$inputEf2="<SELECT NAME='stats_ef2' class='fondo'>".formOptionsRefInSelect('ef',$stats_ef2,'EF2')."</SELECT>\n";
$inputEps="<SELECT NAME='stats_eps' class='fondo'>".formOptionsRefInSelect('eps',$stats_eps,'EPS')."</SELECT>\n";
echo "<form name='form_stats' method='POST' action='".generer_url_ecrire('odb_statistiques')."' class='forml spip_xx-small'>\n";
echo "Crit&egrave;res sur les candidats &agrave; compter\n"
   . "<FIELDSET>\n<LEGEND>Choix recommand&eacute;s</LEGEND>\n"
   . "<table border=0>\n"
   . "<tr><th>de l'ann&eacute;e</th><th>$inputAnnee</th></tr>\n"
   . "<tr><th>qui se nomment</th><th>$inputNom</th></tr>\n"
   . "<tr><th>issus de série</th><th>$inputSerie</th></tr>\n"
   . "<tr><th>et du d&eacute;partement</th><th>$inputDepartement</th></tr>\n"
   . "<tr><th>et de l'&eacute;tablissement</th><th>$inputEtablissement</th></tr>\n"
   . "</table>\n</FIELDSET>\n"
   . "<FIELDSET>\n<LEGEND>Autres choix</LEGEND>\n"
   . "<table border=0 width='100%'>\n"
   . "<tr><td>Centre</td><td>$inputCentre</td></tr>\n"
   . "<tr><td>Langues</td><td>$inputLv1 $inputLv2</td></tr>\n"
   . "<tr><td>&Eacute;preuves facultatives</td><td>$inputEf1 $inputEf2</td></tr>\n"
   . "<tr><td>Sport</td><td>$inputEps</td></tr>\n"
   . "</table>\n</FIELDSET>\n"
   ;
echo "<INPUT style='font-weight:bold;' TYPE=SUBMIT name='step2' CLASS='forml' VALUE='Compter ces candidats'/>";
echo "<INPUT TYPE=hidden name='etablissement_txt' VALUE='$etablissement_txt'/>";
echo "<INPUT TYPE=hidden name='centre_txt' VALUE='$centre_txt'/>";
echo "</form>\n";
fin_boite_info();
fin_cadre_relief();
fin_page();
exit;
}
?>


