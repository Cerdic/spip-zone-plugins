<?php
session_start();
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN."inc-odb.php");

setlocale(LC_TIME, "fr_FR");

global $debug, $txt_gauche, $txt_debug;
$debug=false;

$txt_gauche="";// texte boite de gauche
$txt_="";// texte boite de droite
$txt_debug=""; // texte debug
define('OK',"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define('KO',"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

define('STYLE_MAX',"style='color:#2a2;'");
define('STYLE_MIN',"style='color:#a22;'");

// exécuté automatiquement par le plugin au chargement de la page ?exec=odb_consultations
function exec_odb_suivi() {
global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping;

include_once(DIR_ODB_COMMUN."inc-referentiel.php");
include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");
$annee=date("Y");


debut_page(_T("Suivi des saisies $annee"), "", "");
//echo "<br /><br />";
gros_titre(_T('Office Du Baccalaur&eacute;at'));
$tab_auteur=$GLOBALS["auteur_session"];

if($tab_auteur['statut']=="0minirezo") {
   $isAdmin=true;
} 

debut_cadre_relief( "", false, "", $titre = _T('Suivi des saisies et des modifications'));

debut_gauche();
	echo "<IMG SRC='"._DIR_PLUGIN_ODB_SUIVI."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'/><br/>\n";
   debut_boite_info();
      echo "<b>Suivi des saisies $annee</b>";
   fin_boite_info();
   odb_raccourcis('');
creer_colonne_droite();
debut_droite();
   debut_cadre_relief("", false, "", $titre = _T("Suivi des saisies $annee"));

isAutorise(array('Admin','Encadrant'));

echo "\n<!-- ======================[ Graphes ]======================== -->\n";

$tMois=array('','Janvier','F&eacute;vrier','Mars','Avril','Mai','Juin','Juillet','Ao&ucirc;t','Septembre','Octobre','Novembre','D&eacute;cembre');

$sql="SELECT dayofmonth(maj) as jour, month(maj) as mois, count(*) as nombre\n"
		."FROM odb_candidats\n"
		."WHERE annee=$annee\n"
		."GROUP BY mois, jour\n"
		."ORDER BY maj asc"
		;
		//echo $sql;

$result = mysql_query($sql) or die(mysql_error()); 
while ($row=mysql_fetch_array($result)){
	foreach (array('jour','mois','nombre') as $col)
		$$col=$row[$col];
	$mois=str_pad($mois,2,'0',STR_PAD_LEFT);
	$tValues[$mois]["$jour"]=$nombre;
}
foreach($tValues as $mois=>$t) {
	$sMois=$tMois[(int)$mois];
	$cle="$annee-$mois";
	$_SESSION[$cle]['hauteur']=300;
	$_SESSION[$cle]['largeur']=500;
	$_SESSION[$cle]['titre']="Suivi des saisies - $sMois $annee";
	$_SESSION[$cle]['titreX']="Jour ($sMois)";
	$_SESSION[$cle]['titreY']="Nombre de saisies total";

	$_SESSION[$cle]['valeurs']=array_values($t);
	$_SESSION[$cle]['labelX']=array_keys($t);
	echo "<br/><img src='".DIR_ODB_COMMUN."inc-graphe.php?graphe=$cle' "
		. "title='Suivi quotidien des saisies $sMois $annee' alt='Graphe suivi des saisies $sMois $annee'/>";
}

if(isAdmin()) {
	echo "<br/><br/>\n<!-- ======================[ Suppressions, modifs ]======================== -->\n";

	$sql = "SELECT login, count(*) AS nombre\n"
				."FROM odb_candidats\n"
				."WHERE annee=$annee\n" 
				."GROUP BY login\n"
				."ORDER BY nombre"
				;
	$result = odb_query($sql,__FILE__,__LINE__);
	$max=0;
	$min=9999999;
	$total=0;
	while ($row = mysql_fetch_array($result)) {
		foreach (array('login','nombre') as $col)
			$$col=$row[$col];
		
		$tSuivi[$login]['saisis']=$nombre;
		if($nombre<=$min) $min=$nombre;
		if($nombre>=$max) $max=$nombre;
		$total+=$nombre;
	}

	$sql = "SELECT can.login, count(*) AS nombre\n"
				."FROM odb_candidats can, odb_histo_candidats hist\n" 
				."WHERE can.annee=$annee AND hist.annee=$annee AND can.id_saisie=hist.id_saisie\n"
				."GROUP BY can.login"
				;
	$result = odb_query($sql,__FILE__,__LINE__);
	while ($row = mysql_fetch_array($result)) {
		foreach (array('login','nombre') as $col)
			$$col=$row[$col];
		$tSuivi[$login]['modifies']=$nombre;
	}
	//echo '<pre>';print_r($tSuivi);echo'</pre>';
	//asort($tSuivi);
	$thead[]="<th>Login</th><th>Saisies</th><th>Modifi&eacute;s</th><th>Taux de modifications</th>";
	foreach($tSuivi as $login=>$t) {
		$login=ucwords($login);
		$nbSaisis=(int)$t['saisis'];
		$nbModif=$t['modifies'];
		$tauxModif=round($nbModif/$nbSaisis,2);
		if($nbSaisis==$min) {
			$style=STYLE_MIN;
			$td='th';
		} elseif($nbSaisis==$max) {
			$style=STYLE_MAX;
			$td='th';
		} else {
			$style='';
			$td='td';
		}
		$tbody[]="<$td $style>$login</td><$td $style>$nbSaisis</td><$td $style>$nbModif</td><$td $style>".afficheTaux($tauxModif,10)." ".($tauxModif*100)."%</td>";
	}

	echo odb_html_table("Suivi g&eacute;r&eacute;ral des $total saisies $annee",$tbody,$thead,'vignette-24.png');

	echo "<br/>\n<!-- ======================[ Details quotidiens ]======================== -->\n";

	$thead=array();$tbody=array();
	$thead[]="<th>Jour</th><th>Mois</th><th>Login</th><th>Saisies</th>";

	$sql="SELECT dayofmonth(maj) as jour, month(maj) as mois, login, count(login) as nombre\n"
			."FROM odb_candidats\n"
			."WHERE annee=$annee\n"
			."GROUP BY login, mois, jour\n"
			."ORDER BY maj desc, nombre desc"
			;
	$result = mysql_query($sql) or die(mysql_error()); 

	while ($row=mysql_fetch_array($result)){
		foreach (array('jour','mois','login','nombre') as $col)
			$$col=$row[$col];
		$login=ucwords($login);
		$sMois=$tMois[$mois];

		// on change de mois
		if($mois==$mois_old) {
			$isNouveauMois=false;
		} else {
			$tbody[]="<th colspan=4><center><hr size=0/>$sMois<hr size=0/></center></th>";
			$isNouveauMois=true;
		}
		
		// on change de jour
		if($jour==$jour_old) {
			$sMois_aff='';
			$jour_aff='';
			$isNouveauJour=false;
		} else {
			if($login_max!='') {
				if($max>1) $smax='s';else $smax='';
				if($min>1) $smin='s';else $smin='';
				$tbody[]="<td ".STYLE_MAX.">Plus rapide</td><th ".STYLE_MAX.">$login_max ($max saisie$smax)</th><td ".STYLE_MIN.">Moins rapide</td><th ".STYLE_MIN.">$login_min ($min saisie$smin)</th>";
				$tbody[]="<th colspan=4><center><hr size=0/></center></th>";
				//FIXME pourquoi ca ne s'affiche pas le premier jour du mois ? 
			}
			$max=0;
			$min=9999;
			$jour_aff=$jour;
			$sMois_aff=$sMois;
			$isNouveauJour=true;
		}
		
		if($nombre>=$max) {$max=$nombre;$login_max=$login;}
		if($nombre<=$min) {$min=$nombre;$login_min=$login;}
		$tbody[]="<td>$jour_aff</td><td>$sMois_aff</td><td>$login</td><td>$nombre</td>";
		$jour_old=$jour;
		$mois_old=$mois;
	}

	echo odb_html_table("Suivi quotidien des saisies $annee",$tbody,$thead,'vignette-24.png');
}
fin_page();
exit;
}
?>
