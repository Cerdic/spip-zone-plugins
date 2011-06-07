
<?php

if (!defined('_DIR_PLUGIN_DIDASPIP')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_DIDASPIP',(_DIR_PLUGINS.end($p)));
}
include_spip ("inc/presentation");
function exec_importdidaspip(){
	// vérifier les droits
   global $connect_statut;
   global $connect_toutes_rubriques;
 /*  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
       debut_page(_T('titre'), "saveauto_admin", "plugin");
       echo _T('avis_non_acces_page');
       fin_page();
       exit;
   }
*///charger les chaines de caractères
include("dida_lang.php");
//charger les fonctions
include("dida_fonctions.php");
	//
	// Recupere les donnees
	//

$commencer_page = charger_fonction('commencer_page', 'inc') ;
	echo $commencer_page(_T('importdidaspip'),"", "") ;
	//debut_gauche();


	//////////////////////////////////////////////////////
	// Boite "voir en ligne"
	//
/*
	debut_boite_info();

	echo propre(_T('gestdoc:info_doc'));

	fin_boite_info();
*/
//echo _DIR_PLUGIN_DIDASPIP; 
	global $connect_statut;
	if ($connect_statut != '0minirezo') {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}
	
	
	if (isset($_GET['nom']))
		$_POST["nom"]=$_GET['nom'];
	//verifier les erreurs d'upload
	$erreurmsg=false;
	if ($erreurmsg==false and $_FILES['fichiercours']['error']==UPLOAD_ERR_INI_SIZE) $erreurmsg=$lang["erreurimport1"];
	if ($erreurmsg==false and $_FILES['fichiercours']['error']==UPLOAD_ERR_FORM_SIZE) $erreurmsg=$lang["erreurimport1"];
	if ($erreurmsg==false and $_FILES['fichiercours']['error']==UPLOAD_ERR_PARTIAL) $erreurmsg=$lang["erreurimport2"];
	if ($erreurmsg==false and $_FILES['fichiercours']['error']==UPLOAD_ERR_NO_FILE) $erreurmsg=$lang["erreurimport3"];
	//if ($erreurmsg==false and $_FILES['fichiercours']['error']==UPLOAD_ERR_NO_TMP_DIR) $erreurmsg=$lang["erreurimport4"];
	//au cas où erreru alors que tout s'est bien passé quand même
	if (is_file($_FILES['fichiercours']['tmp_name'])) $erreurmsg=false;
	//verifier que le fichier uploadé est bien un fichier zip
	$extension = explode(".", $_FILES['fichiercours']['name']);
    if ($erreurmsg==false and array_pop($extension)!="zip") $erreurmsg=$lang['erreurimport5'];
	//verifier la conformité des infos saisies
	if ($erreurmsg==false) $erreurmsg=verifConformite($_POST["nom"],"nomcours");
	//if ($erreurmsg==false) $erreurmsg=verifConformite($_POST["titre"],"titrecours");
	//if ($erreurmsg==false) $_POST["titre"]=filtrerchaine($_POST["titre"]);
	//verifier que le nom de cours n'existe pas déjà
	if ($_GET['act']=="installcours" and $erreurmsg==false and is_dir(_DIR_IMG.'/didapages/'.$_POST["nom"]))	$erreurmsg=$lang['erreurimport8'];
	//dezipper le cours dans un dossier tmp d'un dossier a son nom
	if ($erreurmsg==false) {
		if (!is_dir(_DIR_IMG.'didapages/'.$_POST["nom"])) mkdir(_DIR_IMG.'didapages/'.$_POST["nom"]);
		mkdir(_DIR_IMG.'didapages/'.$_POST["nom"].'/tmp');
		//dezipper a l'aide de la (grosse) librairie pclzip
		include_once("pclzip.lib.php");
		$archive = new PclZip($_FILES['fichiercours']['tmp_name']);
		if ($archive->extract(PCLZIP_OPT_PATH, _DIR_IMG."/didapages/".$_POST["nom"].'/tmp')==0) $erreurmsg=$lang['erreurimport9'];
		else if (!is_file(_DIR_IMG."/didapages/".$_POST["nom"]."/tmp/data.xml")){
			//supprimer tout si pas de fichier data.xml (pas un cours didapages)
			 $erreurmsg=$lang['erreurimport5'];
			 rmdirr("admin/cours/".$_POST["nom"]."/tmp");
			 if ($_GET['act']=="installcours") rmdirr(_DIR_IMG."didapages/".$_POST["nom"]);
		}
	}
	//un cours Didapages (exporté pour MSP)) se compose d'un fichier data.xml, 
	//accompagné d'eventuels médias jpg, mp3,swf et flv
	//Le data.xml doit aller dans le dossier /admin/cours qui est protegé
	//les médias doivent aller dans le dossier /cours
	//s'il y a d'autres fichiers, ils doivent être supprimés par sécurité
	if ($erreurmsg==false){
		$dir = @opendir(_DIR_IMG."didapages/".$_POST["nom"]."/tmp");
		$fichentrop="";
		if (!is_dir('../IMG/didapages/'.$_POST["nom"]))  mkdir('../IMG/didapages/'.$_POST["nom"]);
		copy(_DIR_PLUGIN_DIDASPIP."/index.html",_DIR_IMG."didapages/".$_POST["nom"]."/index.html");// Copie du fichier index
		copy(_DIR_PLUGIN_DIDASPIP."/lecteur.swf",_DIR_IMG."didapages/".$_POST["nom"]."/lecteur.swf");// Copie du fichier lecteur flash	
		while (false !== ($fichier = readdir($dir))) {
			if ($fichier=='data.xml'){
				copy(_DIR_IMG."didapages/".$_POST["nom"]."/tmp/".$fichier,_DIR_IMG."didapages/".$_POST["nom"]."/".$fichier);
			} else if ($fichier!='.' and $fichier!='..'){
				$extension = explode(".", $fichier);
				$extension=strtolower(array_pop($extension));
    			if ($extension=="jpg" or $extension=="swf" or $extension=="mp3" or $extension=="flv" or $extension=="html"){
					copy(_DIR_IMG."didapages/".$_POST["nom"]."/tmp/".$fichier,_DIR_IMG."didapages/".$_POST["nom"]."/".$fichier);
				} else {
					$fichentrop.=" ".$fichier;
				}
			}
		}
		closedir($dir);
		//supprimer le dossier temporaire
		rmdirr(_DIR_IMG."didapages/".$_POST["nom"]."/tmp");
		//enregistrer le titre dans un fichier titre
		/*$fichier=fopen('admin/cours/'.$_POST["nom"].'/titre','w');
		$contenu=$_POST["titre"];
		fputs($fichier,$contenu);
		fclose($fichier);*/
		//enregistrer la categorie dans un fichier categorie
		/*$fichier=fopen('admin/cours/'.$_POST["nom"].'/categorie','w');
		if (isset($_POST["textcateg"]))	{
			if (get_magic_quotes_gpc()) fputs($fichier,stripslashes($_POST["textcateg"]));
			else fputs($fichier,$_POST["textcateg"]);
		} else fputs($fichier,'');
		fclose($fichier);*/
		//pas d'accès livre par défaut lors de l'installation
		/*if ($_GET['act']=="installcours"){
			$fichier=fopen('admin/cours/'.$_POST["nom"].'/blocage','w');
			fclose($fichier);
		}*/
		//signaler que des fichiers ont été supprimés
		if ($fichentrop!="") $erreurmsg=$lang['erreurimport10']."(".$fichentrop." )";
	}
	//cas où juste modification du titre du cours ou categ, sans upload
	/*if ($_GET['act']=="modifcourssuite" and $erreurmsg==$lang["erreurimport3"]){
		$erreurmsg=false;
		if ($erreurmsg==false) $erreurmsg=verifConformite($_POST["titre"],"titrecours");
		if ($erreurmsg==false) $_POST["titre"]=filtrerchaine($_POST["titre"]);
		if ($erreurmsg==false) {
			//enregistrer le titre dans un fichier titre
			$fichier=fopen('admin/cours/'.$_POST["nom"].'/titre','w');
			$contenu=$_POST["titre"];
			fputs($fichier,$contenu);
			fclose($fichier);
			//enregistrer la categorie dans un fichier categorie
			$fichier=fopen('admin/cours/'.$_POST["nom"].'/categorie','w');
			if (isset($_POST["textcateg"]))	{
				if (get_magic_quotes_gpc()) fputs($fichier,stripslashes($_POST["textcateg"]));
				else fputs($fichier,$_POST["textcateg"]);
			} else fputs($fichier,'');
			fclose($fichier);
		}
	}
*/
	//si tout s'est bien passé, réafficher la page normale et effacer les champs
	if ($erreurmsg==false) {
		$_GET['act']="menucours";
		unset($_POST["nom"]);
		unset($_POST["nom"]);
		//unset($_POST["titre"]);
	}
	//ouf ! allez hop, on affiche la liste des cours
	include("dida_menu.php");
	include("dida_pagecours.php");



}
?>
