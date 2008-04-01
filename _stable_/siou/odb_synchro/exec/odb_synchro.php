<?php
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/charsets');
define('DIR_ODB_COMMUN',_DIR_PLUGINS."odb/odb_commun/");
include_once(DIR_ODB_COMMUN.'inc-html.php');
include_once(DIR_ODB_COMMUN."inc-odb.php");

setlocale(LC_TIME, "fr_FR");

global $debug, $txt_gauche, $txt_debug;
$debug=false;

$txt_gauche="";// texte boite de gauche
$txt_="";// texte boite de droite
$txt_debug=""; // texte debug
define('OK',"<SPAN style='color:#3C3;font-weight:bold;'>[OK]</SPAN>");
define('KO',"<SPAN style='color:#C33;font-weight:bold;'>[KO]</SPAN>");

function sauvegarde_table_distant($user, $mdp, $base, $table){
	$LeFichier="sqldump.sql";
	if($table==""){
		echo "Le param&egrave;tre table est obligatoire ! ";
		exit;
	}
	$fp=fopen($LeFichier, "w+");
	$donnees=`mysqldump -u $user -p$mdp $base $table > $LeFichier`;
	if(!$fp) {
			  echo "Impossible d'&eacute;crire dans le fichier $LeFichier !";
			  exit;
	}
	$NbCar=fputs($fp,$donnees);
	fclose($fp);
	return $LeFichier;
}

// exécuté automatiquement par le plugin au chargement de la page ?exec=odb_sauvegarde
function exec_odb_synchro() {
	global $connect_statut, $connect_toutes_rubriques, $debug, $txt_gauche, $txt_droite, $txt_debug, $tab_referentiel, $odb_referentiel,$odb_mapping;

	include_once(DIR_ODB_COMMUN."inc-regles_gestion.php");
	$annee=date("Y");

	$base=getBddConf('bdd');				//Nom de la base de données
	$user=getBddConf('user');				
	$mdp=getBddConf('pass');	     

	if(isset($_POST['ftp'])) $host=$_POST['ftp'];
	else 	$host="192.168.1.65"; 			  									//Serveur ftp
	if(isset($_POST['login'])) $LoginDistant=$_POST['login'];		
	else $LoginDistant=ghislain;    											//Utilisateur de connexion ftp
	$MotDePasseDistant=$_POST['mdpftp'];									//Mot de passe ftp

	$ServeurLocal="localhost";           									//Serveur local contenant la base de données
	$LoginLocal=getBddConf('user');     									//Utilisateur de connexion à la base de données
	$MotDePasseLocal=getBddConf('pass');									//Mot de passe
	$BaseLocal=getBddConf('bdd');              							//Nom de la base de données

	debut_page(_T('Sauvegarde et Synchronisation des bases'), "", "");
	echo "<br /><br />";
	gros_titre(_T('Office Du Baccalaur&eacute;at'));
	
	
	if(!isset($_GET['type'])){
		debut_gauche();
		if(!isset($_POST['ftp'])){
		debut_cadre_relief( "", false, "", $titre = _T('Parametres'));
		$param=array();
		echo "<form name='parametre' action='".generer_url_ecrire('odb_synchro')."' method='post' class='forml spip_xx-small'>\n";
		$param[]="<td><b>Dossier SPIP: </b></td><td><input type='text' size=15 name='dossier'></td>";
		$param[]="<td><b>Serveur ftp: </b></td><td><input type='text' size=15 name='ftp' value='$host'></td>";
		$param[]="<td><b>Login ftp: </b></td><td><input type='text' size=15 name='login' value='ghislain'></td>";
		$param[]="<td><b>Mot de passe ftp: </b></td><td><input type='password' size=15 name='mdpftp'></td>";
		echo odb_html_table("Connexion FTP",$param,"",'historique-24.gif');
	
   	$sql = "SHOW TABLES FROM $base LIKE 'odb%'";
   	$result=odb_query($sql,__FILE__,__LINE__);
		debut_droite();
		debut_cadre_relief( "", false, "", $titre = _T('Synchronisation entre le serveur local et le serveur public'));
		echo "<IMG SRC='"._DIR_PLUGIN_ODB_SUIVI."/img_pack/logo_odb.png' alt='Office du bac' ALIGN='absmiddle'><br><br>\n";
					
		$tbody=array();
		$a=1;
		while ($row = mysql_fetch_array($result))  {
    		$table = $row[0];
   		$tbody[]="<td><b>$table </b></td><td><input type='checkbox' name='table$a' value=$table></td>";
			$a++;	
		}
		echo "<br><center>Veuillez cocher les tables a synchroniser dans la liste ci-dessous ! </center></br>";
		echo odb_html_table("Liste des tables ",$tbody,"",'historique-24.gif');
		echo "<center><INPUT type='SUBMIT' VALUE='Synchroniser les tables' class='fondo' onclick=\"if(document.forms['mdpbase'].value=='') {alert('Veuillez entrer le mot de passe de la base')}else{if(document.forms['mdpftp'].value=='') {alert('Veuillez entrer le mot de passe du serveur ftp')}};\"></center>";
		echo "</FORM>\n";
	
		if(isset($_POST['login'])){
			for($i=0; $i<$a; $i++){
				$nomTab="table".$i;
				if($_POST[$nomTab]!='') $tabTables[]=$_POST[$nomTab];	
			} 
			$table=implode(" ", $tabTables);
			if($table!="") {
			}
		}
	}
		
  		if($table!="") {
     		$resultat=sauvegarde_table_distant($user, $mdp, $base, $table);
		
    	//** Téléchargement via ftp du fichier sauvegardé *******************************
     		$NomFichier="sqldump.sql";
     		if(isset($_POST['ftp'])){
     			$Connexion=ftp_connect($host)
        			or die ("Serveur $host non disponible ! \n");
     			ftp_login($Connexion, $LoginDistant, $MotDePasseDistant)
        			or die ("Utilisateur $LoginDistant inconnu ou mot de passe non valide ! \n");
     	
				ftp_put($Connexion, $NomFichier, $NomFichier, FTP_ASCII);
     			ftp_get ($Connexion, $NomFichier, $NomFichier, FTP_ASCII);
     			$ftpclose=ftp_close ($Connexion);
     		}
			
	  	}
	  		if(isset($_POST['ftp'])){
	  			$url_pre="http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];
				$url="http://$host/".$_POST['dossier']."/ecrire/?exec=odb_synchro&type=receveur&url_pre=$url_pre";
				debut_droite();
				debut_boite_info();
				echo OK." - Base locale r&eacute;cuper&eacute;e<br/>Cliquez sur 'Suivant' pour uploader le dump<br/><div align='right'><a href='$url' class='fondo'> Suivant &gt;&gt;&gt;</a></div>";
				fin_boite_info();
				
			}
   }else{
		//Chargement des nouvelles données ************** 
   	mysql_connect($ServeurLocal, $LoginLocal, $MotDePasseLocal)
        	or die ("Connexion impossible &agrave; $ServeurLocal ! \n");
     	mysql_select_db($BaseLocal)
        	or die ("Impossible de s&eacute;lectionner la base $BaseLocal ! \n");
 		$NomFichier="ftp/sqldump.sql"; 
 		chdir("../../../");    
   	`mysql -u $LoginLocal -p$MotDePasseLocal $BaseLocal < $NomFichier`;
	
		$url=$_GET['url_pre'];
		debut_boite_info();
		echo "Synchronisation termin&eacute;e: <A href='$url'>CLiquer ici pour quitter</A>";
		fin_boite_info();
	}
	
	fin_cadre_relief();
	fin_page();
	exit;
}
?>
