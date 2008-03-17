<?php
session_start();
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN."inc-referentiel.php");

setlocale(LC_TIME, "fr_FR");

global $debug;
$debug=false;

define(OK,"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define(KO,"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

// exécuté automatiquement par le plugin au chargement de la page ?exec=odb_repartition
function exec_odb_param() {
global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping;

include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");
$annee=date("Y");
echo "<SCRIPT SRC='".DIR_ODB_COMMUN."contrib/boxover/boxover.js'></SCRIPT>\n";
$imgInfo="<img src='".DIR_ODB_COMMUN."contrib/boxover/info.gif' style='vertical-align:middle'>";

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
         mysql_query($requete) or die (KO." - Erreur dans la requete $requete<br>".mysql_error());
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
$centre=isset($_REQUEST['centre'])?$_REQUEST['centre']:0;
$vider=isset($_REQUEST['vider'])?$_REQUEST['vider']:'';
$reset=isset($_REQUEST['reset'])?$_REQUEST['reset']:'';
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
$msg="Choisissez une action ci-dessous";

if($vider!='') {
   switch($vider) {
      case 'odb_repartition' :
         $msg=OK.' - Les num&eacute;ros de table ont &eacute;t&eacute; r&eacute;initialis&eacute;s';
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
         if($centre>0) $andCentre=" and id_etablissement=$etablissement";
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
   $sql="DELETE FROM $vider where annee=$annee $andSerie $andEtablissement $andCentre";
   //echo $sql;
   mysql_query($sql) or die(KO. - "Erreur dans la requete $sql<br/>".mysql_error());
   $nb_rows=mysql_affected_rows(); // on force nb de lignes affectées au nb de num table supprimes
   foreach($post_sql as $sql)
      mysql_query($sql) or die(KO. - "Erreur dans la requete $sql<br/>".mysql_error());
   
   $gauche="<hr size=1/>".ucfirst($vider);
}
if($reset!='') {
   switch($reset) {
      case 'departement' :
         $msg=OK.' - Les d&eacute;partements ont &eacute;t&eacute; r&eacute;initialis&eacute;s';
         $sql="update odb_candidats set departement=0 where annee=$annee";
         break;
   }
   mysql_query($sql) or die(KO." - Erreur dans la requete <b>$sql</b><br/>".mysql_error());
   $gauche="<hr size=1/>".ucfirst($reset);
}
if($action!='')
   $gauche="<hr size=1/>".ucfirst($action);

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
            case 'introspection' :
               $nbErreurs=odb_introspection($annee);
               if($nbErreurs>0)
                  $msg=KO." - SIOU a d&eacute;tect&eacute; <b>$nbErreurs</b> erreurs et les a signal&eacute;es, veuillez les corriger svp.";
               else
                  $msg=OK.' - R&eacute;f&eacute;rentiel SIOU introspect&eacute;';
               break;
            case 'anonymiser':
               $sql = 'SELECT max(right(id_table,4)) nb_can, id_etablissement'
                    . ' FROM odb_repartition o'
                    . ' group by id_etablissement order by id_etablissement'
                    ;
               $result=mysql_query($sql) or die (KO." - Erreur dans la requete $sql<br />".mysql_error());
               while($row=mysql_fetch_row()) {
                  $id_etablissement=$row['id_etablissement'];
                  $nbCan=$row['nb_can'];
                  $tEta[$id_etablissement]=$nbCan;
                  $nbImpairs=ceil($nbCan/2);
                  $nbPairs=floor($nbCan/2);
               }
               $msg=OK.' - Num&eacute;ros anonymes g&eacute;n&eacute;r&eacute;s';
               break;
         }
      }
      echo $msg;
   fin_cadre_relief();
echo "<br/>\n<!-- ================== Formulaire param ================= -->\n";
debut_boite_info();
echo "<form name='form_param' method='POST' action='".generer_url_ecrire('odb_param')."' class='forml spip_xx-small'>\n";
echo "<table border=0 cellspacing=0 cellpadding=1>\n";
echo "<tr class='tr_odd'>\n\t<th>Action</th><th>Description</th>\n</tr>\n";
$title="Introspection du r&eacute;f&eacute;rentiel";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='introspection' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/loupe.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\"/></td>"
   . "<td><small>L'introspection consiste &agrave; v&eacute;rifier l'int&eacute;grit&eacute; du r&eacute;f&eacute;rentiel.<br/>Par exemple, les d&eacute;partements &agrave; 0 sont corrig&eacute;s (ou signal&eacute;s en cas de conflit entre ville et &eacute;tablissement), les champs invalides et les doublons sont signal&eacute;s.</small></td>\n"
   . "</tr>\n"
   ;
$title="R&eacute;initialiser les d&eacute;partements";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='reset' value='departement' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/effacer.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\" onclick=\"return confirm('Etes-vous certain(e) de vouloir reinitialiser les departements ?');\"/></td>"
   . "<td><small>Cette action remet les <b>d&eacute;partements des candidats</b> &agrave; 0.<br/>Si vous pensez que le d&eacute;partement est mal affect&eacute; pour certains candidats, r&eacute;initialisez les d&eacute;partements <b>puis lancez une introspection</b></small></td>\n"
   . "</tr>\n"
   ;
$title="R&eacute;initialiser les num&eacute;ros de table";
$inputSerie="<SELECT NAME='serie' class='fondo' onChange=\"if(this.value>0) laSerie=' en serie '+this.options[selectedIndex].text; else laSerie='';\" onLoad=\"laSerie='';\">".formOptionsRefInSelect('serie',0)."</SELECT>\n";
$inputCentre="<SELECT NAME='centre' class='fondo' onChange=\"if(this.value>0) leCentre='\\n qui composaient dans le centre '+this.options[selectedIndex].text; else leCentre='';\" onLoad=\"lCentre='';\">".formOptionsRefInSelect('centres',0)."</SELECT>\n";
$inputEtablissement="<SELECT NAME='etablissement' class='fondo' onChange=\"if(this.value>0) lEta='\\nqui proviennent de l\\'etablissement '+this.options[selectedIndex].text; else lEta='';\" onLoad=\"lEta='';\">".formOptionsRefInSelect('etablissement',0)."</SELECT>\n";
echo "<tr class=\"tr_liste\">\n\t<td align='center'><input type='image' name='vider' value='odb_repartition' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/effacer.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\" onclick=\"if(document.forms['form_param'].serie.value==0) laSerie=' toutes series confondues';if(document.forms['form_param'].centre.value==0) leCentre='\\nquel que soit le centre de composition';if(document.forms['form_param'].etablissement.value==0) lEta='\\nquel que soit l\\'etablissement d\\'origine';return confirm('Etes-vous certain(e) de vouloir reinitialiser les numeros de table\\ndes candidats de $annee'+laSerie+leCentre+lEta+' ?');\"/></td>"
   . "<td><small>Cette action vide les num&eacute;ros de table pour l'ann&eacute;e $annee.<br/>Vous pouvez sp&eacute;cifier une s&eacute;rie : $inputSerie<br/> et/ou un centre : $inputCentre<br/> et/ou un &eacute;tablissement : $inputEtablissement</small></td>\n"
   . "</tr>\n"
   ;
$title="G&eacute;n&eacute;rer les num&eacute;ros anonymes";
echo "<tr class='tr_liste'>\n\t<td align='center'><input type='image' name='action' value='anonymiser' src='"._DIR_PLUGIN_ODB_PARAM."img_pack/anonymer.png' alt='$title' title=\"header=[$imgInfo Param&eacute;trage SIOU] body=[$title]\"/></td>"
   . "<td><small>$title</small></td>\n"
   . "</tr>\n"
   ;
echo "</table>\n</form>\n";
fin_boite_info();
fin_cadre_relief();
fin_page();
exit;
}
?>


