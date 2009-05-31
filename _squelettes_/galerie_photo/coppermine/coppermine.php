<?php

// Plugin pour interfacer SPIP 1.9 et la Galerie Photo Coppermine
// http://www.spip.net - http://coppermine.sourceforge.net
//
// Auteur : Philippe Drouot - phil at africacomputing dot org
// (c) 2006 - Distribue sous licence GNU/GPL
//
// Lire documentation.txt pour l'installation du bridge Spip vers Coppermine
// et l'intégration de la galerie photo dans votre site spip
//

// Inclusion du fichier de configuration
require_once(dirname(__FILE__).'/config.inc.php');

// Définition de la balise #COPPERMINE permettant d'intégrer la galerie photo dans un squelette
function balise_COPPERMINE($p) {
	global $coppermineUrl,$coppermineDir;
		
	$coppermineSpipSkel=$_GET['page'];		
		
	$file=$_GET['file'];
	$coppermineUrl="http://".$_SERVER['SERVER_NAME'].$coppermineDir;
	
	// Détermination de l'URL à récupérer pour intégration dans le squelette spip
	$url=$_SERVER['REQUEST_URI'];
	$param=strstr($url,$file);
	$param=substr($param,strlen($file)+1,strlen($param)-(strlen($file)+1));
	if ($file==NULL) $url=$coppermineUrl."index.php";
	else $url=$coppermineUrl."$file.php?".$param;
		
	if ($coppermineDir!="") $p->code = 'fetch_coppermine_url("'.$url.'", "'.$coppermineSpipSkel.'")';
	else die("Vous devez spécifier le répertoire de Coppermine en éditant le fichier ".dirname(__FILE__)."/config.inc.php pour pouvoir utiliser la balise #COPPERMINE");
	
	$p->interdire_scripts = false;
	return $p;
}

// Fonction permettant de récupérer la page concernée de Coppermine et de la mettre en forme
// en vue de l'intégrer dans le squelette spip
function fetch_coppermine_url($url,$squelette) {
	global $coppermineUrl,$coppermineDir;
	
	
	// Hack du fait que coppermine et spip utilisent tous les deux le parametre page
	$url=str_replace("copperminePage=","page=",$url);
		
	// On récupère le contenu de l'url coppermine correspondante 
	require_once(dirname(__FILE__).'/inc/HTTPRequest.php');
	$r = new HTTPRequest($url);	
	$data=$r->DownloadToString();	
	
	// On commence par récupérer le lien vers la feuille de style propre au template coppermine
	$coppermineTemplateCss="";
	if (eregi('<link rel="stylesheet" href="(.*)" type="text/css" />',$data,$regs))
		$coppermineTemplateCss=$coppermineUrl.$regs[1];

	// Suppression de tout ce qui précède <body> ainsi que tout ce qui suit </body>	
	// Ceci afin de supprimer l'entête du template html de coppermine
	$data=eregi_replace("^(.*)<body([^>]*)>","",$data);
	$data=eregi_replace("</body>(.*)$","",$data);	

	// Hack du fait que coppermine et spip utilisent tous les deux le parametre page
	$data=str_replace("page=","copperminePage=",$data);
	
	// Modification de tous les liens fichier.php coppermine pour les transformer en spip.fr?page=&file=fichier
	// Exemple : displayimage.php?album=1&pos=0 -> devient spip.php?page=galerie&album=1&pos=0
	// Rajout de &var_mode=recalcul pour forcer le recalcul : a debugguer !	
	$data=eregi_replace('<a href="([^.^"]*)\.php([^"]*)"','<a href="spip.php?page='.$squelette.'&file=\\1&amp;var_mode=recalcul&amp;\\2"',$data); 	
	$data=eregi_replace('&amp;\?','&amp;',$data);
	
	// Liens javascript et formulaires
	//$data=eregi_replace('<form (.*) action="([^"]*)"','<form \\1 action="'.$coppermineDir.'\\2"',$data); 	
	$data=eregi_replace('<form(.*)action="([^.^"]*)\.php([^>]*)>','<form \\1 action="spip.php?page=galerie&file=\\2&var_mode=recalcul\\3><input type="hidden" name="page" value="galerie" /><input type="hidden" name="file" value="\\2" /><input type="hidden" name="var_mode" value="recalcul" />',$data); 	

	$data=eregi_replace( 'onclick="([^"]*)\'([^.^"]*)\.php([^"]*)"','onclick="\\1\''.$coppermineDir.'\\2.php\\3"',$data); 	
	
	// Cas spécifique des liens accueil
	$data=eregi_replace('&file=index','',$data);
	
	// Cas spécifiques des liens login et logout (pour permettre au bridge spip de fonctionner indépendamment de l'utilisation de la balise #COPPERMINE
	$data=eregi_replace('<a href="([^"]*)file=login([^"]*)','<a href="/spip.php?page=login&url='.urlencode("spip.php?page=$squelette&var_mode=recalcul"),$data); 
	$data=eregi_replace('<a href="([^"]*)file=logout([^"]*)','<a href="/spip.php?action=cookie&logout='.$GLOBALS['auteur_session']['login'].'&url='.urlencode("spip.php?page=$squelette&var_mode=recalcul"),$data); 	
	
	// Modification des liens images
	$data=eregi_replace('<img src="','<img src="'.$coppermineDir,$data);
	$data=eregi_replace('<img src="'.$coppermineDir.'http:','<img src="http:',$data); // pas beau à fusionner avec la règle précédente !
	$data=eregi_replace('<img class="image" src="','<img class="image" src="'.$coppermineDir,$data);
	
	// Hack diaporama
	$data=str_replace("] = 'albums/","] = '".$coppermineDir."albums/",$data);
	$data=str_replace("self.document.location = 'displayimage.php","self.document.location = '".$coppermineDir."displayimage.php",$data);

	// Ajout css + javascript coppermine : pas tres clean car integre dans le body et non le head
	$dataHeader='<link rel="stylesheet" href="'.$coppermineTemplateCss.'" type="text/css" />';
	$dataHeader.='<script type="text/javascript" src="'.$coppermineUrl.'scripts.js"></script>';								
	$data=$dataHeader.$data;
	
	return($data);	
}


// Définition de la balise #COPPERMINE_RANDOM_IMG permettant d'afficher une image aléatoire d'un album public
function balise_COPPERMINE_RANDOM_IMG($p) {			

	$galSkel="";
	$nbVignettes=1;
	
	// Récupération des éventuelles paramètres
	// Pas trés clean la récupération/vérification des paramètres mais j'ai pas
	// encore tout à fait compris ce qu'était sensé contenir l'objet param
	
	$param=$p->param;
	$texte=$param[0][1];
	$texte=$texte[0];
	if ($texte->texte!=NULL) $galSkel=$texte->texte;
		
	$texte2=$param[0][2];
	$texte2=$texte2[0];
	if ($texte2->texte!=NULL)  $nbVignettes=$texte2->texte;
	
	// Execution de la fonction de génération des images aléatoires
	$p->code = 'coppermine_random_img("'.$galSkel.'",'.$nbVignettes.')';
	
	$p->interdire_scripts = false;
	return $p;
}

function coppermine_random_img($galSkel,$nbVignettes) {
	global $coppermineDir;
	
	$coppermineConfigFile="./".$coppermineDir."include/config.inc.php";
		
	if(file_exists($coppermineConfigFile))require_once $coppermineConfigFile;
	else return("");
	
	$CONFIG['TABLE_PICTURES']   = $CONFIG['TABLE_PREFIX'].'pictures';
	$CONFIG['TABLE_CONFIG']     = $CONFIG['TABLE_PREFIX'].'config';
	$CONFIG['TABLE_ALBUMS']     = $CONFIG['TABLE_PREFIX'].'albums';
	
	$CPG_DB_LINK_ID = @mysql_connect($CONFIG['dbserver'], $CONFIG['dbuser'], $CONFIG['dbpass']) or die("Erreur de connexion à la base Coppermine !<br /><br />Message MySQL : <b>" . mysql_error() . "</b>");
	$db_selected = @mysql_select_db($CONFIG['dbname'], $CPG_DB_LINK_ID) or die ('Erreur de connexion à la base Coppermine : ' . mysql_error());
	
	
	// On commence par recuperer les informations de configuration relatives aux vignettes
	$query="SELECT * FROM ".$CONFIG['TABLE_CONFIG']." WHERE (name='fullpath') OR (name='thumb_pfx') OR (name='thumb_width')";
	$results =  mysql_query($query);
	while ($row = mysql_fetch_array($results)) {
	    $CONFIG[$row['name']] = $row['value'];
	} 
	
	// On extrait un album public et non vide au hasard de la base
	$query = "SELECT ".$CONFIG['TABLE_ALBUMS'].".aid, ".$CONFIG['TABLE_ALBUMS'].".title, COUNT(pid) AS total FROM ".$CONFIG['TABLE_ALBUMS'].",".$CONFIG['TABLE_PICTURES'].
					 " WHERE ".$CONFIG['TABLE_ALBUMS'].".aid=".$CONFIG['TABLE_PICTURES'].".aid AND visibility='0'".
					 "GROUP BY ".$CONFIG['TABLE_ALBUMS'].".aid HAVING total>0".
					 " ORDER BY RAND() LIMIT 1";	
	$results =  mysql_query($query);
	$rowA = mysql_fetch_array($results);
	if ($rowA==NULL) return("");

	// Initialisation du code htmeuleuh a retourner
	$data="";
	$lienGal="";
	if ($galSkel!="") $lienGal="spip.php?page=$galSkel&var_mode=recalcul";
	
	// On extrait une image au hasard de l'album sélectionné
	$query = "SELECT * FROM ".$CONFIG['TABLE_PICTURES']." WHERE aid='".$rowA['aid']."' ORDER BY RAND() LIMIT $nbVignettes";
	$results =  mysql_query($query);
	
	
	// Génération du code html d'affichage de la vignette
	while ($row = mysql_fetch_array($results) ) {
		if ($lienGal!="") $data.='<a href="'.$lienGal.'">';
		$legende=addslashes($rowA['title']." - ". $row['title']);
		$data.='<img src="'.$coppermineDir.$CONFIG['fullpath'].$row['filepath'].$CONFIG['thumb_pfx'].$row['filename'].
					'" alt="'.$legende.'" title="'.$legende.'"/ >';
		if ($lienGal!="") $data.='</a>';
	}
	
	
	
	
	return($data);

}
?>
