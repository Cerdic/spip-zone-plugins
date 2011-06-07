<?php

//********************************************************
// Supprime un dossier avec tout son contenu
//*********************************************************
function rmdirr($dossier) {
	@$dir = opendir($dossier);
	while (false !== ($item = readdir($dir))) 
		if ($item!='.' and $item!='..')
		   is_dir($dossier.'/'.$item)? rmdirr($dossier.'/'.$item) : unlink($dossier.'/'.$item);
	@closedir($dir);
  	@rmdir($dossier);
} 

//********************************************************
// Taille d'un dossier
//*********************************************************
function dirsize($dossier) {
	$dir = opendir($dossier);
	$size = 0;
	while (false !== ($item = readdir($dir))) {
		if ($item!='.' and $item!='..') {
			if (is_dir($dossier . '/' . $item)) {
				$size += dirsize($dossier . '/' . $item);
			} else {
				$size += filesize($dossier . '/' . $item);
			}
		}
	}
	closedir($dir);
	return $size;
}

//********************************************************
// Eliminer les caractères interdits
//*********************************************************
function filtrerchaine($str1) { 
   $invalide = array('\\','/',':','*','?','"','<','>','|'); 
   foreach ($invalide as $i) $str1 = strtr($str1, $i, " "); 
   return $str1; 
} 

//********************************************************
// Verifie conformité d'un chaine saisie par l'utilisateur
//*********************************************************
function verifConformite ($chaine,$typeverif) {
	global $lang;
	$msg=false;
	//verifier que le nom d'utilisateur a 4 à 12 caractères
	if ($typeverif=="utilisateur" and (!is_string($chaine) or strlen($chaine)<4 or strlen($chaine)>12)){
		$msg=$lang['erreursaisie1'];
		return $msg;
	}
	//verifier que le mot de passe a 4 à 12 caractères
	if ($typeverif=="motdepasse" and (!is_string($chaine) or strlen($chaine)<4 or strlen($chaine)>12)){
		$msg=$lang['erreursaisie2'];
		return $msg;
	}
	//verifier que le nom a 1 à 20 caractères
	if ($typeverif=="nom" and (!is_string($chaine) or strlen($chaine)<1 or strlen($chaine)>20)){
		$msg=$lang['erreursaisie3'];
		return $msg;
	}
	//verifier que le mot de passe a 4 à 12 caractères
	if ($typeverif=="prenom" and (!is_string($chaine) or strlen($chaine)<1 or strlen($chaine)>20)){
		$msg=$lang['erreursaisie4'];
		return $msg;
	}
	//verifier que le nom de groupe a 1 à 12 caractères
	if ($typeverif=="groupe" and (!is_string($chaine) or strlen($chaine)<1 or strlen($chaine)>12)){
		$msg=$lang['erreursaisie5'];
		return $msg;
	}
	//verifier que le nom de cours a 1 à 12 caractères
	if ($typeverif=="nomcours" and (!is_string($chaine) or strlen($chaine)<1 or strlen($chaine)>12)){
		$msg=$lang['erreurimport6'];
		return $msg;
	}
	//verifier que le titre du cours a 1 à 100 caractères
	if ($typeverif=="titrecours" and (!is_string($chaine) or strlen($chaine)<1 or strlen($chaine)>80)){
		$msg=$lang['erreurimport7'];
		return $msg;
	}
	//verifier absence de char interdits : seuls a-z,A-Z et - sont autorisés
	//sauf pour nom, prenom et titrecours
	if ($typeverif!="titrecours" and $typeverif!="nom" and $typeverif!="prenom"){
		for ($i=0;$i<strlen($chaine);$i++){
			$ok=false;
			if (($chaine[$i]>='A' and $chaine[$i]<='Z') or ($chaine[$i]>='a' and $chaine[$i]<='z')) $ok=true;
			else if (($chaine[$i]>='0' and $chaine[$i]<='9')or($chaine[$i]=='-') ) $ok=true;
			if ($ok==false) {
				$msg=$lang['erreursaisie6'];
				return $msg;
			}
		}
	}	
	return $msg;
}


//********************************************************
// Recuperer la liste des apprenants et des groupes
//*********************************************************
function recuplisteapp (&$apprenants,&$groupes) {
	//recup des données utilisateur dans un tableau apprenants ainsi que liste des groupes
	$dir = @opendir('admin/utilisateurs');
	$i=0;
	while (false !== ($fichier = readdir($dir))) {
		if ($fichier!='.' and $fichier!='..'){
			//recuperer les infos
			$fichier=fopen('admin/utilisateurs/'.$fichier,'r');
			$contenu=fgets($fichier);
			fclose($fichier);
			$tmp=array();
			$tmp=explode(':',$contenu);
			//le format est login:pass:profil:nom:prenom:groupe
			//si $_GET['groupe'] est defini, alors n'afficher que les apprenants de ce groupe
			$afficher=true;
			if (isset($_GET['groupe']))
				if (array_search($_GET['groupe'], explode(',',$tmp[5]))===false)
					$afficher=false;
			//ajouter l'apprenant
			if ($tmp[2]=="apprenant" and $afficher){
				$apprenants[$i]=array();
				$apprenants[$i]=$tmp;
				$i++;
			}
			//créer la liste des groupes 
			if ($tmp[2]=="apprenant")
				if ($tmp[5]!="")
					$groupes=array_merge($groupes,explode(',',$tmp[5])); 
		}
	}
	closedir($dir);
	//ordonner la liste des elements par nom
	$tmp=array();
	foreach($apprenants as $i=>$value) 
		$tmp[$i] = $value[3];
	array_multisort($tmp, $apprenants);
	//eliminer les doublons dans la liste des groupes
	$groupes=array_values(array_unique($groupes));
}

//********************************************************
// Recuperer la liste des cours (0:nom, 1:titre, 2:categorie, 3:taille)
//*********************************************************
function recuplistecours (&$listecours) {

	//les cours sont les dossier du dossier admin/cours/
	$dir = opendir(_DIR_IMG."didapages");
	
	while (false !== ($cours = readdir($dir))) {
		if (($cours!='.' and $cours!='..') and is_dir(_DIR_IMG."didapages/".$cours))
		{
		$titre=$cours;
		$categ="";
		//trouver la taille du cours
		@$taille=round((dirsize(_DIR_IMG."didapages/".$cours)+dirsize("cours/".$cours))/1024); //cette ligne pose problème !
		//enregistrer le nom plus titre
		array_push($listecours,array ($cours,$titre,$categ,$taille));
		}}
	closedir($dir);
}

//********************************************************
// Recuperer la liste des cours en accès libre (0:nom, 1:titre, 2:categorie, 3:taille)
//*********************************************************
function recuplistecourslibre (&$listecours) {
	$listecours2=array();
	recuplistecours($listecours2);
	foreach($listecours2 as $nomcours)
		if (!is_file('admin/cours/'.$nomcours[0].'/blocage'))
			array_push($listecours,$nomcours);
}

//********************************************************
// Recuperer la liste des profs (0:login, 1:motdepasse, 2:nom, 3:prenom)
//*********************************************************
function recuplisteprofs (&$listeprofs) {
	$dir = @opendir('admin/utilisateurs');
	while (false !== ($prof = readdir($dir))) 
		if ($prof!='.' and $prof!='..' and is_file("admin/utilisateurs/".$prof)){
			$fichier=fopen('admin/utilisateurs/'.$prof,'r');
			$contenu=fgets($fichier);
			fclose($fichier);
			$codes=array();
			$codes=explode(':',$contenu);
			if ($codes[2]=="prof") array_push($listeprofs,array ($codes[0],$codes[1],$codes[3],$codes[4]));
		}
	closedir($dir);
}


//********************************************************
// Recuperer la liste inscriptions au cours : 
// 0:nom du cours 1:titre du cours 2 :categorie du travail 3:acces ok
//*********************************************************
function recuplistetravail (&$listetravail, $login) {
	$listecours=array();
	recuplistecours($listecours);
	foreach($listecours as $nomcours)
		if (is_file('admin/travail/'.$login.'/'.$nomcours[0].'.log'))
			if (!is_file('admin/travail/'.$login.'/'.$nomcours[0].'.blo')){
				//categorie
				$categorie="";
				if (is_file('admin/travail/'.$login.'/'.$nomcours[0].'.cat')){
					$fichier=fopen('admin/travail/'.$login.'/'.$nomcours[0].'.cat','r');
					$categorie=fgets($fichier);
					fclose($fichier);
				}
				//fichier tec ()travail en cour - pour verifier qu'un prof n'est pas en train de corriger)
				$accesok=true;
				if (is_file('admin/travail/'.$login.'/'.$nomcours[0].'.tec')){
					$fichier=fopen('admin/travail/'.$login.'/'.$nomcours[0].'.tec','r');
					$contenu=fgets($fichier);
					fclose($fichier);
					$tec=explode(':',$contenu);
					if ($tec[0]!=$login) $accesok=false;
					else $accesok=true;
				} else $accesok=true;
				//enregistrer
				array_push($listetravail,array ($nomcours[0],$nomcours[1],$categorie,$accesok));
			}
}

?>