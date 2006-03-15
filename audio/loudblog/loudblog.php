<?php

function loudblog_parse($loublog_texte,$audioSkel="") {	
	global $settings;
	global $currentid;
	
	// Le contenu de toute cette fonction est un copier-coller de la partie globale
	// du fichier /loudblog/inc/buildwebsite.php sauf section avec commentaires en français
	// (c) Loudblog - http://www.loudblog.de
	
	// --------------------------------------------------------------------
	// IMPORTANT Variables à changer en fonction de la configuration locale
	// --------------------------------------------------------------------
	
	// URL relative du loudblog
	$loudblog_path="/loudblog";	
	
	// Chemin (avec / à la fin) où se trouve physiquement le loudblog 
	// si pas possible avec hébergeur, faire lien(s) sympolique(s)
	// sinon modifier tous les includes de loudblog/inc/* pour faire fonctionner : pas glop !
	ini_set("include_path", ini_get("include_path").":/data/web/org/b/e/africa-web.org/t/s/spiptest/htdocs/loudblog/");	
	// A la windaube : ini_set("include_path", ini_get("include_path").";C:/Web/www/spip-svn/loudblog/"); 	
	
	// --------------------------------------------------------------------
		
	
	include "loudblog/custom/config.php";
	include_once "loudblog/inc/database/adodb.inc.php";
	include_once "loudblog/inc/connect.php";
	include_once "loudblog/inc/functions.php";
		
	if (!isset($db['host'])) { die("<br /><br />Cannot find a valid configuration file! <a href=\"install.php\">Install Loudblog now!</a>"); }
	$GLOBALS['prefix'] = $db['pref'];
	$GLOBALS['path'] = $lb_path;
	$GLOBALS['audiopath'] = $lb_path . "/audio/";
	$GLOBALS['uploadpath'] = $lb_path . "/upload/";
	$GLOBALS['templatepath'] = $lb_path . "/loudblog/custom/templates/";
	
	//getting basic data
	$settings = getsettings();

	dumpdata();
	
	//url
	//get the language translation table
	global $lang;
	$lang = array();
	@include_once($GLOBALS['path']."/loudblog/lang/".$settings['language'].".php");	
	
	include_once "loudblog/inc/loudblogtags.php";
		

	// On fait parser par loudblog	
	$loublog_parsed_texte=fullparse(firstparse(hrefmagic($loublog_texte)));
	
	
	// Hacks pour corriger les chemins dans les liens, qui ne sont plus relatif au répertoire
	// ou se trouve loudblog mais au repertoire de spip (liens en dur dans loudblog).
	$loublog_parsed_texte=str_replace('"loudblog/','"'.$loudblog_path.'/loudblog/',$loublog_parsed_texte);
	$loublog_parsed_texte=str_replace('"audio/','"'.$loudblog_path.'/audio/',$loublog_parsed_texte);
	$loublog_parsed_texte=str_replace('src=audio/','src='.$loudblog_path.'/audio/',$loublog_parsed_texte);
	$loublog_parsed_texte=str_replace('"podcast.php','"'.$loudblog_path.'/podcast.php',$loublog_parsed_texte);
	$loublog_parsed_texte=str_replace('index.php?','spip.php?page='.$audioSkel.'&',$loublog_parsed_texte);
	

return ($loublog_parsed_texte); 
}



function balise_LOUDBLOG($p) {
	
	// Pas trés clean la récupération/vérification des paramètres mais j'ai pas
	// encore tout à fait compris ce qu'était sensé contenir l'objet param
	
	$param=$p->param;
	$texte=$param[0][1];
	$texte=$texte[0];
	$loublog_texte=$texte->texte;	
		
	$texte2=$param[0][2];
	$texte2=$texte2[0];
	$audioSkel=$texte2->texte;
	
	// Second paramètre : squelette spip (spip.php?page=) pour les liens à modifier
	if ($audioSkel==NULL) $audioSkel=substr($_SERVER['REQUEST_URI'],1,strlen($_SERVER['REQUEST_URI'])-1);			
 
 	// Zoup, on envoie au parseur loudblog
	if ($loublog_texte!=NULL) $p->code = 'loudblog_parse("'.addslashes($loublog_texte).'","'.$audioSkel.'")';
	$p->interdire_scripts = true;
	return $p;
}

	// Le contenu de toute ce qui suit est un copier-coller des fonctions
	// du fichier /loudblog/inc/buildwebsite.php 
	// (c) Loudblog - http://www.loudblog.de

function firstparse ($string) {

//very first, we do the loop_postings, because we need some global data for other functions, aight?
$postparsed = parsepostings ($string);

//now we put the posting-parsing-results into the original string
if ((isset($postparsed[0])) AND ($postparsed[0] != false)) {
    foreach ($postparsed as $replace) {
        $string = str_replace($replace['origin'], $replace['parsed'], $string);
    }
}
return $string;
}


//--------------------------------------------------------


function fullparse ($string) {

//we have to look for container-tags and parse them.
$contparsed = parsecontainer ($string);

//now we put the container-parsing-results into the original string
if ((isset($contparsed[0])) AND ($contparsed[0] != false)) {
    foreach ($contparsed as $replace) {
        $string = str_replace($replace['origin'], $replace['parsed'], $string);
    }
}

//secondly, we have to look for single-tags and parse them, too.
$singleparsed = parsesingle ($string);

//now we put the single-parsing-results into the original template
if (isset($singleparsed[0])) {
    foreach ($singleparsed as $replace) {
        $string = str_replace($replace['origin'], $replace['parsed'], $string);
    }
}
return $string;
}


//--------------------------------------------------------------------
function parsepostings ($string) {
//search for postings-tags

$parsing = "";
$search = '|<(lb:loop_postings)[^>]*>.*?</\1>|s';
preg_match_all($search, $string, $matches);
$i = 0;
$parsing = false;
if (isset($matches[0])) {
    foreach ($matches[1] as $containertag) {
        $call = substr ($containertag, 3);
        $parsing[$i]['origin'] = $matches[0][$i];
        $parsing[$i]['parsed'] = call_user_func($call, $matches[0][$i]);
        $i +=1;
    } 
}
return $parsing;
}

//--------------------------------------------------------------------
function parsecontainer ($string) {
//search for container-tags

$parsing = "";
$search = '|<(lb:[_a-z][_a-z0-9]*)[^>]*>.*?</\1>|s';
preg_match_all($search, $string, $matches);
$i = 0;
$parsing = false;
if (isset($matches[0])) {
    foreach ($matches[1] as $containertag) {
        $call = substr ($containertag, 3);
        $parsing[$i]['origin'] = $matches[0][$i];
        $parsing[$i]['parsed'] = call_user_func($call, $matches[0][$i]);
        $i +=1;
    } 
}
return $parsing;
}

//--------------------------------------------------------------------
function stripcontainer ($string) {
//put those "<lb:something>content</lb:something>" tags to trash
if ($string != "") {

    $start = strpos ($string,">") + 1;
    $length= strrpos($string,"<") - strlen($string);
    $string = substr ($string, $start, $length);
}
return $string;
}

//--------------------------------------------------------------------
function parsesingle ($string) {
//search for single-tags

$parsing = "";
if ($string != "") {
    $search = '|<(lb:[_a-z][_a-z0-9]*)[^>]* />|s';
    preg_match_all($search, $string, $matches);
    $i = 0;
    $parsing = false;
    if (isset($matches[0])) {
        foreach ($matches[1] as $singletag) {
            $call = substr ($singletag, 3);
            $parsing[$i]['origin'] = $matches[0][$i];
            $parsing[$i]['parsed'] = call_user_func($call, $matches[0][$i]);
            $i +=1;
        }
    }
} 
return $parsing;
}

//--------------------------------------------------------------------
function getattributes ($string) {
//takes the whole loudblog-tag and returns the attributes as array

$att = array();
if ($string != "") {
    $string = substr($string, 0, strpos($string, ">"));
    $fragments = explode('"', strstr($string, " "));
    for ($i = 0; $i < count($fragments)-1; $i+=2) {
        $att[substr(trim($fragments[$i]), 0, -1)] = $fragments[$i+1];
    } 
}
return $att;
}

//--------------------------------------------------------------------
function hrefmagic ($string) {
//takes all relative href-links and src-links and forward to template-location

$return = false;
if ($string != "") {
    global $settings;
	$search = '#(href|src)=["\']([^/][^:"\']*)["\']#';
	$replace= '$1="loudblog/custom/templates/'.$settings['template'].'/$2"';
    $return = preg_replace ($search, $replace, $string);
}
return $return;
}
?>
