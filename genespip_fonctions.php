<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

// Boucles SPIP-listes
global $tables_principales;

//Ensuite, donner le format des tables ajoutées. Par exemple :
$tables_principales['spip_genespip_individu']= array(
 'field' => array(
                         "id_individu" => "int(11) NOT NULL auto_increment",
                         "nom" => "text NOT NULL",
                         "prenom" => "text",
                         "sexe" => "int(11) NOT NULL default '0'",
                         "metier" => "longtext",
                         "pere" => "int(11) NOT NULL default '0'",
                         "mere" => "int(11) NOT NULL default '0'",
                         "enfant" => "int(11) NOT NULL default '0'",
                         "note" => "longtext NOT NULL",
                         "proprio" => "int(11) NOT NULL default '0'",
                         "portrait" => "int(11) default '0'",
                         "format_portrait" => "text",
                         "id_auteur" => "int(3) default NULL",
                         "source" => "text",
                         "adresse" => "text",
                         "signature" => "int(11) default NULL",
                         "format_signature" => "text",
                         "date_update"  => "datetime NOT NULL default '0000-00-00 00:00:00'",
                         "poubelle" => "int(1) NOT NULL default '0'",
                         "limitation" => "int(3) default NULL"
                    ),
 'key' => array("PRIMARY KEY" => "id_individu"));

$tables_principales['spip_genespip_documents'] = array(
 'field' => array(
                         "id_documents" => "int(11) NOT NULL auto_increment",
                         "id_individu" => "int(11) NOT NULL default '0'",
                         "id_article" => "int(11) NOT NULL default '0'",
                 ),
 'key' => array("PRIMARY KEY" => "id_documents"));

$tables_principales['spip_genespip_liste'] = array(
 'field' => array(
                         "id_liste" => "int(11) NOT NULL auto_increment",
                         "nom" => "text NOT NULL",
                         "nombre" => "int(11) NOT NULL",
                         "date_couverte" => "TINYTEXT NOT NULL",
                         "date_update"  => "date NOT NULL",
                 ),
 'key' => array("PRIMARY KEY" => "id_liste"));

$tables_principales['spip_genespip_parametres'] = array(
 'field' => array(
                         "id_parametres" => "int(11) NOT NULL auto_increment",
                         "date_init"  => "datetime NOT NULL",
                         "rubrique" => "int(11) NOT NULL",
                         "theme" => "TINYTEXT NOT NULL",
                         "pub" => "int(11) NOT NULL",
                         "multilingue" => "INT(3) NOT NULL default '0'",
                         "acces" => "INT(3) NOT NULL default '3'",
                         "centans" => "INT(3) NOT NULL default '0'",
                 ),
 'key' => array("PRIMARY KEY" => "id_parametres"));

$tables_principales['spip_genespip_lieux'] = array(
 'field' => array(
                         "id_lieu" => "int(11) NOT NULL auto_increment",
                         "ville" => "text NOT NULL",
                         "code_departement" => "int(11) NOT NULL",
                         "departement" => "text NOT NULL",
                         "region" => "text NOT NULL",
                         "pays" => "text NOT NULL"
                 ),
 'key' => array("PRIMARY KEY" => "id_lieu"));

$tables_principales['spip_genespip_evenements'] = array(
 'field' => array(
                          "id_evenement" => "int(11) NOT NULL auto_increment",
                          "id_individu" => "int(11) NOT NULL",
                          "id_type_evenement" => "int(11) NOT NULL",
                          "date_evenement" => "date NOT NULL",
                          "precision_date" => "text NOT NULL",
                          "id_lieu" => "int(11) NOT NULL DEFAULT '1'",
                          "id_epoux" => "int(11) NOT NULL",
                          "date_update"  => "datetime NOT NULL"
                 ),
 'key' => array("PRIMARY KEY" => "id_evenement"));

$tables_principales['spip_genespip_type_evenements'] = array(
 'field' => array(
                          "id_type_evenement" => "INT NOT NULL auto_increment",
                          "type_evenement" => "TEXT NOT NULL",
                          "clair_evenement" => "TEXT NOT NULL"
                 ),
 'key' => array("PRIMARY KEY" => "id_type_evenement"));

$tables_principales['spip_genespip_journal'] = array(
 'field' => array(
                          "id_journal" => "INT NOT NULL auto_increment",
                          "action" => "TINYTEXT NOT NULL",
                          "descriptif" => "TEXT NOT NULL",
                          "id_individu" => "INT NOT NULL",
                          "id_auteur" => "INT NOT NULL",
                          "date_update"  => "datetime NOT NULL"
                  ),
 'key' => array("PRIMARY KEY" => "id_journal"));


//$tables_jointures['spip_genespip_evenements']['id_individu'] = 'id_individu';
//$tables_jointures['spip_genespip_individu']['id_individu'] = 'id_individu';

//
// <BOUCLE(INDIVIDU)>
//
function boucle_GENESPIP_INDIVIDU_dist($id_boucle, &$boucles) {
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $boucle->from[$id_table] =  "spip_genespip_individu";
            return calculer_boucle($id_boucle, $boucles); 
}

//
// <BOUCLE(LISTE)>
//
function boucle_GENESPIP_LISTE_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_genespip_liste";
        return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(INDIVIDU)>
//
function boucle_GENESPIP_DOCUMENTS_dist($id_boucle, &$boucles) {
            $boucle = &$boucles[$id_boucle];
            $id_table = $boucle->id_table;
            $boucle->from[$id_table] =  "spip_genespip_documents";
            return calculer_boucle($id_boucle, $boucles); 
}

//
// <BOUCLE(EVENEMENTS)>
//
function boucle_GENESPIP_EVENEMENTS_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_genespip_evenements";
        return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(LIEUX)>
//
function boucle_GENESPIP_LIEUX_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_genespip_lieux";
        return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(JOURNAL)>
//
function boucle_GENESPIP_JOURNAL_dist($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_genespip_journal";
        return calculer_boucle($id_boucle, $boucles);
}

function genespip_header_prive($flux){
    $flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('genespip.css')).'" />';
        return $flux;
}
function genespip_rediriger_javascript($url) {
    echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
    exit();
 }

//Conversion de date français
function genespip_datefr($date) {
    $split = split('-',$date); 
    $annee = $split[0];
    $mois = $split[1];
    $jour = $split[2];
    if ($annee==NULL){$annee='0000';}
    if ($mois==NULL){$mois='00';}
    if ($jour==NULL){$jour='00';}
	return $jour.'/'.$mois.'/'.$annee;
} 
//Conversion de date US
function genespip_dateus($date) {
    $split = split('/',$date);
    $jour = $split[0];
    $mois = $split[1]; 
    $annee = $split[2];
    if ($annee==NULL){$annee='0000';}
    if ($mois==NULL){$mois='00';}
    if ($jour==NULL){$jour='00';}
return $annee.'-'.$mois.'-'.$jour;
}

//*******************THEME du SITE*******************************
function genespip_modif_theme($theme, $pub, $multilingue, $acces, $centans) {
$update_theme = "UPDATE spip_genespip_parametres SET theme = '".$theme."', pub = ".$pub.", multilingue = ".$multilingue.", acces = ".$acces.", centans = ".$centans;
$update_theme = spip_query($update_theme);
}
//***************************************************************


//********************MAJ table spip_genespip_liste**************
function genespip_maj_liste() {
set_time_limit(0);
echo "<br /><u>Mise &agrave; jour de la liste &eacute;clair</u>";
$date_update=date("Y-m-d");
$result_individu = spip_query("SELECT id_individu, nom, count(id_individu) as comptenom FROM spip_genespip_individu where poubelle<>1 group by nom");
        while ($indi = spip_fetch_array($result_individu)) {
           $result_date_min = spip_query("SELECT date_evenement FROM spip_genespip_individu,spip_genespip_evenements where spip_genespip_individu.id_individu=spip_genespip_evenements.id_individu and nom = '".$indi['nom']."' and id_type_evenement='1' and date_evenement <> 0000-00-00 ORDER BY date_evenement ASC limit 0,1" );
           if (mysql_num_rows($result_date_min)!=0){
             while ($min = spip_fetch_array($result_date_min)) {
             $split = split('-',$min['date_evenement']);
             $date_min=$split[0];
             }
           }else{$date_min="?";}
           $result_date_max = spip_query("SELECT date_evenement FROM spip_genespip_individu,spip_genespip_evenements where spip_genespip_individu.id_individu=spip_genespip_evenements.id_individu and nom = '".$indi['nom']."' and id_type_evenement='1' and date_evenement <> 0000-00-00 ORDER BY date_evenement DESC limit 0,1" );
           if (mysql_num_rows($result_date_max)!=0){
             while ($max = spip_fetch_array($result_date_max)) {
             $split = split('-',$max['date_evenement']);
             $date_couverte=$date_min."-".$split[0];
             }
           }else{$date_couverte=$date_min."-?";}
        $result_liste = "SELECT * FROM spip_genespip_liste where nom = '".$indi['nom']."'";
        $result_liste = spip_query($result_liste);
        /*echo mysql_num_rows($result_liste);*/
              if (mysql_num_rows($result_liste)==0){
                 $insert_liste = 'INSERT INTO spip_genespip_liste (nom, nombre, date_couverte, date_update) VALUES ("'.$indi["nom"].'", '.$indi["comptenom"].', "'.$date_couverte.'", "'.$date_update.'")';
                 /*echo $insert_liste."<br />";*/
                 $insert_liste = spip_query($insert_liste) or die ("Requête insert_liste invalide<br />");
              }else{
              while ($liste = spip_fetch_array($result_liste)) {
              if ($liste['nombre']!=$indi['comptenom']){
                 $update_liste = "UPDATE spip_genespip_liste SET nombre = ".$indi['comptenom'].", date_couverte= '".$date_couverte."', date_update= '".$date_update."' WHERE nom = '".$indi['nom']."'";
                 /*echo $update_liste."<br />";*/
                 $update_liste = spip_query($update_liste) or die ("Requête update_liste invalide<br />");
              }
              }
              }
        }
$result_liste_inverse = spip_query("SELECT nom FROM spip_genespip_liste");
        while ($liste_inv = spip_fetch_array($result_liste_inverse)) {
        $result_individu_inverse = spip_query("SELECT nom FROM spip_genespip_individu where poubelle<>1 and nom = '".$liste_inv['nom']."'");
               if (mysql_num_rows($result_individu_inverse)==0){
                 $delete_liste=mysql_query('DELETE FROM spip_genespip_liste WHERE nom = "'.$liste_inv["nom"].'"') or die ("Requ&ecirc;te delete_liste invalide");
               }
        }
echo "&nbsp;<font color='red'>OK</font><br />";
}
// ##Définition du journal##
// 1 -> Nouvel individu
// 2 -> Modification information individu
// 3 -> Modification évènement
// 4 -> Suppression évènement
// 5 -> Ajout évènement
// 6 -> Ajout portrait
// 7 -> Ajout signature
// 8 -> suppression individu

//********************Gestion des fiches**************
//Création fiche

function genespip_ajout_fiche() {
	$date_update=date("Y-m-d H:i:s");
	$date_update2=date("Y-m-d");
	$sexe=$_POST['sexe'].$_GET['sexe'];
	$nom=$_POST['nom'].$_GET['nom'];
	$prenom=$_POST['prenom'].$_GET['prenom'];
	$insert_fiche="INSERT INTO spip_genespip_individu (nom ,prenom, sexe, id_auteur, date_update) VALUES ('".addslashes($nom)."', '".addslashes($prenom)."', '".$sexe."', ".$GLOBALS['connect_id_auteur'].",'".$date_update."')";
	$insert_fiche = spip_query($insert_fiche);
	$id_individu=mysql_insert_id();
	// ### Journal ###
	$insert_journal="INSERT INTO spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update) VALUES ('creation fiche', '1', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
	$sqlJOURNAL =spip_query($insert_journal) or die ("Requete JOURNAL invalide");
	genespip_maj_liste();
	return $id_individu;
}

//Modification d'une fiche - modif (22-03-2008)
  //Données générales
function genespip_modif_fiche($id_individu) {
$date_update=date("Y-m-d H:i:s");
$naissance=genespip_dateus($_POST['naissance']);
$deces=genespip_dateus($_POST['deces']);
$action_sql = "UPDATE spip_genespip_individu SET nom = '".addslashes($_POST['nom'])."', prenom = '".addslashes($_POST['prenom'])."', sexe ='".$_POST['sexe']."', metier = '".addslashes($_POST['metier'])."', enfant = '".$_POST['enfant']."', note = '".addslashes($_POST['note'])."', portrait='".$_POST['portrait']."', source= '".addslashes($_POST['source'])."', adresse= '".addslashes($_POST['adresse'])."', date_update= '".$date_update."', limitation= '".$_POST['limitation']."' WHERE id_individu = ".$id_individu;
$sqlmodif =spip_query($action_sql) or die ("Requete update_fiche invalide");
// ### Journal ###
$insert_journal="INSERT INTO spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update) VALUES ('modification fiche', '2', '".$id_individu."', '".$GLOBALS['connect_id_auteur']."', '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die ("Requete JOURNAL invalide");

}
  //Modif Evenement  - create (22-03-2008)
function genespip_up_evt($id_individu,$id_type_evenement) {
$date_update=date("Y-m-d H:i:s");
$date_evenement=genespip_dateus($_POST['date_evenement']);
$action_sql = "UPDATE spip_genespip_evenements SET date_evenement ='".$date_evenement."', precision_date = '".$_POST['precision_date']."', id_lieu = '".$_POST['id_lieu']."', id_epoux = '".$_POST['id_epoux']."', date_update= '".$date_update."' where id_type_evenement = ".$id_type_evenement." and id_epoux = '".$_POST['id_epoux']."' and id_individu = ".$id_individu;
$sqlup =spip_query($action_sql) or die ("Requete update_evenement invalide");
if ($_POST['id_epoux']<>NULL){
$action_sql = "UPDATE spip_genespip_evenements SET date_evenement ='".$date_evenement."', precision_date = '".$_POST['precision_date']."', id_lieu = '".$_POST['id_lieu']."', id_epoux = '".$id_individu."', date_update= '".$date_update."' where id_type_evenement = ".$id_type_evenement." and id_epoux = ".$id_individu." and id_individu = ".$_POST['id_epoux'];
$sqlup =spip_query($action_sql) or die ("Requete update_evenement_epoux invalide");
}
// ### Journal ###
$insert_journal="INSERT INTO spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update) VALUES ('modification evenement', '3', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die ("Requete JOURNAL invalide");
}
  //Supp Evenement  - create (22-03-2008)
function genespip_del_evt($id_evenement) {
$date_update=date("Y-m-d H:i:s");
$action_sql = "DELETE FROM spip_genespip_evenements WHERE id_evenement = ".$id_evenement;
$sqldel =spip_query($action_sql) or die ("Requete delete_evenement invalide");
// ### Journal ###
$insert_journal="INSERT INTO spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update) VALUES ('suppression evenement', '4', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die ("Requete JOURNAL invalide");
}
  //Ajout Evenement  - create (22-03-2008)
function genespip_add_evt($id_individu) {
	$date_update=date("Y-m-d H:i:s");
	$date_evenement=genespip_dateus($_POST['date_evenement']);
	$action_sql="INSERT INTO spip_genespip_evenements (id_individu, id_type_evenement, date_evenement ,precision_date, id_lieu, id_epoux, date_update) VALUES (".$id_individu.", ".$_POST['id_type_evenement'].", '".$date_evenement."', '".$_POST['precision_date']."', '".$_POST['id_lieu']."','".$_POST['id_epoux']."', '".$date_update."')";
	$sqladd =spip_query($action_sql) or die ("Requete ajout_evenement invalide");
	if ($_POST['id_epoux']<>NULL){
		$action_sql="INSERT INTO spip_genespip_evenements (id_individu, id_type_evenement, date_evenement ,precision_date, id_lieu, id_epoux, date_update) VALUES (".$_POST['id_epoux'].", ".$_POST['id_type_evenement'].", '".$date_evenement."', '".$_POST['precision_date']."', '".$_POST['id_lieu']."','".$id_individu."', '".$date_update."')";
		$sqladd =spip_query($action_sql) or die ("Requete ajout_evenement_epoux invalide");
	}
	// ### Journal ###
	$insert_journal="INSERT INTO spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update) VALUES ('creation evenement', '5', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
	$sqlJOURNAL =spip_query($insert_journal) or die ("Requete JOURNAL invalide");
}

//Modification d'une fiche - Ajout indicateur portrait
function genespip_modif_fiche_portrait($portrait,$id_individu,$format_portrait) {
$date_update=date("Y-m-d H:i:s");
$action_sql="UPDATE spip_genespip_individu SET portrait = ".$portrait.", format_portrait = '".$format_portrait."', date_update= '".$date_update."' WHERE id_individu = ".$id_individu;
$sqlmodif =spip_query($action_sql) or die ("Requête update_portrait invalide");
// ### Journal ###
if ($portrait==1){
$insert_journal="INSERT INTO spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update) VALUES ('ajout portrait', '6', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die ("Requete JOURNAL invalide");
}
}
//Modification d'une fiche - Ajout indicateur signature
function genespip_modif_fiche_signature($signature,$id_individu,$format_signature) {
$date_update=date("Y-m-d H:i:s");
$action_sql="UPDATE spip_genespip_individu SET signature = ".$signature.", format_signature = '".$format_signature."', date_update= '".$date_update."' WHERE id_individu = ".$id_individu;
$sqlmodif =spip_query($action_sql) or die ("Requête update_signature invalide");
// ### Journal ###
if ($signature==1){
$insert_journal="INSERT INTO spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update) VALUES ('ajout signature', '7', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die ("Requete JOURNAL invalide");
}
}

//Modification des parents
function genespip_modif_parent($id_individu) {
$date_update=date("Y-m-d H:i:s");
$action_sql="UPDATE spip_genespip_individu SET pere = '".$_POST['pere']."', mere = '".$_POST['mere']."', date_update= '".$date_update."' WHERE id_individu = ".$id_individu;
$sqlmodif =spip_query($action_sql) or die ("Requête update_parent invalide");
}
//***************Lieu***********************
  //Modif Lieu  - create (25-03-2008)
function genespip_up_lieu($id_lieu) {
$action_sql = "UPDATE spip_genespip_lieux SET ville ='".addslashes($_POST['ville'])."', departement = '".addslashes($_POST['departement'])."', code_departement = '".$_POST['code_departement']."', region = '".addslashes($_POST['region'])."', pays= '".addslashes($_POST['pays'])."' where id_lieu = ".$id_lieu;
$sqlup =spip_query($action_sql) or die ("Requete update_lieu invalide");
}
  //Delete Lieu  - create (25-03-2008)
function genespip_del_lieu($id_lieu) {
$action_sql = "DELETE FROM spip_genespip_lieux WHERE id_lieu = ".$id_lieu;
$sqldel =spip_query($action_sql) or die ("Requete delete_lieu invalide");
}
  //Add Lieu  - create (25-03-2008)
function genespip_add_lieu() {
$action_sql="INSERT INTO spip_genespip_lieux (ville, departement, code_departement , region, pays) VALUES ('".addslashes($_POST['ville'])."', '".addslashes($_POST['departement'])."', '".$_POST['code_departement']."', '".addslashes($_POST['region'])."', '".addslashes($_POST['pays'])."')";
$sqladd =spip_query($action_sql) or die ("Requete ajout_lieu invalide");
}
//***************Corbeille***********************
//Mise à la corbeille (en attente) d'une fiche
function genespip_poubelle_fiche($id_individu) {
$date_update=date("Y-m-d H:i:s");
$action_sql="UPDATE spip_genespip_individu SET poubelle = '".$_POST['poubelle']."' WHERE id_individu = ".$id_individu;
$sqlmodif =spip_query($action_sql) or die ("Requête update_poubelle invalide");
echo "<font color='red'>".$date_update." : La fiche N&ordm;".$id_individu." a &eacute;t&eacute; mise &agrave; la poubelle.</font>";
genespip_maj_liste();
// ### Journal ###
$insert_journal="INSERT INTO spip_genespip_journal (action, descriptif, id_individu, id_auteur, date_update) VALUES ('suppression fiche', '8', '".$id_individu."', ".$GLOBALS['connect_id_auteur'].", '".$date_update."')";
$sqlJOURNAL =spip_query($insert_journal) or die ("Requete JOURNAL invalide");

}
function genespip_supp_fiche($action) {
if ($action=="Supprimer"){
//****Suppression definitive
$valeur=$_POST['action_fiche'];
$nmax=count($valeur);
for($i=0;$i!=$nmax;$i++)
   {
$sqldel=mysql_query("DELETE FROM spip_genespip_individu WHERE id_individu = ".$valeur[$i]) or die ("Requête delete_fiche invalide");
        echo "<font color='red'>Fiche n&ordm;".$valeur[$i]."</font><br />";
$sqldel=mysql_query("DELETE FROM spip_genespip_evenements WHERE id_individu = ".$valeur[$i]) or die ("Requête delete_union2 invalide");
$sqldel=mysql_query("DELETE FROM spip_genespip_evenements WHERE id_epoux = ".$valeur[$i]) or die ("Requête delete_union3 invalide");
        echo "<font color='red'>Union de fiche n&ordm;".$valeur[$i]."</font><br />";
   }
}elseif ($action=="Restaurer"){
//****Restauration fiche
$valeur=$_POST['action_fiche'];
$nmax=count($valeur);
for($i=0;$i!=$nmax;$i++)
   {
$date_update=date("Y-m-d H:i:s");
$action_sql="UPDATE spip_genespip_individu SET poubelle = 0 WHERE id_individu = ".$valeur[$i];
$sqlmodif =spip_query($action_sql) or die ("Requête update invalide");
        echo "<font color='red'>Fiche n&ordm;".$valeur[$i]." restaur&eacute;e</font><br />";
   }
genespip_maj_liste();
}
}

//************************************************
function genespip_nom_prenom($id_individu,$choix){
if ($choix==1 or $choix==3){
$result = spip_query("SELECT id_individu, nom, prenom FROM spip_genespip_individu where id_individu = ".$id_individu);
}
elseif ($choix==2){
$result = spip_query("SELECT id_individu, nom, prenom FROM spip_genespip_individu where pere = ".$id_individu." or mere =".$id_individu);
}
$n=0;
while ($fiche = spip_fetch_array($result)) {
if ($n!=0){$detail .="<br />";}
if ($choix==3){$detail=$fiche['nom']." ".$fiche['prenom'];}
else {$detail .= "<a href=".generer_url_ecrire('fiche_detail')."&id_individu=".$fiche['id_individu'].">&raquo;&nbsp;".$fiche['nom']." ".$fiche['prenom']."</a>";}
$n=$n+1;
}
return $detail;
}
//**************************DOCUMENTS********************
//Ajout document
function genespip_ajout_document($id_individu, $id_article) {
$date_update=date("Y-m-d H:i:s");
$requete_insert="INSERT INTO spip_genespip_documents (id_individu, id_article) VALUES ('$id_individu', '$id_article')";
$insert_document = spip_query($requete_insert);
echo "<br /><font color='red'>".$date_update." : Nouvelle liaison &rdquo;document&ldquo; r&eacute;alis&eacute;e</font>";

$result = spip_query("SELECT * FROM spip_articles where id_article=".$id_article);
while ($fiche = spip_fetch_array($result)) {

if ($fiche['chapo']<>""){
if (get_magic_quotes_gpc()==0){
$chapo=addslashes($fiche['chapo'])."<br />";
}else{
$chapo=$fiche['chapo']."<br />";
}}
$chapo=$chapo."[Fiche de ".genespip_nom_prenom($id_individu,3)."->spip.php?page=individu&id_individu=".$id_individu."]";
$requete="UPDATE spip_articles SET chapo = '".$chapo."' where id_article=".$id_article;

$update_article = spip_query($requete) or die ("Requête ajout lien invalide");

}}
//Suppression lien document
function genespip_supp_document($id_individu, $id_article) {
$sqldel=spip_query("DELETE FROM spip_genespip_documents WHERE id_individu = ".$id_individu." and id_article = ".$id_article) or die ("Requête delete_documents invalide");
}
//Selection article
function genespip_choix_article(){
$result = spip_query("SELECT id_article, titre FROM spip_articles");
  $art .= "<select size='1' name='id_article' size='3'>";
  $art .= "<option value='0'>---</option>";
while ($fiche = spip_fetch_array($result)) {
  $art .= "<option value='".$fiche['id_article']."'>".$fiche['id_article']."/ ".$fiche['titre']."</option>";
}
  $art .= "</select>";
return $art;
}

//détail article sélectionner
function genespip_liste_document($id_individu){
$url_action_document=generer_url_ecrire('fiche_document');
$url_detail_document=generer_url_ecrire('articles');
$result = spip_query("SELECT spip_genespip_documents.id_individu, spip_articles.id_article, spip_articles.titre FROM spip_genespip_documents,spip_articles WHERE spip_genespip_documents.id_individu = ".$id_individu." and spip_genespip_documents.id_article=spip_articles.id_article");
  $art .= "<table width='100%'>";
while ($fiche = spip_fetch_array($result)) {
  $art .= "<tr><td><a href='".$url_detail_document."&id_individu=".$fiche['id_individu']."&id_article=".$fiche['id_article']."'>".$fiche['id_article']."/ ".$fiche['titre']."</a></td>";
  $art .= "<td><a href='".$url_action_document."&action=delete&id_individu=".$fiche['id_individu']."&id_article=".$fiche['id_article']."'><img border='0' noborder src='"._DIR_PLUGIN_GENESPIP."img_pack/del.gif' alt='Supprimer' /></a></td>";
}
  $art .= "</table>";
return $art;
}
//Tester présence lien article avant dans exec/articles et exec/article_edit
function genespip_tester_document($id_individu,$id_article,$page){
$url_action_document=generer_url_ecrire('fiche_document');
$affiche .= "toto";
if (isset($id_article)==NULL){
$affiche .= icone_horizontale(_T('&rsaquo;&rsaquo;&nbsp;Retour sur la fiche sans enregistrer&nbsp;&rsaquo;&rsaquo;'), $url_action_document."&id_individu=".$id_individu, 'rien.gif', '');
}else{
$result = spip_query("SELECT * FROM spip_genespip_documents WHERE id_individu = ".$id_individu." and id_article = ".$id_article);
$compte = mysql_num_rows($result);
if ($compte==0){
$affiche .= icone_horizontale(_T('&rsaquo;&rsaquo;&nbsp;Cliquer ici pour lier l&acute;article &agrave; la fiche&nbsp;&rsaquo;&rsaquo;'), $url_action_document."&id_individu=".$id_individu."&id_article=".$id_article."&action=Valider", 'rien.gif', 'creer.gif');
}else{
if ($page=="articles"){
$affiche .= icone_horizontale(_T('&rsaquo;&rsaquo;&nbsp;Retour sur la fiche&nbsp;&rsaquo;&rsaquo;'), $url_action_document."&id_individu=".$id_individu, 'rien.gif', '');
}
if ($page=="articles_edit"){
$affiche .= icone_horizontale(_T('&rsaquo;&rsaquo;&nbsp;Retour sur la fiche sans enregistrer&nbsp;&rsaquo;&rsaquo;'), $url_action_document."&id_individu=".$id_individu, 'rien.gif', '');
}}}
return $affiche;
}
//Verif rubrique documents
function genespip_creer_rubrique(){
$date_update=date("Y-m-d H:i:s");
$result = spip_query("SELECT spip_genespip_parametres.rubrique, spip_rubriques.id_rubrique, spip_rubriques.titre FROM spip_genespip_parametres, spip_rubriques WHERE spip_rubriques.id_rubrique = spip_genespip_parametres.rubrique");
$compte = mysql_num_rows($result);
if ($compte==0){
$insert_rubrique = spip_query("INSERT INTO spip_rubriques (titre, statut, date, idx, statut_tmp, date_tmp) VALUES ('Documents, actes', 'publie', '".$date_update."', 'oui', 'publie', '".$date_update."')");
$id_rubrique .=spip_insert_id();
$insert_rubrique = spip_query("UPDATE spip_genespip_parametres SET rubrique = '".$id_rubrique."'");
}else{
while ($fiche = spip_fetch_array($result)) {
$id_rubrique .=$fiche['id_rubrique'];
}}
return $id_rubrique;
}
//Nouvelle fiche formulaire - modif 23/05/2008 (ajout sexe)
function genespip_nouvelle_fiche($url_action_accueil){
	$ret .= "<a name='images'></a>";
	$ret .= debut_cadre_relief("petition-24.gif", true, "creer.gif", _T('genespip:nouvelle fiche'));
	$ret .= "<form action='".$url_action_accueil."' method='post'>";
	$ret .= "<table><tr><td>";
	$ret .= _T('genespip:nom').":</td><td><input type='text' name='nom' size='12' /></td></tr><tr><td>";
	$ret .= _T('genespip:prenom').":</td><td><input type='text' name='prenom' size='12' /></td></tr>";
	$ret .= "<tr><td colspan='2'>M&nbsp;<input type='radio' name='sexe' value='0' id='1' checked />";
	$ret .= "&nbsp;F&nbsp;<input type='radio' name='sexe' value='1' id='2' /></td></tr>";
	$ret .= "<tr><td colspan='2'><input type='submit' name='submit' value='Valider' size='8' /></td></tr></table>";
	$ret .= "<input type='hidden' name='edit' value='nouvellefiche' size='8' />";
	$ret .= "</form>";
	$ret .= fin_cadre_relief(true);
	return $ret;
}

// menu_lang plat sans URL sur la langue sélectionnée
function url_lang ($langues) {
    include_spip('inc/charsets');    
    $texte = '';
    $tab_langues = explode(',', $GLOBALS['meta']['langues_multilingue']);
    while ( list($clef, $valeur) = each($tab_langues) )
  if ($valeur == $GLOBALS['spip_lang']) {
    if ($valeur=="en"){$flag="gb";}else{$flag=$valeur;}
  $drapeau="<img src='"._DIR_PLUGIN_GENESPIP."img_pack/pays/".$flag.".png'>";
  $texte .= '<span style="border:1px solid #626262;background-color:#EAEAEA;padding:1px">'.$drapeau.'</span>';
  }
  else {
    if ($valeur=="en"){$flag="gb";}else{$flag=$valeur;}
  $drapeau="<img src='"._DIR_PLUGIN_GENESPIP."img_pack/pays/".$flag.".png'>";
  $texte .= '<span style="padding:1px">';
  $texte .= '<a href="'.parametre_url(generer_url_action('cookie'), 'url', parametre_url(self(true), '&'), '&').'&amp;var_lang='.$valeur.'" alt="'.traduire_nom_langue($valeur).'">'.$drapeau.'</a>';
  $texte .= '</span>';
  }
    return $texte;
}
//fin

include_spip('inc/genespip_balise');
?>
<script language="javascript" type="text/javascript">
function update_flag1(objet){
 if (objet.value)
  document.getElementById("img_flags1").src = '/genespip/plugins/genespip/img_pack/pays/'+objet.value+'.png';
}
function update_flag2(objet){
 if (objet.value)
  document.getElementById("img_flags2").src = '/genespip/plugins/genespip/img_pack/pays/'+objet.value+'.png';
}
</script>